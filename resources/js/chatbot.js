export default function initializeProjectChatbot() {
    const root = document.querySelector('[data-project-chat]');
    if (!root) return;

    const launcher = root.querySelector('[data-chat-open]');
    const panel = root.querySelector('[data-chat-panel]');
    const closeButton = root.querySelector('[data-chat-close]');
    const scrollRegion = root.querySelector('[data-chat-scroll]');
    const messages = root.querySelector('[data-chat-messages]');
    const prompts = root.querySelector('[data-chat-prompts]');
    const form = root.querySelector('[data-chat-form]');
    const input = root.querySelector('[data-chat-input]');
    const submit = root.querySelector('[data-chat-submit]');
    const csrfToken = form.querySelector('input[name="_token"]').value;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let busy = false;
    let closeTimer = null;

    function setOpen(open) {
        if (open) {
            window.clearTimeout(closeTimer);
            panel.classList.remove('is-closing');
            panel.hidden = false;
            launcher.setAttribute('aria-expanded', 'true');
            input.focus();
            return;
        }

        if (panel.hidden || panel.classList.contains('is-closing')) return;

        function finishClose() {
            panel.hidden = true;
            panel.classList.remove('is-closing');
            launcher.setAttribute('aria-expanded', 'false');
            launcher.focus();
        }

        if (reducedMotion.matches) {
            finishClose();
        } else {
            panel.classList.add('is-closing');
            closeTimer = window.setTimeout(finishClose, 170);
        }
    }

    function addMessage(value, role, pending = false) {
        const message = document.createElement('p');
        message.className = 'project-chat__message project-chat__message--' + role;
        if (pending) message.classList.add('project-chat__message--pending');
        message.textContent = value;
        messages.append(message);
        scrollRegion.scrollTop = scrollRegion.scrollHeight;
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
        const pending = addMessage('Sandali, tinitingnan ko…', 'assistant', true);

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
                pending.textContent = 'Medyo sunod-sunod ang tanong. Wait muna ng isang minuto, tapos try ulit.';
            } else if (response.status === 419) {
                pending.textContent = 'Nag-expire ang page. I-refresh muna, tapos try ulit.';
            } else {
                const data = await response.json();
                pending.textContent = typeof data.reply === 'string'
                    ? data.reply
                    : 'Hindi ako makasagot ngayon. Try ulit mamaya.';
            }
        } catch {
            pending.textContent = 'Hindi ako makakonekta ngayon. Check mo ang internet mo, tapos try ulit.';
        } finally {
            pending.classList.remove('project-chat__message--pending');
            scrollRegion.scrollTop = scrollRegion.scrollHeight;
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
