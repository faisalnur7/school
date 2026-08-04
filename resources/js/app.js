import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const THEME_STORAGE_KEY = 'school-theme';

function normalizeTheme(theme) {
    return theme === 'dark' ? 'dark' : 'light';
}

function getPreferredTheme() {
    try {
        const storedTheme = window.localStorage.getItem(THEME_STORAGE_KEY);
        if (storedTheme) {
            return normalizeTheme(storedTheme);
        }
    } catch (error) {
        // Ignore storage errors and fall back to system preference.
    }

    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light';
}

function applyTheme(theme) {
    const nextTheme = normalizeTheme(theme);
    const root = document.documentElement;

    root.dataset.theme = nextTheme;
    root.classList.toggle('dark', nextTheme === 'dark');
    root.style.colorScheme = nextTheme;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        updateThemeToggleButton(button, nextTheme);
    });
}

function updateThemeToggleButton(button, activeTheme) {
    const nextTheme = activeTheme === 'dark' ? 'light' : 'dark';
    const nextThemeLabel = nextTheme === 'dark' ? 'Dark' : 'Light';

    button.setAttribute('aria-pressed', activeTheme === 'dark' ? 'true' : 'false');
    button.setAttribute('aria-label', `Switch to ${nextThemeLabel.toLowerCase()} mode`);
    button.setAttribute('title', `Switch to ${nextThemeLabel.toLowerCase()} mode`);
    button.dataset.activeTheme = activeTheme;
    button.dataset.nextTheme = nextTheme;

    const label = button.querySelector('[data-theme-toggle-label]');

    if (label) {
        label.textContent = nextThemeLabel;
    }
}

function setTheme(theme, persist = true) {
    const nextTheme = normalizeTheme(theme);

    if (persist) {
        try {
            window.localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
        } catch (error) {
            // Ignore storage errors and still apply the requested theme.
        }
    }

    applyTheme(nextTheme);
}

window.setThemeMode = setTheme;
window.toggleThemeMode = function toggleThemeMode() {
    const currentTheme = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
    setTheme(currentTheme === 'dark' ? 'light' : 'dark');
};

function syncThemeButtons() {
    const currentTheme = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        updateThemeToggleButton(button, currentTheme);
    });
}

applyTheme(getPreferredTheme());
syncThemeButtons();

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-theme-toggle]');

    if (!toggle) {
        return;
    }

    event.preventDefault();
    window.toggleThemeMode();
});

const colorSchemeQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

