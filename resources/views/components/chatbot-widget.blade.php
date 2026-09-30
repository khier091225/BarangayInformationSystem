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
                <p class="project-chat__message project-chat__message--assistant">Hi! Kumusta? 👋 May tanong ka tungkol sa BIS? Chat mo lang ako tungkol sa registration, requests, reports, o iba pang features nito.</p>
            </div>

            <div class="project-chat__prompts" data-chat-prompts aria-label="Suggested questions">
                <span class="project-chat__prompts-label">Pwede mong itanong</span>
                <button type="button" data-chat-prompt="Paano ako mag-register?">Paano mag-register?</button>
                <button type="button" data-chat-prompt="Anong documents ang pwede kong i-request?">Request ng dokumento</button>
                <button type="button" data-chat-prompt="Paano ko ma-track ang request ko?">I-track ang request</button>
            </div>
        </div>

        <form class="project-chat__form" data-chat-form>
            @csrf
            <label class="sr-only" for="project-chat-message">Tanong mo tungkol sa Barangay Information System</label>
            <textarea id="project-chat-message" name="message" data-chat-input rows="2" maxlength="500" placeholder="Tanong mo lang tungkol sa BIS…" required></textarea>
            <button type="submit" data-chat-submit aria-label="Send message" title="Send message">
                <i data-lucide="arrow-up" aria-hidden="true"></i>
            </button>
        </form>
        <p class="project-chat__note">Tungkol lang sa BIS. Huwag mag-share ng private info.</p>
    </section>
</div>
