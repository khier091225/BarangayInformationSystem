<div class="project-chat" data-project-chat data-endpoint="{{ route('chatbot.reply') }}">
    <button class="project-chat__launcher" type="button" data-chat-open aria-expanded="false" aria-controls="project-chat-panel" aria-label="Open BIS Assistant">
        <span class="project-chat__launcher-icon"><i data-lucide="messages-square" aria-hidden="true"></i></span>
        <span class="project-chat__launcher-copy">
            <strong>Ask BIS</strong>
            <small>Here to help</small>
        </span>
    </button>

    <section class="project-chat__panel" id="project-chat-panel" data-chat-panel aria-labelledby="project-chat-title" hidden>
        <header class="project-chat__header">
            <span class="project-chat__symbol"><i data-lucide="messages-square" aria-hidden="true"></i></span>
            <span class="project-chat__heading">
                <strong id="project-chat-title">BIS Assistant</strong>
                <small>Barangay Kay-Anlog help</small>
            </span>
            <button class="project-chat__close" type="button" data-chat-close aria-label="Close BIS Assistant">
                <i data-lucide="x" aria-hidden="true"></i>
            </button>
        </header>

        <div class="project-chat__conversation" data-chat-scroll>
            <div class="project-chat__messages" data-chat-messages role="log" aria-label="Chat messages" aria-live="polite" aria-relevant="additions text">
                <p class="project-chat__message project-chat__message--assistant">Hi! Kumusta? 👋 Ask me about registration, document requests, incident reports, blotter requests, or using the Barangay Information System.</p>
            </div>

            <div class="project-chat__prompts" data-chat-prompts aria-label="Suggested questions">
                <span class="project-chat__prompts-label">You can ask</span>
                <button type="button" data-chat-prompt="How do I register?">How do I register?</button>
                <button type="button" data-chat-prompt="What documents can I request?">Request documents</button>
                <button type="button" data-chat-prompt="How do I track my request?">Track a request</button>
            </div>
        </div>

        <form class="project-chat__form" data-chat-form>
            @csrf
            <label class="sr-only" for="project-chat-message">Your question about the Barangay Information System</label>
            <textarea id="project-chat-message" name="message" data-chat-input rows="2" maxlength="500" placeholder="Ask a question about the BIS…" required></textarea>
            <button type="submit" data-chat-submit aria-label="Send message" title="Send message">
                <i data-lucide="arrow-up" aria-hidden="true"></i>
            </button>
        </form>
        <p class="project-chat__note">Project help only. Please do not share private information.</p>
    </section>
</div>