if (colorSchemeQuery && colorSchemeQuery.addEventListener) {
    colorSchemeQuery.addEventListener('change', (event) => {
        try {
            if (!window.localStorage.getItem(THEME_STORAGE_KEY)) {
                setTheme(event.matches ? 'dark' : 'light', false);
            }
        } catch (error) {
            setTheme(event.matches ? 'dark' : 'light', false);
        }
    });
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function formatChatTime(value) {
    if (!value) {
        return '';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return new Intl.DateTimeFormat(undefined, {
        hour: 'numeric',
        minute: '2-digit',
    }).format(date);
}

function formatChatDate(value) {
    if (!value) {
        return '';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    }).format(date);
}

function formatRelativeChatTime(value) {
    if (!value) {
        return 'Just now';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return 'Just now';
    }

    const diffSeconds = Math.max(0, Math.floor((Date.now() - date.getTime()) / 1000));

    if (diffSeconds < 60) {
        return diffSeconds <= 1 ? 'Just now' : `${diffSeconds} secs ago`;
    }

    const diffMinutes = Math.floor(diffSeconds / 60);

    if (diffMinutes < 60) {
        return diffMinutes === 1 ? '1 min ago' : `${diffMinutes} mins ago`;
    }

    const diffHours = Math.floor(diffMinutes / 60);

    if (diffHours < 24) {
        return diffHours === 1 ? '1 hr ago' : `${diffHours} hrs ago`;
    }

    const diffDays = Math.floor(diffHours / 24);

    return diffDays === 1 ? '1 day ago' : `${diffDays} days ago`;
}

const CHAT_REACTION_OPTIONS = [
    { value: 'like', emoji: '👍', label: 'Like' },
    { value: 'love', emoji: '❤️', label: 'Love' },
    { value: 'haha', emoji: '😂', label: 'Haha' },
    { value: 'wow', emoji: '😮', label: 'Wow' },
    { value: 'sad', emoji: '😢', label: 'Sad' },
    { value: 'angry', emoji: '😡', label: 'Angry' },
];

function getChatReactionOption(value) {
    return CHAT_REACTION_OPTIONS.find((option) => option.value === value) || null;
}

function renderChatReactionSummary(message) {
    const summary = Array.isArray(message?.reactions?.summary) ? message.reactions.summary : [];

    if (!summary.length) {
        return '';
    }

    return `
        <div class="chat-message-reactions" data-chat-message-reactions>
            ${summary.map((item) => {
                const option = getChatReactionOption(item.reaction);
                const count = Number(item.count || 0);
                if (!option || count <= 0) {
                    return '';
                }

                const isMine = message?.reactions?.mine === item.reaction;

                return `
                    <button type="button" class="chat-message-reaction-chip ${isMine ? 'is-mine' : ''}" data-chat-message-react-toggle="${escapeHtml(item.reaction)}" aria-label="${escapeHtml(option.label)}">
                        <span>${escapeHtml(option.emoji)}</span>
                        <span>${escapeHtml(count)}</span>
                    </button>
                `;
            }).join('')}
        </div>
    `;
}

function renderChatReactionMenu(message) {
    if (message?.pending) {
        return '';
    }

    const currentReaction = message?.reactions?.mine || '';

    return `
        <div class="chat-message-reaction-menu" data-chat-message-reaction-menu>
            ${CHAT_REACTION_OPTIONS.map((option) => `
                <button
                    type="button"
                    class="chat-message-reaction-option ${currentReaction === option.value ? 'is-active' : ''}"
                    data-chat-message-react-option="${escapeHtml(option.value)}"
                    aria-label="${escapeHtml(option.label)}"
                    title="${escapeHtml(option.label)}"
                >
                    <span>${escapeHtml(option.emoji)}</span>
                </button>
            `).join('')}
        </div>
    `;
}

function renderChatMessageOptionsMenu(message) {
    if (message?.pending || !message?.mine) {
        return '';
    }

    return `
        <div class="chat-message-options-menu" data-chat-message-options-menu>
            <button type="button" class="chat-message-options-item" data-chat-message-edit-button>
                <i class="fas fa-pen"></i>
                <span>Edit</span>
            </button>
            <button type="button" class="chat-message-options-item is-danger" data-chat-message-delete-button>
                <i class="fas fa-trash"></i>
                <span>Delete</span>
            </button>
        </div>
    `;
}

function renderChatMessage(message) {
    if (message.is_system) {
        return `
            <div class="chat-message-system" ${message.pending ? 'data-chat-message-pending="true"' : `data-chat-message-id="${escapeHtml(message.id)}"`}>
                <span>${escapeHtml(message.body || 'Conversation update')}</span>
            </div>
        `;
    }

    const senderName = message.sender?.name || 'User';
    const senderImage = message.sender?.image_url || '';
    const bubbleClass = `${message.mine ? 'chat-message-bubble chat-message-bubble--mine' : 'chat-message-bubble'}${message.pending ? ' chat-message-bubble--pending' : ''}`;
    const attachment = message.attachment
        ? message.attachment.missing
            ? `
                <div class="chat-message-file chat-message-file--missing">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Attachment unavailable</span>
                </div>
            `
            : message.attachment.is_image
            ? `
                <a class="chat-message-image-link" href="${escapeHtml(message.attachment.url)}" target="_blank" rel="noreferrer">
                    <img src="${escapeHtml(message.attachment.url)}" alt="${escapeHtml(message.attachment.name)}">
                </a>
            `
            : `
                <a class="chat-message-file" href="${escapeHtml(message.attachment.url)}" target="_blank" rel="noreferrer">
                    <i class="fas fa-file-image"></i>
                    <span>${escapeHtml(message.attachment.name)}</span>
                </a>
            `
        : '';
    const idAttribute = message.pending
        ? `data-chat-message-pending-id="${escapeHtml(message.id)}"`
        : `data-chat-message-id="${escapeHtml(message.id)}"`;
    const reactionsMarkup = renderChatReactionSummary(message);
    const reactionMenuMarkup = renderChatReactionMenu(message);
    const optionsMenuMarkup = renderChatMessageOptionsMenu(message);
    const moreButtonMarkup = message.mine
        ? `
            <button type="button" class="chat-message-action-button chat-message-more-button" data-chat-message-more-button aria-label="More options" title="More options">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        `
        : '';

    return `
        <article class="chat-message ${message.mine ? 'chat-message--mine' : ''}${message.pending ? ' chat-message--pending' : ''}" ${idAttribute} data-chat-message-body="${escapeHtml(message.body || '')}">
            ${message.mine ? '' : `
                <img class="chat-message-avatar" src="${escapeHtml(senderImage)}" alt="${escapeHtml(senderName)}">
            `}
            <div class="chat-message-content">
                ${message.mine ? '' : `<div class="chat-message-meta">${escapeHtml(senderName)}</div>`}
                <div class="chat-message-row">
                    <div class="chat-message-actions">
                        ${reactionMenuMarkup}
                        ${optionsMenuMarkup}
                        <button type="button" class="chat-message-action-button chat-message-react-button" data-chat-message-react-button aria-label="React to message" title="React to message">
                            <i class="far fa-smile"></i>
                        </button>
                        ${moreButtonMarkup}
                    </div>
                    <div class="${bubbleClass}" data-chat-message-bubble>
                        ${message.body ? `<div class="chat-message-text">${escapeHtml(message.body)}</div>` : ''}
                        ${attachment}
                        ${reactionsMarkup}
                    </div>
                </div>
                <div class="chat-message-time">${escapeHtml(message.pending ? 'Sending...' : formatChatTime(message.created_at))}</div>
            </div>
        </article>
    `;
}

function buildChatMessageNode(message) {
    if (!message) {
        return null;
    }
    const wrapper = document.createElement('div');
    wrapper.innerHTML = renderChatMessage(message).trim();
    return wrapper.firstElementChild;
}

function renderChatThread(threadEl, messages, emptyStateHtml) {
    if (!threadEl) {
        return;
    }

    if (!messages || messages.length === 0) {
        threadEl.innerHTML = emptyStateHtml;
        return;
    }

    const dateFormatter = new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
    let lastDate = '';
    const html = [];

    messages.forEach((message) => {
        const currentDate = message.created_at ? dateFormatter.format(new Date(message.created_at)) : '';
        if (currentDate && currentDate !== lastDate) {
            html.push(`<div class="chat-date-divider">${escapeHtml(currentDate)}</div>`);
            lastDate = currentDate;
        }

        html.push(renderChatMessage(message));
    });

    threadEl.innerHTML = html.join('');
    threadEl.dataset.chatLastMessageId = messages.length ? String(messages[messages.length - 1].id || '') : '';
    scrollChatThreadToBottom(threadEl);
}

function renderConversationCard(conversation, isActive = false) {
    const isGroup = !!conversation.is_group;
    const unreadCount = Number(conversation.unread_count || 0);
    const unreadClass = unreadCount > 0 ? 'conversation-card--unread' : '';
    const unreadBadge = unreadCount > 0
        ? `<span class="conversation-card__count">${escapeHtml(unreadCount)}</span>`
        : '';

    return `
        <a
            href="/communications?conversation=${encodeURIComponent(conversation.id)}"
            class="conversation-card ${isActive ? 'is-active' : ''} ${unreadClass}"
            data-communications-item
            data-chat-conversation-link
            data-chat-conversation-id="${escapeHtml(conversation.id)}"
            data-chat-conversation-url="/communications/conversations/${encodeURIComponent(conversation.id)}"
            data-communications-text="${escapeHtml((conversation.search_text || `${conversation.name || ''} ${conversation.subtitle || ''} ${conversation.last_message_preview || ''}`).toLowerCase())}"
        >
            <span class="conversation-card__avatar ${isGroup ? '' : 'chat-conversation-avatar--muted'}">${escapeHtml(conversation.avatar_label || 'C')}</span>
            <span class="conversation-card__copy">
                <span class="conversation-card__top">
                    <span class="text-truncate">${escapeHtml(conversation.name || 'Conversation')}</span>
                    ${unreadBadge}
                </span>
                <span class="conversation-card__preview">${escapeHtml(conversation.last_message_preview || 'Start the conversation')}</span>
                <span class="conversation-card__meta">
                    <span>${escapeHtml(conversation.subtitle || '')}</span>
                    <span>${escapeHtml(formatRelativeChatTime(conversation.last_message_at))}</span>
                </span>
            </span>
        </a>
    `;
}

function renderWidgetConversationButton(conversation, isActive = false) {
    const isGroup = !!conversation.is_group;
    const unreadCount = Number(conversation.unread_count || 0);
    const unreadBadge = unreadCount > 0
        ? `<span class="chat-conversation-unread ${isActive ? 'd-none' : ''}" data-chat-conversation-unread>${escapeHtml(unreadCount)}</span>`
        : '';

    return `
        <span class="chat-conversation-avatar ${isGroup ? '' : 'chat-conversation-avatar--muted'}">${escapeHtml(conversation.avatar_label || 'C')}</span>
        <span class="chat-conversation-copy">
            <span class="chat-conversation-name">
                <span class="text-truncate">${escapeHtml(conversation.name || 'Conversation')}</span>
                ${unreadBadge}
            </span>
            <span class="chat-conversation-preview">${escapeHtml(conversation.last_message_preview || 'Start the conversation')}</span>
            <span class="chat-conversation-meta">
                <span>${escapeHtml(formatRelativeChatTime(conversation.last_message_at))}</span>
            </span>
        </span>
    `;
}

function bindComposerEnterToSend(form, input) {
    if (!form || !input) {
        return;
    }

    input.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' || event.shiftKey || event.isComposing) {
            return;
        }

        event.preventDefault();

        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
            return;
        }

        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    });
}

