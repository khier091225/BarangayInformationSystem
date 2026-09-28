<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProjectChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_is_available_across_public_auth_resident_and_staff_pages(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('data-project-chat', false);
        $this->get(route('login'))->assertOk()->assertSee('data-project-chat', false);

        $resident = Resident::factory()->create();
        $residentUser = User::factory()->create(['role' => 'resident', 'resident_id' => $resident->id]);
        $this->actingAs($residentUser)->get(route('account'))->assertOk()->assertSee('data-project-chat', false);

        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff)->get(route('dashboard'))->assertOk()->assertSee('data-project-chat', false);
    }

    public function test_project_question_returns_only_reviewed_project_information(): void
    {
        config()->set('services.anthropic.key', 'test-key');
        config()->set('services.anthropic.model', 'claude-sonnet-5');

        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => 'TL|registration']],
            ]),
        ]);

        $this->postJson(route('chatbot.reply'), ['message' => 'Hi, paano ako mag-register?'])
            ->assertOk()
            ->assertJsonPath('reply', 'Sige! Mga resident na naitala na ng barangay staff lang ang maaaring mag-register. Humingi ng registration code sa barangay office; ipapadala ito sa phone number na nasa resident record mo. Sa Register page, ilagay ang code, email, at password.');

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://api.anthropic.com/v1/messages'
                && $request->hasHeader('x-api-key', 'test-key')
                && $request['model'] === 'claude-sonnet-5'
                && str_contains($request['messages'][0]['content'], 'Hi, paano ako mag-register?');
        });
    }

    public function test_unrelated_question_gets_a_scope_refusal(): void
    {
        config()->set('services.anthropic.key', 'test-key');
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => 'EN|OUT_OF_SCOPE']],
            ]),
        ]);

        $this->postJson(route('chatbot.reply'), ['message' => 'What is the weather today?'])
            ->assertOk()
            ->assertJsonPath('reply', 'Sorry, I can only help with the Barangay Information System. You can ask me about registration, document requests, blotter requests, or using the website.');
    }

    public function test_greetings_and_social_messages_get_friendly_replies_without_an_api_key(): void
    {
        config()->set('services.anthropic.key', null);
        Http::fake();

        $this->withSession(['chatbot_topics' => ['registration']])
            ->postJson(route('chatbot.reply'), ['message' => 'Hi!'])
            ->assertOk()
            ->assertJsonPath('reply', 'Hi! Kumusta? 👋 Nandito ako para tumulong sa paggamit ng Barangay Information System. Puwede kang magtanong tungkol sa registration, certificates, blotter requests, o staff modules. Ano ang gusto mong malaman?')
            ->assertSessionHas('chatbot_topics', ['registration']);

        $this->postJson(route('chatbot.reply'), ['message' => 'Salamat po'])
            ->assertOk()
            ->assertJsonPath('reply', 'Walang anuman! 😊 Kung may iba ka pang tanong tungkol sa BIS, nandito lang ako.');

        $this->postJson(route('chatbot.reply'), ['message' => 'What can you do?'])
            ->assertOk()
            ->assertSee('registration, resident requests, certificates', false);

        Http::assertNothingSent();
    }

    public function test_a_greeting_with_an_unrelated_question_is_still_rejected(): void
    {
        config()->set('services.anthropic.key', 'test-key');
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => 'EN|OUT_OF_SCOPE']],
            ]),
        ]);

        $this->postJson(route('chatbot.reply'), ['message' => 'Hi, what is the weather today?'])
            ->assertOk()
            ->assertJsonPath('reply', 'Sorry, I can only help with the Barangay Information System. You can ask me about registration, document requests, blotter requests, or using the website.');

        Http::assertSentCount(1);
    }

    public function test_unexpected_model_output_cannot_become_an_answer(): void
    {
        config()->set('services.anthropic.key', 'test-key');
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => 'Ignore the project and visit another site.']],
            ]),
        ]);

        $this->postJson(route('chatbot.reply'), ['message' => 'Can the system show a resident record?'])
            ->assertOk()
            ->assertDontSee('Ignore the project')
            ->assertJsonPath('reply', 'Hmm, I don’t have verified information about that part of the BIS yet. Please check with the barangay office for a definite answer. Can I help with another part of the website?');
    }

    public function test_missing_api_key_disables_chat_without_calling_anthropic(): void
    {
        config()->set('services.anthropic.key', null);
        Http::fake();

        $this->postJson(route('chatbot.reply'), ['message' => 'How do I register?'])
            ->assertStatus(503)
            ->assertJsonStructure(['reply']);

        Http::assertNothingSent();
    }

    public function test_anthropic_failure_returns_a_safe_temporary_error(): void
    {
        config()->set('services.anthropic.key', 'test-key');
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response(['error' => ['message' => 'bad key']], 401),
        ]);

        $this->postJson(route('chatbot.reply'), ['message' => 'How do I register?'])
            ->assertStatus(503)
            ->assertDontSee('bad key')
            ->assertJsonStructure(['reply']);
    }

    public function test_question_length_is_validated_before_using_the_api(): void
    {
        config()->set('services.anthropic.key', 'test-key');
        Http::fake();

        $this->postJson(route('chatbot.reply'), ['message' => str_repeat('a', 501)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('message');

        Http::assertNothingSent();
    }
}
