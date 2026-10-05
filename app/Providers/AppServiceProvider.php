<?php

namespace App\Providers;

use App\Http\ProjectChatbot;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.chatbot-widget', function (ViewInstance $view): void {
            $history = request()->session()->get('chatbot_history', []);
            $view->with('chatHistory', app(ProjectChatbot::class)->recentHistory(is_array($history) ? $history : []));
            $view->with('chatHistoryLimit', ProjectChatbot::HISTORY_LIMIT);
        });

        ResetPassword::toMailUsing(function (User $user, string $token): MailMessage {
            return (new MailMessage)
                ->subject('Reset your password | Barangay Kay-Anlog')
                ->view([
                    'html' => 'emails.reset-password',
                    'text' => 'emails.reset-password-text',
                ], [
                    'name' => $user->name,
                    'resetUrl' => route('password.reset', [
                        'token' => $token,
                        'email' => $user->getEmailForPasswordReset(),
                    ]),
                    'expiresIn' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
                ]);
        });
    }
}