function createOptimisticChatMessage(body, currentUserId) {
    const now = new Date().toISOString();

    return {
        id: `pending-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
        body,
        mine: true,
        is_system: false,
        created_at: now,
        sender: {
            id: currentUserId || '',
            name: 'You',
            image_url: '',
        },
        attachment: null,
        pending: true,
    };
}

function resetChatComposer(form, bodyInput, attachmentInput, attachmentLabel, previewRoot, previewImage, previewLabel) {
    if (form) {
        form.reset();
    }

    if (bodyInput) {
        bodyInput.value = '';
        bodyInput.defaultValue = '';
    }

    if (attachmentInput) {
        attachmentInput.value = '';
    }

    if (attachmentLabel) {
        attachmentLabel.textContent = 'Attach image';
    }

    updateChatAttachmentPreview(null, previewRoot, previewImage, previewLabel);
    resizeChatComposerTextarea(bodyInput);
}

function showChatSendError(message) {
    const errorMessage = String(message || 'Unable to send message.');

    if (window.toastr && typeof window.toastr.error === 'function') {
        window.toastr.error(errorMessage);
        return;
    }

    console.error(errorMessage);
}

function extractChatErrorMessage(payload, fallbackMessage) {
    if (!payload) {
        return fallbackMessage;
    }

    if (typeof payload.message === 'string' && payload.message.trim() !== '') {
        return payload.message;
    }

    if (payload.errors && typeof payload.errors === 'object') {
        const firstError = Object.values(payload.errors).flat().find((value) => typeof value === 'string' && value.trim() !== '');
        if (firstError) {
            return firstError;
        }
    }

    return fallbackMessage;
}

function resizeChatComposerTextarea(textarea) {
    if (!textarea) {
        return;
    }

    const computedStyle = window.getComputedStyle(textarea);
    const lineHeight = Number.parseFloat(computedStyle.lineHeight) || 20;
    const paddingTop = Number.parseFloat(computedStyle.paddingTop) || 0;
    const paddingBottom = Number.parseFloat(computedStyle.paddingBottom) || 0;
    const borderTop = Number.parseFloat(computedStyle.borderTopWidth) || 0;
    const borderBottom = Number.parseFloat(computedStyle.borderBottomWidth) || 0;
    const maxHeight = Math.ceil((lineHeight * 4) + paddingTop + paddingBottom + borderTop + borderBottom);

    textarea.style.height = 'auto';
    const nextHeight = Math.min(textarea.scrollHeight, maxHeight);
    textarea.style.height = `${nextHeight}px`;
    textarea.style.overflowY = textarea.scrollHeight > maxHeight ? 'auto' : 'hidden';
}

function updateChatAttachmentPreview(file, previewRoot, previewImage, previewLabel) {
    if (!previewRoot) {
        return;
    }

    if (!file) {
        previewRoot.classList.add('d-none');
        previewImage?.classList.add('d-none');
        previewImage?.removeAttribute('src');
        if (previewLabel) {
            previewLabel.textContent = '';
        }
        return;
    }

    previewRoot.classList.remove('d-none');
    if (previewLabel) {
        previewLabel.textContent = file.name || 'Selected attachment';
    }

    if (previewImage && file.type && file.type.startsWith('image/')) {
        previewImage.src = URL.createObjectURL(file);
        previewImage.classList.remove('d-none');
        previewImage.onload = () => URL.revokeObjectURL(previewImage.src);
    } else {
        previewImage?.classList.add('d-none');
        previewImage?.removeAttribute('src');
    }
}

function getLastRenderedMessageId(threadEl) {
    if (!threadEl) {
        return '';
    }

    const messages = threadEl.querySelectorAll('[data-chat-message-id]');
    const lastMessage = messages[messages.length - 1];

    return lastMessage?.dataset.chatMessageId || '';
}

function setThreadLastMessageId(threadEl, messageId) {
    if (!threadEl) {
        return;
    }

    threadEl.dataset.chatLastMessageId = messageId ? String(messageId) : '';
}

function scrollChatThreadToBottom(threadEl) {
    if (!threadEl) {
        return;
    }

    const scrollToBottom = () => {
        threadEl.scrollTop = threadEl.scrollHeight;
    };

    scrollToBottom();

    if (typeof window.requestAnimationFrame === 'function') {
        window.requestAnimationFrame(() => {
            scrollToBottom();
            window.requestAnimationFrame(scrollToBottom);
        });
        return;
    }

    window.setTimeout(scrollToBottom, 0);
}

function replacePendingChatMessage(threadEl, pendingId, message) {
    if (!threadEl || !pendingId) {
        return false;
    }

    const pendingNode = threadEl.querySelector(`[data-chat-message-pending-id="${String(pendingId)}"]`);

    if (!pendingNode) {
        return false;
    }

    const wrapper = document.createElement('div');
    wrapper.innerHTML = renderChatMessage(message);
    const node = wrapper.firstElementChild;

    if (!node) {
        return false;
    }

    pendingNode.replaceWith(node);
    setThreadLastMessageId(threadEl, message.id || getLastRenderedMessageId(threadEl));
    scrollChatThreadToBottom(threadEl);

    return true;
}

function removePendingChatMessage(threadEl, pendingId) {
    if (!threadEl || !pendingId) {
        return;
    }

    const pendingNode = threadEl.querySelector(`[data-chat-message-pending-id="${String(pendingId)}"]`);
    pendingNode?.remove();
}

function removeChatMessageNode(threadEl, messageId, emptyStateHtml = '') {
    if (!threadEl || !messageId) {
        return false;
    }

    const messageNode = threadEl.querySelector(`[data-chat-message-id="${String(messageId)}"]`);

    if (!messageNode) {
        return false;
    }

    const previousSibling = messageNode.previousElementSibling;
    messageNode.remove();

    if (previousSibling?.classList.contains('chat-date-divider')) {
        const nextSibling = previousSibling.nextElementSibling;
        if (!nextSibling || nextSibling.classList.contains('chat-date-divider')) {
            previousSibling.remove();
        }
    }

    if (!threadEl.querySelector('[data-chat-message-id]')) {
        threadEl.innerHTML = emptyStateHtml || threadEl.dataset.emptyStateHtml || '';
    }

    return true;
}

function appendChatMessage(threadEl, message, emptyStateHtml = '') {
    if (!threadEl || !message) {
        return;
    }

    const emptyState = threadEl.querySelector('.chat-empty-state');
    if (emptyState) {
        threadEl.innerHTML = emptyStateHtml || '';
    }

    const node = buildChatMessageNode(message);
    if (!node) {
        return;
    }

    threadEl.appendChild(node);
    setThreadLastMessageId(threadEl, message.id || getLastRenderedMessageId(threadEl));
    scrollChatThreadToBottom(threadEl);
}

function replaceChatMessageNode(threadEl, message) {
    if (!threadEl || !message?.id) {
        return false;
    }

    const existingNode = threadEl.querySelector(`[data-chat-message-id="${String(message.id)}"]`);

    if (!existingNode) {
        return false;
    }

    const nextNode = buildChatMessageNode(message);

    if (!nextNode) {
        return false;
    }

    existingNode.replaceWith(nextNode);
    setThreadLastMessageId(threadEl, message.id || getLastRenderedMessageId(threadEl));
    return true;
}

function updateChatMessageNode(threadEl, message, emptyStateHtml = '') {
    if (!threadEl || !message) {
        return;
    }

    if (!replaceChatMessageNode(threadEl, message)) {
        appendChatMessage(threadEl, message, emptyStateHtml);
        return;
    }

    scrollChatThreadToBottom(threadEl);
}

function closeChatReactionMenus(scope = document) {
    scope.querySelectorAll('[data-chat-message-reaction-open="true"]').forEach((messageNode) => {
        messageNode.dataset.chatMessageReactionOpen = 'false';
    });
}

function closeChatMessageMenus(scope = document) {
    closeChatReactionMenus(scope);
    scope.querySelectorAll('[data-chat-message-options-open="true"]').forEach((messageNode) => {
        messageNode.dataset.chatMessageOptionsOpen = 'false';
    });
}

function getTotalUnreadCount(conversations) {
    return (Array.isArray(conversations) ? conversations : []).reduce(
        (total, conversation) => total + Number(conversation?.unread_count || 0),
        0
    );
}

function initCommunicationsDirectory() {
    document.querySelectorAll('[data-communications-search]').forEach((input) => {
        if (input.dataset.communicationsSearchInitialized === 'true') {
            return;
        }

        input.dataset.communicationsSearchInitialized = 'true';

        const page = input.closest('.communications-page');
        if (!page) {
            return;
        }

        const items = Array.from(page.querySelectorAll('[data-communications-item]'));

        const applyFilter = () => {
            const term = input.value.trim().toLowerCase();

            items.forEach((item) => {
                const haystack = (item.dataset.communicationsText || item.textContent || '').toLowerCase();
                item.classList.toggle('d-none', term !== '' && !haystack.includes(term));
            });
        };

        input.addEventListener('input', applyFilter);
        input.addEventListener('search', applyFilter);
        applyFilter();
    });
}

function initCommunicationsPage() {
    const page = document.querySelector('[data-chat-page-root]');

    if (!page || page.dataset.chatPageInitialized === 'true') {
        return;
    }

    page.dataset.chatPageInitialized = 'true';

    const csrfToken = getCsrfToken();
    const conversationsList = page.querySelector('[data-chat-conversations-list]');
    const threadEl = page.querySelector('[data-chat-thread]');
    const titleEl = page.querySelector('[data-chat-title]');
    const subtitleEl = page.querySelector('[data-chat-subtitle]');
    const avatarEl = page.querySelector('[data-chat-avatar]');
    const unreadBadge = page.querySelector('[data-chat-unread-badge]');
    const composeForm = page.querySelector('[data-chat-message-form]');
    const composeInput = page.querySelector('[data-chat-message-input]');
    const attachmentInput = page.querySelector('[data-chat-attachment-input]');
    const attachmentLabel = page.querySelector('[data-chat-attachment-label]');
    const attachmentPreview = page.querySelector('[data-chat-attachment-preview]');
    const attachmentPreviewImage = page.querySelector('[data-chat-attachment-preview-image]');
    const attachmentPreviewLabel = page.querySelector('[data-chat-attachment-preview-label]');
    const composeEditBar = page.querySelector('[data-chat-compose-edit-bar]');
    const composeEditCancel = page.querySelector('[data-chat-compose-edit-cancel]');
    const inboxUnreadBadge = page.querySelector('[data-chat-inbox-unread-badge]');
    const searchInput = page.querySelector('[data-communications-search]');
    const searchForm = searchInput?.closest('form');
    const autocompleteRoot = page.querySelector('[data-chat-user-autocomplete]');
    const newConversationForm = document.querySelector('[data-chat-new-form]');
    const mobileBackdrop = page.querySelector('[data-chat-mobile-backdrop]');
    const mobileInboxToggle = page.querySelector('[data-chat-mobile-inbox-toggle]');
    const mobileInboxClose = page.querySelector('[data-chat-mobile-inbox-close]');
    const mobilePeopleToggle = page.querySelector('[data-chat-mobile-people-toggle]');
    const mobilePeopleClose = page.querySelector('[data-chat-mobile-people-close]');
    const currentUserId = page.dataset.currentUserId || '';
    const messageRouteBase = page.dataset.messageRouteBase || '';
    const conversationCreateUrl = page.dataset.conversationCreateUrl || '';
    const emptyStateHtml = threadEl?.dataset.emptyStateHtml || '';
    const pollIntervalMs = 5000;
    const inboxPollIntervalMs = 5000;
    let activeConversationId = page.dataset.activeConversationId || '';
    let activeConversationUpdatedAt = page.dataset.activeConversationUpdatedAt || '';
    let lastKnownMessageId = threadEl?.dataset.chatLastMessageId || getLastRenderedMessageId(threadEl) || '';
    let editingMessageId = '';
    let editingMessageNode = null;
    let pollTimer = null;
    let pollInFlight = false;
    let inboxPollTimer = null;
    let inboxPollInFlight = false;
    let autocompleteTimer = null;
    let autocompleteRequestController = null;
    const mobileQuery = window.matchMedia ? window.matchMedia('(max-width: 991.98px)') : null;

    function setActiveCardStyles(conversationId, shouldScroll = true) {
        page.querySelectorAll('[data-chat-conversation-link]').forEach((card) => {
            const isActive = String(card.dataset.chatConversationId) === String(conversationId);
            card.classList.toggle('is-active', isActive);
            if (isActive && shouldScroll) {
                card.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        });
    }

    function setMobileInboxOpen(isOpen) {
        if (!mobileQuery?.matches) {
            page.classList.remove('is-mobile-inbox-open');
            return;
        }

        page.classList.toggle('is-mobile-inbox-open', isOpen);
        if (isOpen) {
            page.classList.remove('is-mobile-people-open');
        }
        if (mobileBackdrop) {
            mobileBackdrop.setAttribute('aria-hidden', isOpen || page.classList.contains('is-mobile-people-open') ? 'false' : 'true');
        }
    }

    function setMobilePeopleOpen(isOpen) {
        if (!mobileQuery?.matches) {
            page.classList.remove('is-mobile-people-open');
            return;
        }

        page.classList.toggle('is-mobile-people-open', isOpen);
        if (isOpen) {
            page.classList.remove('is-mobile-inbox-open');
        }
        if (mobileBackdrop) {
            mobileBackdrop.setAttribute('aria-hidden', isOpen || page.classList.contains('is-mobile-inbox-open') ? 'false' : 'true');
        }
    }

    async function deleteMessage(messageNode) {
        if (!messageNode?.dataset.chatMessageId) {
            return;
        }

        if (!window.confirm('Delete this message?')) {
            return;
        }

        const messageId = String(messageNode.dataset.chatMessageId);
        const endpoint = getMessageDeleteEndpoint(messageId);

        if (!endpoint) {
            return;
        }

        try {
            const response = await fetch(endpoint, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            let payload = null;
            try {
                payload = await response.json();
            } catch (parseError) {
                payload = null;
            }

            if (!response.ok) {
                showChatSendError(extractChatErrorMessage(payload, 'Unable to delete message.'));
                return;
            }

            removeChatMessageNode(threadEl, messageId, emptyStateHtml);
            if (payload?.conversation) {
                setPageHeader(payload.conversation);
                syncConversationCard(payload.conversation, true, false);
                setActiveConversationSyncVersion(payload.conversation);
            }
            syncLastKnownMessageIdFromThread();
            closeChatMessageMenus(threadEl || document);
            cancelChatMessageEdit({ focus: false });
            scrollChatThreadToBottom(threadEl);
        } catch (error) {
            console.error(error);
            showChatSendError('Unable to delete message.');
        }
    }

    function closeMobileInbox() {
        setMobileInboxOpen(false);
    }

    function openMobileInbox() {
        setMobileInboxOpen(true);
    }

    function closeMobilePeople() {
        setMobilePeopleOpen(false);
    }

    function openMobilePeople() {
        setMobilePeopleOpen(true);
    }

    function setActiveConversationSyncVersion(conversation) {
        activeConversationUpdatedAt = conversation?.updated_at || activeConversationUpdatedAt || '';
        page.dataset.activeConversationUpdatedAt = activeConversationUpdatedAt;
    }

    function getReactionEndpoint(messageId) {
        if (!messageId || !activeConversationId) {
            return '';
        }

        return `${messageRouteBase}/${activeConversationId}/messages/${encodeURIComponent(messageId)}/reactions`;
    }

    function getMessageUpdateEndpoint(messageId) {
        if (!messageId || !activeConversationId) {
            return '';
        }

        return `${messageRouteBase}/${activeConversationId}/messages/${encodeURIComponent(messageId)}`;
    }

    function getMessageDeleteEndpoint(messageId) {
        if (!messageId || !activeConversationId) {
            return '';
        }

        return `${messageRouteBase}/${activeConversationId}/messages/${encodeURIComponent(messageId)}`;
    }

    function getMessageBodyFromNode(messageNode) {
        if (!messageNode) {
            return '';
        }

        return String(
            messageNode.dataset.chatMessageBody
            || messageNode.querySelector('.chat-message-text')?.textContent
            || ''
        );
    }

    function syncComposerEditState() {
        const isEditing = Boolean(editingMessageId);

        if (composeForm) {
            if (isEditing) {
                composeForm.dataset.chatEditingMessageId = editingMessageId;
            } else {
                delete composeForm.dataset.chatEditingMessageId;
            }
        }

        composeEditBar?.classList.toggle('d-none', !isEditing);

        const submitLabel = composeForm?.querySelector('[data-chat-submit-label]');
        if (submitLabel) {
            submitLabel.textContent = isEditing ? 'Save' : 'Send';
        }
    }

    function closeChatMessageMenus(scope = document) {
        closeChatReactionMenus(scope);
        scope.querySelectorAll('[data-chat-message-options-open="true"]').forEach((messageNode) => {
            messageNode.dataset.chatMessageOptionsOpen = 'false';
        });
    }

    function cancelChatMessageEdit({ focus = true } = {}) {
        editingMessageId = '';
        editingMessageNode = null;

        syncComposerEditState();

        if (composeInput) {
            resizeChatComposerTextarea(composeInput);
        }

        if (focus) {
            composeInput?.focus();
        }
    }

    function startChatMessageEdit(messageNode) {
        if (!messageNode || !composeForm || !composeInput) {
            return;
        }

        const messageId = messageNode.dataset.chatMessageId || '';
        if (!messageId) {
            return;
        }

        editingMessageId = messageId;
        editingMessageNode = messageNode;
        closeChatMessageMenus(threadEl || document);

        composeInput.value = getMessageBodyFromNode(messageNode);
        resizeChatComposerTextarea(composeInput);

        if (attachmentInput) {
            attachmentInput.value = '';
        }

        if (attachmentLabel) {
            attachmentLabel.textContent = 'Attach image';
        }

        updateChatAttachmentPreview(null, attachmentPreview, attachmentPreviewImage, attachmentPreviewLabel);
        syncComposerEditState();
        composeInput.focus();
        composeInput.setSelectionRange(composeInput.value.length, composeInput.value.length);
    }

    function getMessageNodeById(messageId) {
        if (!threadEl || !messageId) {
            return null;
        }

        return threadEl.querySelector(`[data-chat-message-id="${String(messageId)}"]`);
    }

    function toggleReactionMenu(messageNode, forceOpen = null) {
        if (!messageNode) {
            return;
        }

        const isCurrentlyOpen = messageNode.dataset.chatMessageReactionOpen === 'true';
        const nextState = forceOpen === null
            ? !isCurrentlyOpen
            : !!forceOpen;

        if (nextState) {
            closeChatMessageMenus(threadEl || document);
        } else {
            closeChatMessageMenus(threadEl || document);
            return;
        }

        messageNode.dataset.chatMessageReactionOpen = nextState ? 'true' : 'false';
    }

    function toggleOptionsMenu(messageNode, forceOpen = null) {
        if (!messageNode) {
            return;
        }

        const isCurrentlyOpen = messageNode.dataset.chatMessageOptionsOpen === 'true';
        const nextState = forceOpen === null
            ? !isCurrentlyOpen
            : !!forceOpen;

        if (nextState) {
            closeChatMessageMenus(threadEl || document);
        } else {
            closeChatMessageMenus(threadEl || document);
            return;
        }

        messageNode.dataset.chatMessageOptionsOpen = nextState ? 'true' : 'false';
    }

    async function submitChatReaction(messageId, reaction) {
        const endpoint = getReactionEndpoint(messageId);

        if (!endpoint || !reaction) {
            return;
        }

        try {
            const response = await window.axios.post(endpoint, { reaction }, {
                headers: {
                    Accept: 'application/json',
                },
            });

            const payload = response?.data || null;

            if (payload?.message) {
                updateChatMessageNode(threadEl, payload.message, emptyStateHtml);
                lastKnownMessageId = String(payload.message.id || lastKnownMessageId);
            }

            if (payload?.conversation) {
                setPageHeader(payload.conversation);
                syncConversationCard(payload.conversation, true, false);
                setActiveConversationSyncVersion(payload.conversation);
            }
        } catch (error) {
            const payload = error?.response?.data || null;

            if (error?.response?.status === 419) {
                showChatSendError('Your session expired. Refresh the page and try again.');
                return;
            }

            console.error(error);
            showChatSendError(extractChatErrorMessage(payload, 'Unable to update reaction.'));
        }
    }

    function getUserInitials(name) {
        return String(name || 'User')
            .trim()
            .split(/\s+/)
            .slice(0, 2)
            .map((part) => part.charAt(0))
            .join('')
            .toUpperCase() || 'U';
    }

    function hideUserAutocomplete() {
        if (!autocompleteRoot) {
            return;
        }

        autocompleteRoot.innerHTML = '';
        autocompleteRoot.classList.add('d-none');
        autocompleteRoot.dataset.open = 'false';
    }

    function renderUserAutocomplete(users, term) {
        if (!autocompleteRoot) {
            return;
        }

        const normalizedUsers = Array.isArray(users) ? users : [];
        const content = normalizedUsers.length
            ? normalizedUsers.map((user) => {
                const metaParts = [user.email, user.phone].filter(Boolean);

                return `
                    <div
                        class="communications-search__result"
                        data-chat-user-result
                        data-user-id="${escapeHtml(user.id)}"
                        data-user-name="${escapeHtml(user.name || 'User')}"
                        data-user-email="${escapeHtml(user.email || '')}"
                        data-user-phone="${escapeHtml(user.phone || '')}"
                        role="button"
                        tabindex="0"
                    >
                        <span class="communications-search__result-main">
                            <span class="communications-search__result-avatar">${escapeHtml(getUserInitials(user.name))}</span>
                            <span class="communications-search__result-copy">
                                <span class="communications-search__result-name">${escapeHtml(user.name || 'User')}</span>
                                <span class="communications-search__result-meta">${escapeHtml(metaParts.join(' • ') || 'Start a direct chat')}</span>
                            </span>
                        </span>
                        <button
                            type="button"
                            class="communications-search__result-action"
                            data-chat-user-message-button
                            aria-label="Message ${escapeHtml(user.name || 'User')}"
                            title="Message ${escapeHtml(user.name || 'User')}"
                        >
                            Message
                        </button>
                    </div>
                `;
            }).join('')
            : `<div class="communications-search__empty">No users found for "${escapeHtml(term)}"</div>`;

        autocompleteRoot.innerHTML = content;
        autocompleteRoot.classList.remove('d-none');
        autocompleteRoot.dataset.open = 'true';
    }

    async function fetchUserAutocomplete(term) {
        const searchTerm = String(term || '').trim();

        if (!autocompleteRoot) {
            return;
        }

        if (!searchTerm) {
            hideUserAutocomplete();
            return;
        }

        if (autocompleteRequestController) {
            autocompleteRequestController.abort();
        }

        autocompleteRequestController = new AbortController();

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('q', searchTerm);

            const response = await fetch(url.toString(), {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: autocompleteRequestController.signal,
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            renderUserAutocomplete(payload.users || [], searchTerm);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error(error);
            }
        }
    }

    function scheduleUserAutocomplete() {
        if (!searchInput) {
            return;
        }

        window.clearTimeout(autocompleteTimer);
        autocompleteTimer = window.setTimeout(() => {
            fetchUserAutocomplete(searchInput.value);
        }, 180);
    }

    async function startConversationFromAutocomplete(user, forceNew = false) {
        const createUrl = newConversationForm?.action || conversationCreateUrl;

        if (!user?.id || !createUrl) {
            return;
        }

        const formData = new FormData();
        formData.append('participant_id', user.id);
        if (forceNew) {
            formData.append('force_new', '1');
        }

        try {
            const response = await fetch(createUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            let payload = null;
            try {
                payload = await response.json();
            } catch (parseError) {
                payload = null;
            }

            if (!response.ok) {
                throw payload;
            }

            if (payload?.conversation?.id) {
                searchInput.value = '';
                hideUserAutocomplete();
                const conversationUrl = `/communications?conversation=${encodeURIComponent(payload.conversation.id)}`;
                const loadedConversation = await loadConversation(`${messageRouteBase}/${payload.conversation.id}`, {
                    fallbackUrl: conversationUrl,
                });

                if (!loadedConversation?.conversation?.id) {
                    window.location.assign(conversationUrl);
                    return;
                }

                syncConversationCard(payload.conversation, true, true);
                setActiveCardStyles(payload.conversation.id, true);
                await pollInboxConversations();
            }
        } catch (error) {
            console.error(error);
        } finally {
            composeInput?.focus();
        }
    }

    function startConversationFromSearchResult(resultEl, forceNew = false) {
        if (!resultEl) {
            return;
        }

        startConversationFromAutocomplete({
            id: resultEl.dataset.userId,
            name: resultEl.dataset.userName,
            email: resultEl.dataset.userEmail,
            phone: resultEl.dataset.userPhone,
        }, forceNew);
    }

    function findConversationCard(conversationId) {
        return Array.from(conversationsList?.querySelectorAll('[data-chat-conversation-id]') || [])
            .find((card) => String(card.dataset.chatConversationId) === String(conversationId)) || null;
    }

    function syncLastKnownMessageIdFromThread() {
        lastKnownMessageId = threadEl?.dataset.chatLastMessageId || getLastRenderedMessageId(threadEl) || lastKnownMessageId;
        return lastKnownMessageId;
    }

    function syncConversationCard(conversation, isActive = true, moveToTop = false) {
        if (!conversationsList || !conversation) {
            return null;
        }

        let card = findConversationCard(conversation.id);
        const markup = renderConversationCard(conversation, isActive);

        if (card) {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = markup.trim();
            const nextCard = wrapper.firstElementChild;
            card.replaceWith(nextCard);
            card = nextCard;
        } else {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = markup.trim();
            card = wrapper.firstElementChild;
        }

        if (moveToTop || !card.parentElement) {
            conversationsList.prepend(card);
        }
        setActiveCardStyles(conversation.id);
        return card;
    }

    function updateInboxUnreadBadge(conversations) {
        if (!inboxUnreadBadge) {
            return;
        }

        const totalUnread = getTotalUnreadCount(conversations);
        inboxUnreadBadge.textContent = totalUnread > 0 ? String(totalUnread) : '';
        inboxUnreadBadge.classList.toggle('d-none', totalUnread <= 0);
    }

    function refreshInboxConversations(conversations) {
        if (!conversationsList || !Array.isArray(conversations)) {
            return;
        }

        conversationsList.innerHTML = conversations
            .map((conversation) => renderConversationCard(
                conversation,
                String(conversation.id) === String(activeConversationId)
            ))
            .join('');

        updateInboxUnreadBadge(conversations);
        if (searchInput) {
            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
        setActiveCardStyles(activeConversationId, false);
    }

    async function pollActiveConversation() {
        if (!activeConversationId || !threadEl || pollInFlight || document.hidden) {
            return;
        }

        const afterId = syncLastKnownMessageIdFromThread();
        const url = afterId
            ? `${messageRouteBase}/${activeConversationId}?after_id=${encodeURIComponent(afterId)}`
            : `${messageRouteBase}/${activeConversationId}`;

        pollInFlight = true;

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            const messages = payload.messages || [];
            const nextUpdatedAt = payload.conversation?.updated_at || '';
            const previousUpdatedAt = activeConversationUpdatedAt;

            if (payload.conversation) {
                setPageHeader(payload.conversation);
                syncConversationCard(payload.conversation, true, false);
            }

            if (!messages.length) {
                if (nextUpdatedAt && nextUpdatedAt !== previousUpdatedAt) {
                    await loadConversation(`${messageRouteBase}/${activeConversationId}`, { updateUrl: false });
                }
                return;
            }

            messages.forEach((message) => {
                appendChatMessage(threadEl, message, emptyStateHtml);
                lastKnownMessageId = String(message.id || lastKnownMessageId);
            });

            if (nextUpdatedAt) {
                setActiveConversationSyncVersion(payload.conversation);
            }
        } catch (error) {
            console.error(error);
        } finally {
            pollInFlight = false;
        }
    }

    async function pollInboxConversations() {
        if (!conversationsList || inboxPollInFlight || document.hidden) {
            return;
        }

        inboxPollInFlight = true;

        try {
            const response = await fetch(window.location.href, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            refreshInboxConversations(payload.conversations || []);
        } catch (error) {
            console.error(error);
        } finally {
            inboxPollInFlight = false;
        }
    }

    function refreshChatLiveState() {
        pollActiveConversation();
        pollInboxConversations();
    }

    function setPageHeader(conversation) {
        if (!conversation) {
            return;
        }

        if (titleEl) {
            titleEl.textContent = conversation.name || 'Conversation';
        }

        if (subtitleEl) {
            subtitleEl.textContent = conversation.subtitle || '';
        }

        if (avatarEl) {
            avatarEl.textContent = conversation.avatar_label || 'C';
        }

        if (unreadBadge) {
            const count = Number(conversation.unread_count || 0);
            unreadBadge.textContent = count > 0 ? String(count) : '';
            unreadBadge.classList.toggle('d-none', count <= 0);
        }

        if (composeForm && messageRouteBase && conversation.id) {
            composeForm.action = `${messageRouteBase}/${conversation.id}/messages`;
        }

        page.dataset.activeConversationId = String(conversation.id);
        activeConversationId = String(conversation.id);
        setActiveConversationSyncVersion(conversation);
        syncConversationCard(conversation, true, false);
    }

    function setUrlForConversation(conversationId) {
        const nextUrl = new URL(window.location.href);
        nextUrl.searchParams.set('conversation', conversationId);
        history.pushState({ conversationId: String(conversationId) }, '', nextUrl.toString());
    }

    async function loadConversation(url, { updateUrl = true, fallbackUrl = null } = {}) {
        if (!url) {
            return;
        }

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Unable to load conversation.');
            }

            const payload = await response.json();
            setPageHeader(payload.conversation);
            renderChatThread(threadEl, payload.messages || [], emptyStateHtml);
            cancelChatMessageEdit({ focus: false });
            if (updateUrl && payload.conversation?.id) {
                setUrlForConversation(payload.conversation.id);
            }
            closeMobileInbox();
            return payload;
        } catch (error) {
            console.error(error);

            if (fallbackUrl) {
                window.location.assign(fallbackUrl);
            }
        }
    }

    async function sendMessage(form) {
        const formData = new FormData(form);
        const bodyValue = String(formData.get('body') || '').trim();
        const hasAttachment = !!(attachmentInput && attachmentInput.files && attachmentInput.files.length);
        const editingMessageId = String(form.dataset.chatEditingMessageId || '').trim();
        const isEditing = Boolean(editingMessageId);
        const submitButton = form.querySelector('[data-chat-submit]');
        const submitLabel = submitButton?.querySelector('[data-chat-submit-label]');
        const endpoint = isEditing ? getMessageUpdateEndpoint(editingMessageId) : form.action;
        const method = isEditing ? 'PATCH' : 'POST';

        if (!bodyValue && !hasAttachment) {
            composeInput?.focus();
            return;
        }

        if (isEditing && !bodyValue) {
            showChatSendError('Message cannot be empty.');
            composeInput?.focus();
            return;
        }

        if (bodyValue) {
            formData.set('body', bodyValue);
        }

        if (isEditing) {
            formData.delete('attachment');
        }

        submitButton?.setAttribute('disabled', 'disabled');
        if (submitLabel) {
            submitLabel.textContent = isEditing ? 'Saving...' : 'Sending...';
        }

        try {
            const response = await fetch(endpoint, {
                method,
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            let payload = null;
            try {
                payload = await response.json();
            } catch (parseError) {
                payload = null;
            }

            if (response.status === 422) {
                showChatSendError(extractChatErrorMessage(payload, 'Please fix the highlighted errors and try again.'));
                composeInput?.focus();
                return;
            }

            if (!response.ok) {
                showChatSendError(extractChatErrorMessage(payload, 'Unable to send message.'));
                return;
            }

            if (payload?.message) {
                if (isEditing) {
                    updateChatMessageNode(threadEl, payload.message, emptyStateHtml);
                } else {
                    appendChatMessage(threadEl, payload.message, emptyStateHtml);
                }
                lastKnownMessageId = String(payload.message.id || lastKnownMessageId);
            }

            if (payload?.conversation) {
                syncConversationCard(payload.conversation, true, false);
                pollInboxConversations();
                setPageHeader(payload.conversation);
            }

            resetChatComposer(form, composeInput, attachmentInput, attachmentLabel, attachmentPreview, attachmentPreviewImage, attachmentPreviewLabel);
            cancelChatMessageEdit({ focus: false });
            scrollChatThreadToBottom(threadEl);
            composeInput?.focus();
        } catch (error) {
            showChatSendError('Unable to send message.');
            console.error(error);
        } finally {
            submitButton?.removeAttribute('disabled');
            if (submitLabel) {
                submitLabel.textContent = form.dataset.chatEditingMessageId ? 'Save' : 'Send';
            }
        }
    }

    async function createConversation(form) {
        const createUrl = form?.action || conversationCreateUrl;
        if (!createUrl) {
            return;
        }

        const formData = new FormData(form);
        const submitButton = form.querySelector('[type="submit"]');
        submitButton?.setAttribute('disabled', 'disabled');

        try {
            const response = await fetch(createUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const payload = await response.json();

            if (!response.ok) {
                throw payload;
            }

            if (payload.conversation?.id) {
                await loadConversation(`${messageRouteBase}/${payload.conversation.id}`);
                syncConversationCard(payload.conversation, true, true);
            }
        } finally {
            submitButton?.removeAttribute('disabled');
        }
    }

    async function handleConversationClick(target) {
        const url = target.dataset.chatConversationUrl;
        const conversationId = target.dataset.chatConversationId;

        if (!url || !conversationId) {
            return;
        }

        setActiveCardStyles(conversationId);
        await loadConversation(url, { fallbackUrl: target.href });
    }

    searchForm?.addEventListener('submit', (event) => {
        event.preventDefault();
    });

    if (searchInput) {
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim()) {
                scheduleUserAutocomplete();
            }
        });

        searchInput.addEventListener('input', () => {
            scheduleUserAutocomplete();
        });

        searchInput.addEventListener('search', () => {
            scheduleUserAutocomplete();
        });

        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                hideUserAutocomplete();
            }
        });

        if (searchInput.value.trim()) {
            scheduleUserAutocomplete();
        }
    }

    composeEditCancel?.addEventListener('click', (event) => {
        event.preventDefault();
        cancelChatMessageEdit();
    });

    page.addEventListener('click', (event) => {
        const editButton = event.target.closest('[data-chat-message-edit-button]');
        if (editButton) {
            event.preventDefault();
            event.stopPropagation();
            const messageNode = editButton.closest('[data-chat-message-id]');
            startChatMessageEdit(messageNode);
            return;
        }

        const deleteButton = event.target.closest('[data-chat-message-delete-button]');
        if (deleteButton) {
            event.preventDefault();
            event.stopPropagation();
            const messageNode = deleteButton.closest('[data-chat-message-id]');
            closeChatMessageMenus(threadEl || document);
            deleteMessage(messageNode);
            return;
        }

        const reactionChip = event.target.closest('[data-chat-message-react-toggle]');
        if (reactionChip) {
            event.preventDefault();
            event.stopPropagation();
            const messageNode = reactionChip.closest('[data-chat-message-id]');
            const reaction = reactionChip.dataset.chatMessageReactToggle;
            toggleReactionMenu(messageNode, false);
            if (messageNode?.dataset.chatMessageId && reaction) {
                submitChatReaction(messageNode.dataset.chatMessageId, reaction);
            }
            return;
        }

        const reactionButton = event.target.closest('[data-chat-message-react-button]');
        if (reactionButton) {
            event.preventDefault();
            event.stopPropagation();
            const messageNode = reactionButton.closest('[data-chat-message-id]');
            toggleReactionMenu(messageNode);
            return;
        }

        const likeButton = event.target.closest('[data-chat-message-like-button]');
        if (likeButton) {
            event.preventDefault();
            event.stopPropagation();
            const messageNode = likeButton.closest('[data-chat-message-id]');
            closeChatMessageMenus(threadEl || document);
            if (messageNode?.dataset.chatMessageId) {
                submitChatReaction(messageNode.dataset.chatMessageId, 'like');
            }
            return;
        }

        const moreButton = event.target.closest('[data-chat-message-more-button]');
        if (moreButton) {
            event.preventDefault();
            event.stopPropagation();
            const messageNode = moreButton.closest('[data-chat-message-id]');
            toggleOptionsMenu(messageNode);
            return;
        }

        const reactionOption = event.target.closest('[data-chat-message-react-option]');
        if (reactionOption) {
            event.preventDefault();
            event.stopPropagation();
            const messageNode = reactionOption.closest('[data-chat-message-id]');
            const reaction = reactionOption.dataset.chatMessageReactOption;
            toggleReactionMenu(messageNode, false);
            if (messageNode?.dataset.chatMessageId && reaction) {
                submitChatReaction(messageNode.dataset.chatMessageId, reaction);
            }
            return;
        }

        const messageBubble = event.target.closest('[data-chat-message-bubble]');
        if (messageBubble) {
            const interactiveTarget = event.target.closest('a, button, input, textarea, select, label');

            if (interactiveTarget) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            const messageNode = messageBubble.closest('[data-chat-message-id]');
            toggleReactionMenu(messageNode);
            return;
        }

        const conversationLink = event.target.closest('[data-chat-conversation-link]');

        if (conversationLink && page.contains(conversationLink)) {
            event.preventDefault();
            handleConversationClick(conversationLink);
            hideUserAutocomplete();
            return;
        }

        const userMessageButton = event.target.closest('[data-chat-user-message-button]');

        if (userMessageButton && page.contains(userMessageButton)) {
            event.preventDefault();
            event.stopPropagation();
            startConversationFromSearchResult(userMessageButton.closest('[data-chat-user-result]'), true);
            return;
        }

        const peopleResult = event.target.closest('[data-chat-user-force-new="1"][data-chat-user-result]');
        if (peopleResult && page.contains(peopleResult)) {
            event.preventDefault();
            event.stopPropagation();
            startConversationFromSearchResult(peopleResult, true);
            return;
        }

        const userResult = event.target.closest('[data-chat-user-result]');

        if (userResult && page.contains(userResult)) {
            event.preventDefault();
            event.stopPropagation();
            startConversationFromSearchResult(userResult, userResult.dataset.chatUserForceNew === '1');
            return;
        }

        if (searchInput && !searchInput.contains(event.target) && !autocompleteRoot?.contains(event.target)) {
            hideUserAutocomplete();
        }

        closeChatMessageMenus(threadEl || document);
    });

    page.addEventListener('keydown', (event) => {
        const userResult = event.target.closest('[data-chat-user-result]');

        if (!userResult || !page.contains(userResult)) {
            return;
        }

        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        if (event.target.closest('[data-chat-user-message-button]')) {
            return;
        }

        event.preventDefault();
        startConversationFromSearchResult(userResult, true);
    });

    mobileInboxToggle?.addEventListener('click', (event) => {
        event.preventDefault();
        openMobileInbox();
    });

    mobileInboxClose?.addEventListener('click', (event) => {
        event.preventDefault();
        closeMobileInbox();
    });

    mobilePeopleToggle?.addEventListener('click', (event) => {
        event.preventDefault();
        openMobilePeople();
    });

    mobilePeopleClose?.addEventListener('click', (event) => {
        event.preventDefault();
        closeMobilePeople();
    });

    mobileBackdrop?.addEventListener('click', () => {
        closeMobileInbox();
        closeMobilePeople();
    });

    if (mobileQuery) {
        const syncMobileState = () => {
            if (!mobileQuery.matches) {
                page.classList.remove('is-mobile-inbox-open');
            } else if (!activeConversationId) {
                openMobileInbox();
            }
        };

        if (mobileQuery.addEventListener) {
            mobileQuery.addEventListener('change', syncMobileState);
        }

        syncMobileState();
    }

    page.addEventListener('submit', (event) => {
        const messageForm = event.target.closest('[data-chat-message-form]');
        if (messageForm && page.contains(messageForm)) {
            event.preventDefault();
            sendMessage(messageForm);
            return;
        }

        const peopleForm = event.target.closest('[data-chat-person-form]');
        if (peopleForm && page.contains(peopleForm)) {
            event.preventDefault();
            createConversation(peopleForm);
            return;
        }

        const newConversation = event.target.closest('[data-chat-new-form]');
        if (newConversation && page.contains(newConversation)) {
            event.preventDefault();
            createConversation(newConversation);
        }
    });

    bindComposerEnterToSend(composeForm, composeInput);

    if (composeInput) {
        resizeChatComposerTextarea(composeInput);
        composeInput.addEventListener('input', () => resizeChatComposerTextarea(composeInput));
        composeInput.addEventListener('change', () => resizeChatComposerTextarea(composeInput));
    }

    if (threadEl) {
        syncLastKnownMessageIdFromThread();
        scrollChatThreadToBottom(threadEl);
        refreshChatLiveState();
        pollTimer = window.setInterval(refreshChatLiveState, pollIntervalMs);
        inboxPollTimer = window.setInterval(pollInboxConversations, inboxPollIntervalMs);

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                refreshChatLiveState();
            }
        });

        window.addEventListener('focus', refreshChatLiveState);
        window.addEventListener('online', refreshChatLiveState);

        window.addEventListener('load', () => {
            scrollChatThreadToBottom(threadEl);
            refreshChatLiveState();
        });
    }

    window.addEventListener('popstate', () => {
        const conversationId = new URL(window.location.href).searchParams.get('conversation');
        if (!conversationId) {
            return;
        }

        loadConversation(`${messageRouteBase}/${conversationId}`, {
            updateUrl: false,
            fallbackUrl: `/communications?conversation=${encodeURIComponent(conversationId)}`,
        });
    });

    if (activeConversationId) {
        history.replaceState({ conversationId: String(activeConversationId) }, '', window.location.href);
        setActiveCardStyles(activeConversationId);
    }

    if (mobileQuery?.matches && activeConversationId) {
        closeMobileInbox();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCommunicationsDirectory);
    document.addEventListener('DOMContentLoaded', initCommunicationsPage);
} else {
    initCommunicationsDirectory();
    initCommunicationsPage();
}
