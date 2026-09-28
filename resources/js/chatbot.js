export default function initializeProjectChatbot() {
    const root = document.querySelector('[data-project-chat]');
    if (!root) return;

    const launcher = root.querySelector('[data-chat-open]');
    const panel = root.querySelector('[data-chat-panel]');
    const closeButton = root.querySelector('[data-chat-close]');
    const messages = root.querySelector('[data-chat-messages]');
    const prompts = root.querySelector('[data-chat-prompts]');
    const form = root.querySelector('[data-chat-form]');
    const input = root.querySelector('[data-chat-input]');
    const submit = root.querySelector('[data-chat-submit]');
    const csrfToken = form.querySelector('input[name="_token"]').value;
    let busy = false;

    function setOpen(open) {
        panel.hidden = !open;
        launcher.setAttribute('aria-expanded', String(open));
        if (open) {
            input.focus();
        } else {
            launcher.focus();
        }
    }

    function addMessage(value, role, pending = false) {
        const message = document.createElement('p');
        message.className = 'project-chat__message project-chat__message--' + role;
        if (pending) message.classList.add('project-chat__message--pending');
        message.textContent = value;
        messages.append(message);
        messages.scrollTop = messages.scrollHeight;
        return message;
    }

    async function sendMessage(question) {
        const message = question.trim();
        if (busy || message.length < 2 || message.length > 500) return;

        busy = true;
        submit.disabled = true;
        input.disabled = true;
        prompts.hidden = true;
        input.value = '';
        addMessage(message, 'user');
        const pending = addMessage('Checking the BIS information…', 'assistant', true);

        try {
            const response = await fetch(root.dataset.endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ message }),
            });

            if (response.status === 429) {
                pending.textContent = 'Too many questions for now. Please wait a minute and try again.';
            } else if (response.status === 419) {
                pending.textContent = 'This page has expired. Please refresh it and try again.';
            } else {
                const data = await response.json();
                pending.textContent = typeof data.reply === 'string'
                    ? data.reply
                    : 'I could not answer right now. Please try again later.';
            }
        } catch {
            pending.textContent = 'I could not connect right now. Please check your connection and try again.';
        } finally {
            pending.classList.remove('project-chat__message--pending');
            messages.scrollTop = messages.scrollHeight;
            busy = false;
            submit.disabled = false;
            input.disabled = false;
            if (!panel.hidden) input.focus();
        }
    }

    launcher.addEventListener('click', () => setOpen(panel.hidden));
    closeButton.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !panel.hidden) setOpen(false);
    });
    form.addEventListener('submit', event => {
        event.preventDefault();
        sendMessage(input.value);
    });
    input.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage(input.value);
        }
    });
    prompts.querySelectorAll('[data-chat-prompt]').forEach(button => {
        button.addEventListener('click', () => sendMessage(button.dataset.chatPrompt));
    });
}
