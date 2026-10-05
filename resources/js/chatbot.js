export default function initializeProjectChatbot() {
    const root = document.querySelector('[data-project-chat]');
    if (!root) return;

    const launcher = root.querySelector('[data-chat-open]');
    const panel = root.querySelector('[data-chat-panel]');
    const closeButton = root.querySelector('[data-chat-close]');
    const scrollRegion = root.querySelector('[data-chat-scroll]');
    const messages = root.querySelector('[data-chat-messages]');
    const prompts = root.querySelector('[data-chat-prompts]');
    const latestButton = root.querySelector('[data-chat-latest]');
    const form = root.querySelector('[data-chat-form]');
    const input = root.querySelector('[data-chat-input]');
    const submit = root.querySelector('[data-chat-submit]');
    const feedback = root.querySelector('[data-chat-feedback]');
    const counter = root.querySelector('[data-chat-count]');
    const csrfToken = form.querySelector('input[name="_token"]').value;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const compactViewport = window.matchMedia('(max-width: 520px)');
    let busy = false;
    let closeTimer = null;

    function updateViewport() {
        const viewport = window.visualViewport;
        const viewportHeight = viewport?.height ?? window.innerHeight;
        const keyboardInset = panel.hidden ? 0 : Math.max(0, window.innerHeight - viewportHeight - (viewport?.offsetTop ?? 0));
        root.style.setProperty('--chat-viewport-height', `${viewportHeight}px`);
        root.style.setProperty('--chat-keyboard-inset', `${keyboardInset}px`);
    }

    function updateComposer() {
        input.style.height = 'auto';
        input.style.height = `${Math.min(input.scrollHeight, 112)}px`;
        counter.textContent = `${input.value.length}/500`;
        const messageLength = input.value.trim().length;
        submit.disabled = busy || messageLength < 2 || messageLength > 500;
        submit.setAttribute('aria-label', busy ? 'Naghihintay ng sagot' : 'Send message');
        form.setAttribute('aria-busy', String(busy));
        root.querySelectorAll('[data-chat-retry]').forEach(button => button.disabled = busy);
    }

    function clearFeedback() {
        feedback.hidden = true;
        feedback.textContent = '';
        input.removeAttribute('aria-invalid');
        input.setAttribute('aria-describedby', 'project-chat-help project-chat-note');
    }

    function isNearLatest() {
        return scrollRegion.scrollHeight - scrollRegion.clientHeight - scrollRegion.scrollTop < 64;
    }

    function updateLatestButton() {
        latestButton.hidden = panel.hidden || messages.children.length < 2 || isNearLatest();
    }

    function scrollToLatest(smooth = false) {
        scrollRegion.scrollTo({
            top: scrollRegion.scrollHeight,
            behavior: smooth && !reducedMotion.matches ? 'smooth' : 'auto',
        });
        updateLatestButton();
    }

    function setOpen(open) {
        if (open) {
            if (root.inert) return;
            window.clearTimeout(closeTimer);
            panel.classList.remove('is-closing');
            panel.hidden = false;
            launcher.setAttribute('aria-expanded', 'true');
            updateViewport();
            updateComposer();
            (compactViewport.matches ? closeButton : input).focus({ preventScroll: true });
            requestAnimationFrame(() => {
                if (messages.children.length > 1) scrollToLatest();
                else updateLatestButton();
            });
            return;
        }

        if (panel.hidden || panel.classList.contains('is-closing')) return;

        function finishClose() {
            panel.hidden = true;
            panel.classList.remove('is-closing');
            launcher.setAttribute('aria-expanded', 'false');
            updateViewport();
            if (!root.inert) launcher.focus({ preventScroll: true });
        }

        if (reducedMotion.matches) {
            finishClose();
        } else {
            panel.classList.add('is-closing');
            closeTimer = window.setTimeout(finishClose, 170);
        }
    }

    function setPending(message) {
        message.className = 'project-chat__message project-chat__message--assistant project-chat__message--pending';
        message.textContent = 'Tinitingnan ko…';
        const dots = document.createElement('span');
        dots.className = 'project-chat__typing';
        dots.setAttribute('aria-hidden', 'true');
        for (let index = 0; index < 3; index++) dots.append(document.createElement('span'));
        message.append(dots);
        message.parentElement.querySelector('.project-chat__message-label').textContent = 'BIS Assistant';
        message.parentElement.querySelector('[data-chat-retry]')?.remove();
    }

    function addMessage(value, role, pending = false) {
        const entry = document.createElement('div');
        entry.className = 'project-chat__entry project-chat__entry--' + role;
        const label = document.createElement('span');
        label.className = 'project-chat__message-label';
        label.textContent = role === 'user' ? 'Ikaw' : 'BIS Assistant';
        const message = document.createElement('p');
        message.className = 'project-chat__message project-chat__message--' + role;
        message.textContent = value;
        entry.append(label, message);
        messages.append(entry);
        if (pending) setPending(message);
        scrollToLatest();
        return message;
    }

    function showFailure(message, explanation, question, refresh = false) {
        message.textContent = explanation;
        message.classList.add('project-chat__message--error');
        message.parentElement.querySelector('.project-chat__message-label').textContent = 'Hindi nakakuha ng sagot';
        const retry = document.createElement('button');
        retry.type = 'button';
        retry.className = 'project-chat__retry';
        retry.dataset.chatRetry = '';
        retry.textContent = refresh ? 'Refresh page' : 'Subukan ulit';
        retry.addEventListener('click', () => refresh ? window.location.reload() : sendMessage(question, message));
        message.parentElement.append(retry);
    }

    async function sendMessage(question, retryMessage = null) {
        const message = question.trim();
        if (busy || root.inert) return;
        if (message.length < 2 || message.length > 500) {
            feedback.textContent = message.length < 2
                ? 'Dagdagan pa nang kaunti ang tanong mo (minimum 2 characters).'
                : 'Hanggang 500 characters lang bawat tanong.';
            feedback.hidden = false;
            input.setAttribute('aria-invalid', 'true');
            input.setAttribute('aria-describedby', 'project-chat-help project-chat-note project-chat-feedback');
            return;
        }

        clearFeedback();
        busy = true;
        prompts.hidden = true;
        let pending = retryMessage;
        if (pending) {
            if (compactViewport.matches) closeButton.focus({ preventScroll: true });
            setPending(pending);
        } else {
            input.value = '';
            addMessage(message, 'user');
            pending = addMessage('', 'assistant', true);
        }
        updateComposer();
        if (!compactViewport.matches) input.focus({ preventScroll: true });
        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 35000);
        let followLatest = true;

        try {
            const response = await fetch(root.dataset.endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ message }),
            });

            followLatest = isNearLatest();
            if (response.status === 429) {
                showFailure(pending, 'Medyo sunod-sunod ang tanong. Wait muna ng isang minuto, tapos subukan ulit.', message);
            } else if (response.status === 419) {
                showFailure(pending, 'Nag-expire ang page. I-refresh muna para makapag-chat ulit.', message, true);
            } else {
                const data = await response.json();
                followLatest = isNearLatest();
                if (!response.ok || typeof data.reply !== 'string' || data.reply.trim() === '') {
                    const explanation = typeof data.reply === 'string' && data.reply.trim() !== ''
                        ? data.reply
                        : 'Hindi ko makuha ang sagot ngayon. Subukan ulit mamaya.';
                    showFailure(pending, explanation, message);
                } else {
                    pending.textContent = data.reply;
                }
            }
        } catch (error) {
            followLatest = isNearLatest();
            showFailure(pending, error.name === 'AbortError'
                ? 'Medyo matagal ang sagot ngayon. Subukan ulit mamaya.'
                : 'Hindi ko makuha ang sagot ngayon. Check ang connection mo, tapos subukan ulit.', message);
        } finally {
            window.clearTimeout(timeout);
            pending.classList.remove('project-chat__message--pending');
            busy = false;
            updateComposer();
            if (followLatest) scrollToLatest();
            else updateLatestButton();
        }
    }

    launcher.addEventListener('click', () => setOpen(panel.hidden));
    closeButton.addEventListener('click', () => setOpen(false));
    latestButton.addEventListener('click', () => scrollToLatest(true));
    scrollRegion.addEventListener('scroll', updateLatestButton, { passive: true });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !event.defaultPrevented && !root.inert && !panel.hidden) {
            event.preventDefault();
            setOpen(false);
        }
    });
    form.addEventListener('submit', event => {
        event.preventDefault();
        sendMessage(input.value);
    });
    input.addEventListener('input', () => {
        clearFeedback();
        updateComposer();
    });
    input.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey && !event.isComposing && !compactViewport.matches) {
            event.preventDefault();
            sendMessage(input.value);
        }
    });
    prompts.querySelectorAll('[data-chat-prompt]').forEach(button => {
        button.addEventListener('click', () => {
            if (compactViewport.matches) closeButton.focus({ preventScroll: true });
            sendMessage(button.dataset.chatPrompt);
        });
    });
    window.addEventListener('resize', () => {
        updateViewport();
        if (!panel.hidden) updateComposer();
        updateLatestButton();
    });
    window.visualViewport?.addEventListener('resize', updateViewport);
    window.visualViewport?.addEventListener('scroll', updateViewport);
    updateViewport();
    updateComposer();
}
