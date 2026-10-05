@php
    $suggestions = match (auth()->user()?->role) {
        'staff' => [
            ['label' => 'Registration code', 'question' => 'Paano gumawa ng registration code para sa resident?', 'icon' => 'user-plus'],
            ['label' => 'Mag-review ng request', 'question' => 'Paano mag-review ng document request ng resident?', 'icon' => 'inbox'],
            ['label' => 'Mag-issue ng certificate', 'question' => 'Paano mag-issue ng barangay certificate?', 'icon' => 'file-check-2'],
        ],
        'resident' => [
            ['label' => 'Request ng dokumento', 'question' => 'Anong documents ang pwede kong i-request?', 'icon' => 'files'],
            ['label' => 'I-track ang request', 'question' => 'Paano ko ma-track ang request ko?', 'icon' => 'history'],
            ['label' => 'Mag-report ng incident', 'question' => 'Paano ako mag-report ng incident?', 'icon' => 'flag'],
        ],
        default => [
            ['label' => 'Paano mag-register?', 'question' => 'Paano ako mag-register?', 'icon' => 'user-plus'],
            ['label' => 'Request ng dokumento', 'question' => 'Anong documents ang pwede kong i-request?', 'icon' => 'files'],
            ['label' => 'I-track ang request', 'question' => 'Paano ko ma-track ang request ko?', 'icon' => 'history'],
        ],
    };
@endphp

<div class="project-chat" data-project-chat data-endpoint="{{ route('chatbot.reply') }}" lang="fil">
    <button class="project-chat__launcher" type="button" data-chat-open aria-expanded="false" aria-controls="project-chat-panel" aria-label="Open BIS Assistant">
        <span class="project-chat__launcher-icon"><i data-lucide="messages-square" aria-hidden="true"></i></span>
        <span class="project-chat__launcher-copy">
            <strong>Ask BIS</strong>
            <small>May tanong ka?</small>
        </span>
    </button>

    <section class="project-chat__panel" id="project-chat-panel" data-chat-panel role="dialog" aria-labelledby="project-chat-title" aria-describedby="project-chat-description" hidden>
        <header class="project-chat__header">
            <x-brand-seal class="project-chat__seal" />
            <span class="project-chat__heading">
                <strong id="project-chat-title">BIS Assistant</strong>
                <small id="project-chat-description">Tulong sa paggamit ng BIS</small>
            </span>
            <button class="project-chat__close" type="button" data-chat-close aria-label="Close BIS Assistant">
                <i data-lucide="x" aria-hidden="true"></i>
            </button>
        </header>

        <div class="project-chat__body">
            <div class="project-chat__conversation" data-chat-scroll tabindex="0" role="region" aria-label="Pag-uusap sa BIS Assistant">
                <div class="project-chat__messages" data-chat-messages role="log" aria-label="Chat messages" aria-live="polite" aria-relevant="additions text">
                    <div class="project-chat__entry project-chat__entry--assistant">
                        <span class="project-chat__message-label">BIS Assistant</span>
                        <p class="project-chat__message project-chat__message--assistant"><strong>Hi! Kumusta? 👋</strong><span>May tanong ka tungkol sa BIS? Pili ka sa ibaba o i-type lang ang tanong mo.</span></p>
                    </div>
                </div>

                <div class="project-chat__prompts" data-chat-prompts aria-label="Suggested questions">
                    <span class="project-chat__prompts-label">Pwede mong itanong</span>
                    @foreach ($suggestions as $suggestion)
                        <button type="button" data-chat-prompt="{{ $suggestion['question'] }}"><i data-lucide="{{ $suggestion['icon'] }}" aria-hidden="true"></i><span>{{ $suggestion['label'] }}</span><i data-lucide="chevron-right" aria-hidden="true"></i></button>
                    @endforeach
                </div>
            </div>
            <button class="project-chat__latest" type="button" data-chat-latest hidden>Pinakabagong mensahe <i data-lucide="chevron-down" aria-hidden="true"></i></button>
        </div>

        <div class="project-chat__composer">
            <p class="project-chat__feedback" id="project-chat-feedback" data-chat-feedback role="alert" hidden></p>
            <form class="project-chat__form" data-chat-form>
                @csrf
                <label class="sr-only" for="project-chat-message">Tanong mo tungkol sa Barangay Information System</label>
                <textarea id="project-chat-message" name="message" data-chat-input rows="1" minlength="2" maxlength="500" aria-describedby="project-chat-help project-chat-note" placeholder="Ano ang tanong mo?" required></textarea>
                <button type="submit" data-chat-submit aria-label="Send message" title="Send message" disabled>
                    <i data-lucide="arrow-up" aria-hidden="true"></i>
                    <span class="project-chat__send-spinner" aria-hidden="true"></span>
                </button>
            </form>
            <div class="project-chat__composer-help">
                <p id="project-chat-help"><span class="project-chat__keyboard-help">Enter para mag-send · Shift+Enter para sa bagong linya.</span><span class="project-chat__mobile-help">Hanggang 500 characters bawat tanong.</span></p>
                <span data-chat-count aria-hidden="true">0/500</span>
            </div>
            <p class="project-chat__note" id="project-chat-note"><i data-lucide="shield-check" aria-hidden="true"></i><span>Para sa BIS lang. Huwag ilagay ang password o registration code.</span></p>
        </div>
    </section>
</div>
