@extends('layouts.master')

@section('contents')
@php
    $currentUser = auth()->user();
    $activeConversationName = $activeConversation ? $activeConversation->titleFor($currentUser) : 'Communications';
    $activeConversationSubtitle = $activeConversation ? $activeConversation->subtitleFor($currentUser) : 'Select a conversation to begin.';
    $lastMessageDay = null;
    $totalUnreadCount = $conversations->sum('unread_count');
    $quickPeople = $users;
    $reactionOptions = [
        'like' => ['emoji' => '👍', 'label' => 'Like'],
        'love' => ['emoji' => '❤️', 'label' => 'Love'],
        'haha' => ['emoji' => '😂', 'label' => 'Haha'],
        'wow' => ['emoji' => '😮', 'label' => 'Wow'],
        'sad' => ['emoji' => '😢', 'label' => 'Sad'],
        'angry' => ['emoji' => '😡', 'label' => 'Angry'],
    ];
@endphp

<div
    class="col-12 communications-page"
    data-chat-page-root
    data-current-user-id="{{ $currentUser->id }}"
    data-active-conversation-id="{{ $activeConversation?->id ?? '' }}"
    data-active-conversation-updated-at="{{ $activeConversation?->updated_at?->toIso8601String() ?? '' }}"
    data-message-route-base="{{ url('/communications/conversations') }}"
    data-conversation-create-url="{{ route('communications.conversations.store') }}"
>
    <div class="communications-mobile-backdrop" data-chat-mobile-backdrop></div>
    <style>
        .communications-page {
            --chat-bg: linear-gradient(180deg, #f8fbff 0%, #eef4ff 48%, #f8fafc 100%);
            --chat-surface: rgba(255, 255, 255, 0.9);
            --chat-border: rgba(148, 163, 184, 0.18);
            min-height: calc(100dvh - 180px);
            padding-bottom: 1rem;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .communications-mobile-backdrop {
            display: none;
        }

        .communications-hero {
            margin-bottom: 1rem;
            padding: 0.9rem 1rem;
            border: 1px solid var(--chat-border);
            border-radius: 1rem;
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.12), transparent 22%),
                linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .communications-hero__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            min-width: 0;
        }

        .communications-hero__title {
            margin: 0;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .communications-hero__actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
            flex: 0 0 auto;
        }

        .communications-grid {
            display: grid;
            grid-template-columns: minmax(240px, 2fr) minmax(0, 5fr) minmax(280px, 3fr);
            gap: 1rem;
            align-items: stretch;
            flex: 1 1 auto;
            min-height: 0;
        }

        .communications-panel {
            display: flex;
            flex-direction: column;
            min-width: 0;
            border: 1px solid var(--chat-border);
            border-radius: 1.35rem;
            background: var(--chat-surface);
            box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            min-height: 0;
        }

        .communications-panel__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1rem 0.85rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        .communications-panel__title {
            margin: 0;
            color: #0f172a;
            font-size: 0.95rem;
            font-weight: 800;
        }

        .communications-panel__subtitle {
            margin-top: 0.2rem;
            color: #64748b;
            font-size: 0.8rem;
        }

        .communications-search {
            position: relative;
            padding: 0.9rem 1rem 0;
        }

        .communications-search__wrap {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.72rem 0.85rem;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 999px;
            background: rgba(248, 250, 252, 0.98);
        }

        .communications-search__wrap i {
            color: #94a3b8;
        }

        .communications-search__input {
            width: 100%;
            border: 0;
            background: transparent;
            outline: none;
            color: #0f172a;
            font-size: 0.9rem;
        }

        .communications-search__input::placeholder {
            color: #94a3b8;
        }

        .communications-search__results {
            position: absolute;
            top: calc(100% + 0.4rem);
            left: 1rem;
            right: 1rem;
            z-index: 25;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
        }

        .communications-search__result {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.85rem;
            width: 100%;
            padding: 0.75rem 0.9rem;
            border: 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.85);
            background: transparent;
            text-align: left;
            transition: background-color 0.15s ease;
        }

        .communications-search__result-main {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
            flex: 1 1 auto;
        }

        .communications-search__result:last-child {
            border-bottom: 0;
        }

        .communications-search__result:hover,
        .communications-search__result:focus-visible {
            background: rgba(37, 99, 235, 0.06);
            outline: none;
        }

        .communications-search__result-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.3rem;
            height: 2.3rem;
            flex: 0 0 auto;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .communications-search__result-copy {
            min-width: 0;
            flex: 1 1 auto;
        }

        .communications-search__result-name {
            display: block;
            color: #0f172a;
            font-size: 0.88rem;
            font-weight: 800;
            line-height: 1.15;
        }

        .communications-search__result-meta {
            display: block;
            margin-top: 0.16rem;
            color: #64748b;
            font-size: 0.75rem;
            line-height: 1.2;
        }

        .communications-search__result-action {
            flex: 0 0 auto;
            border: 1px solid rgba(37, 99, 235, 0.18);
            border-radius: 999px;
            padding: 0.42rem 0.8rem;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #1d4ed8;
            font-size: 0.76rem;
            font-weight: 800;
            line-height: 1;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }

        .communications-search__result:hover .communications-search__result-action,
        .communications-search__result:focus-visible .communications-search__result-action {
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
            transform: translateY(-1px);
        }

        .communications-search__result-action:hover,
        .communications-search__result-action:focus-visible {
            outline: none;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }

        .communications-search__empty {
            padding: 0.85rem 0.9rem;
            color: #64748b;
            font-size: 0.82rem;
        }

        .communications-people {
            display: flex;
            flex-direction: column;
            min-height: 0;
            flex: 1 1 auto;
            padding: 0.2rem 1rem 1rem;
        }

        .communications-people__heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.65rem;
        }

        .communications-people__title {
            margin: 0;
            color: #0f172a;
            font-size: 0.82rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .communications-people__hint {
            color: #94a3b8;
            font-size: 0.74rem;
        }

        .communications-people__list {
            display: grid;
            gap: 0.65rem;
            min-height: 0;
            overflow: auto;
            overflow-x: hidden;
            padding-right: 0.2rem;
        }

        .communications-people__item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            grid-template-areas:
                "avatar name"
                "avatar meta";
            align-items: start;
            gap: 0.5rem 0.85rem;
            width: 100%;
            min-width: 0;
            overflow: hidden;
            min-height: 4.9rem;
            padding: 0.85rem 0.9rem;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 1.1rem;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 251, 255, 0.98) 100%);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
        }

        .communications-people__item:hover,
        .communications-people__item:focus-within {
            transform: translateY(-1px);
            border-color: rgba(37, 99, 235, 0.2);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(244, 249, 255, 1) 100%);
        }

        .communications-people__main {
            display: contents;
        }

        .communications-people__copy {
            display: contents;
        }

        .communications-search__result-avatar {
            grid-area: avatar;
            align-self: start;
            margin-top: 0.05rem;
        }

        .communications-people__name {
            display: block;
            grid-area: name;
            color: #0f172a;
            font-size: 0.9rem;
            font-weight: 800;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .communications-people__meta {
            display: block;
            grid-area: meta;
            margin-top: 0;
            color: #64748b;
            font-size: 0.72rem;
            line-height: 1.3;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .communications-section {
            padding: 0.85rem 0.8rem 0.25rem;
        }

        .communications-section__heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            padding: 0 0.2rem 0.6rem;
        }

        .communications-section__title {
            margin: 0;
            color: #0f172a;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .communications-section__hint {
            color: #94a3b8;
            font-size: 0.72rem;
        }

        .conversation-list {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none;
            overflow: auto;
            padding: 0 0.1rem 0.2rem;
        }

        .conversation-card {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            width: 100%;
            padding: 0.68rem 0.75rem;
            border: 1px solid transparent;
            border-radius: 1rem;
            color: #0f172a;
            background: transparent;
            text-align: left;
            transition: border-color 0.18s ease, background-color 0.18s ease, transform 0.18s ease;
        }

        .conversation-card:hover,
        .conversation-card.is-active {
            border-color: rgba(37, 99, 235, 0.14);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-1px);
        }

        .conversation-card--unread {
            border-color: rgba(37, 99, 235, 0.2);
            background: rgba(219, 234, 254, 0.26);
        }

        .conversation-card--unread .conversation-card__top {
            color: #0f172a;
        }

        .conversation-card__avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            color: #fff;
            font-weight: 800;
            flex: 0 0 auto;
        }

        .conversation-card__copy {
            min-width: 0;
            flex: 1 1 auto;
        }

        .conversation-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            font-size: 0.88rem;
            font-weight: 800;
        }

        .conversation-card__preview {
            display: block;
            overflow: hidden;
            width: 100%;
            color: #64748b;
            font-size: 0.76rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .conversation-card__meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-top: 0.15rem;
            color: #94a3b8;
            font-size: 0.68rem;
        }

        .conversation-card__count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.15rem;
            height: 1.15rem;
            padding: 0 0.3rem;
            border-radius: 999px;
            background: #2563eb;
            color: #fff;
            font-size: 0.68rem;
            font-weight: 800;
        }

        .chat-thread-shell {
            display: flex;
            flex-direction: column;
            min-height: 0;
            height: 100%;
            min-height: calc(100dvh - 420px);
            flex: 1 1 auto;
        }

        .communications-panel--inbox {
            height: 100%;
        }

        .communications-panel--inbox .communications-section {
            display: flex;
            flex: 1 1 auto;
            min-height: 0;
            flex-direction: column;
        }

        .communications-panel--inbox .communications-section:last-child {
            padding-bottom: 0.75rem;
        }

        .communications-panel--inbox .conversation-list {
            flex: 1 1 auto;
        }

        .chat-thread-shell__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            padding: 1rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        .chat-thread-shell__identity {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            min-width: 0;
        }

        .chat-thread-shell__avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 100%);
            color: #fff;
            font-weight: 800;
        }

        .chat-thread-shell__name {
            margin: 0;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 800;
        }

        .chat-thread-shell__sub {
            color: #64748b;
            font-size: 0.82rem;
        }

        .chat-thread-shell__body {
            min-height: 0;
            overflow: auto;
            padding: 1rem 1rem 0.85rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            flex: 1 1 auto;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.05), transparent 22%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .chat-compose {
            position: relative;
            z-index: 2;
            margin-top: auto;
            padding: 1rem;
            border-top: 1px solid rgba(148, 163, 184, 0.12);
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(14px);
            box-shadow: 0 -10px 24px rgba(15, 23, 42, 0.05);
        }

        .communications-panel--thread {
            max-height: calc(100dvh - 220px);
            overflow: hidden;
        }

        .communications-panel--info {
            max-height: calc(100dvh - 220px);
            overflow: hidden;
        }

        .chat-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
            min-height: 0;
            height: 100%;
        }

        .chat-info-card {
            padding: 0.95rem;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 1rem;
            background: rgba(248, 250, 252, 0.86);
        }

        .chat-info-card__label {
            color: #2563eb;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .chat-info-card__value {
            margin-top: 0.4rem;
            color: #0f172a;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .chat-member-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.09);
            color: #1e3a8a;
            font-size: 0.74rem;
            font-weight: 700;
        }

        @media (max-width: 1199.98px) {
            .communications-grid {
                grid-template-columns: minmax(260px, 300px) minmax(0, 1fr);
            }

            .communications-panel--info {
                display: none;
            }
        }

        @media (max-width: 991.98px) {
            .communications-page {
                min-height: calc(100dvh - 120px);
                padding-bottom: 0.5rem;
            }

            .communications-hero {
                margin-bottom: 0.75rem;
                padding: 1rem;
            }

            .communications-hero h2 {
                font-size: 1.35rem;
            }

            .communications-grid {
                grid-template-columns: 1fr;
                position: relative;
            }

            .conversation-list {
                max-height: none;
            }

            .communications-panel {
                border-radius: 1.1rem;
            }

            .communications-panel--info {
                display: none;
            }

            .communications-panel--inbox {
                position: fixed;
                inset: 0 auto 0 0;
                width: min(88vw, 22rem);
                max-width: 88vw;
                z-index: 1085;
                transform: translateX(-105%);
                transition: transform 0.24s ease;
                border-radius: 0 1.1rem 1.1rem 0;
                box-shadow: 20px 0 42px rgba(15, 23, 42, 0.2);
            }

            .communications-page.is-mobile-inbox-open .communications-panel--inbox {
                transform: translateX(0);
            }

            .communications-page.is-mobile-people-open .communications-panel--info {
                display: flex;
                position: fixed;
                inset: 0 0 0 auto;
                width: min(88vw, 22rem);
                max-width: 88vw;
                z-index: 1085;
                transform: translateX(105%);
                transition: transform 0.24s ease;
                border-radius: 1.1rem 0 0 1.1rem;
                box-shadow: -20px 0 42px rgba(15, 23, 42, 0.2);
            }

            .communications-page.is-mobile-people-open .communications-panel--info {
                transform: translateX(0);
            }

            .communications-page.is-mobile-inbox-open .communications-mobile-backdrop,
            .communications-page.is-mobile-people-open .communications-mobile-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 1080;
                background: rgba(15, 23, 42, 0.46);
                backdrop-filter: blur(2px);
            }

            .communications-panel--thread {
                min-height: calc(100dvh - 240px);
                max-height: calc(100dvh - 160px);
            }

            .communications-panel--info {
                max-height: none;
                overflow: hidden;
            }

            .chat-thread-shell {
                min-height: calc(100dvh - 300px);
            }

            .chat-thread-shell__header {
                padding: 0.85rem 0.9rem;
            }

            .chat-thread-shell__avatar {
                width: 2.6rem;
                height: 2.6rem;
            }

            .chat-thread-shell__name {
                font-size: 0.95rem;
            }

            .chat-thread-shell__sub {
                font-size: 0.76rem;
            }

            .chat-thread-shell__body {
                padding: 0.85rem 0.85rem 0.65rem;
                gap: 0.6rem;
            }

            .chat-compose {
                padding: 0.85rem;
            }

            .chat-compose textarea {
                min-height: 4.5rem;
                resize: none;
            }

            .conversation-card {
                padding: 0.75rem;
            }

            .conversation-card__preview,
            .conversation-card__meta {
                font-size: 0.75rem;
            }

            .communications-people {
                padding-inline: 0.75rem;
            }

            .communications-people__item {
                gap: 0.45rem 0.75rem;
                min-height: 4.8rem;
                padding: 0.8rem 0.85rem;
            }

            .communications-people__main {
                display: contents;
            }

            .communications-search__result-avatar {
                grid-area: avatar;
                align-self: start;
                margin-top: 0.05rem;
            }

            .communications-people__copy {
                display: contents;
            }

            .communications-people__name {
                grid-area: name;
                font-size: 0.88rem;
            }

            .communications-people__meta {
                grid-area: meta;
                margin-top: 0;
                font-size: 0.7rem;
                line-height: 1.25;
            }

            .communications-people__copy {
                min-width: 0;
            }
        }
    </style>

    <div class="communications-hero">
        <div class="communications-hero__row">
            <h2 class="communications-hero__title">Communications</h2>
            <div class="communications-hero__actions">
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-toggle="modal" data-target="#newConversationModal">
                    <i class="fas fa-plus mr-1"></i> New chat
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="communications-grid">
        <section class="communications-panel communications-panel--inbox">
                <div class="communications-panel__header">
                <div>
                    <h3 class="communications-panel__title">Inbox</h3>
                    <div class="communications-panel__subtitle">Recent direct chats and groups.</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="conversation-card__count {{ $totalUnreadCount > 0 ? '' : 'd-none' }}" data-chat-inbox-unread-badge>{{ $totalUnreadCount > 0 ? $totalUnreadCount : '' }}</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill d-lg-none" data-chat-mobile-inbox-close>
                        Close
                    </button>
                </div>
            </div>
            <div class="communications-search">
                <form action="{{ route('communications.index') }}" method="GET" class="communications-search__wrap">
                    <i class="fas fa-search"></i>
                    <input
                        type="search"
                        name="q"
                        value="{{ $searchQuery ?? '' }}"
                        class="communications-search__input"
                        placeholder="Search users or chats"
                        autocomplete="off"
                        data-communications-search
                    >
                </form>
                <div class="communications-search__results d-none" data-chat-user-autocomplete></div>
            </div>
            <div class="communications-section">
                <div class="communications-section__heading">
                    <h4 class="communications-section__title">Chats</h4>
                    <span class="communications-section__hint">{{ $conversations->count() }} threads</span>
                </div>
                <div class="conversation-list" data-chat-conversations-list>
                    @forelse($conversations as $conversation)
                        <a
                            href="{{ route('communications.index', ['conversation' => $conversation['id']]) }}"
                            class="conversation-card {{ (int) ($activeConversation?->id ?? 0) === (int) $conversation['id'] ? 'is-active' : '' }} {{ ($conversation['unread_count'] ?? 0) > 0 ? 'conversation-card--unread' : '' }}"
                            data-communications-item
                            data-chat-conversation-link
                            data-chat-conversation-id="{{ $conversation['id'] }}"
                            data-chat-conversation-url="{{ route('communications.conversations.show', $conversation['id']) }}"
                            data-communications-text="{{ $conversation['search_text'] ?? strtolower($conversation['name'] . ' ' . $conversation['subtitle'] . ' ' . ($conversation['last_message_preview'] ?? '')) }}"
                        >
                            <span class="conversation-card__avatar {{ !($conversation['is_group'] ?? false) ? 'chat-conversation-avatar--muted' : '' }}">
                                {{ $conversation['avatar_label'] ?? 'C' }}
                            </span>
                            <span class="conversation-card__copy">
                                <span class="conversation-card__top">
                                    <span class="text-truncate">{{ $conversation['name'] }}</span>
                                    @if(($conversation['unread_count'] ?? 0) > 0)
                                        <span class="conversation-card__count">{{ $conversation['unread_count'] }}</span>
                                    @endif
                                </span>
                                <span class="conversation-card__preview">{{ $conversation['last_message_preview'] ?: 'Start the conversation' }}</span>
                                <span class="conversation-card__meta">
                                    <span>{{ $conversation['subtitle'] }}</span>
                                    <span>{{ $conversation['last_message_at'] ? \Illuminate\Support\Carbon::parse($conversation['last_message_at'])->diffForHumans() : 'Just now' }}</span>
                                </span>
                            </span>
                        </a>
                    @empty
                        <div class="chat-empty-state" data-communications-empty="chats">
                            <div class="chat-thread-avatar">C</div>
                            <div class="font-weight-bold text-dark">No conversations yet</div>
                            <div class="small">Create a direct chat or a group to begin.</div>
                        </div>
                    @endforelse
                </div>
            </div>

        </section>

        <section class="communications-panel communications-panel--thread">
            <div class="chat-thread-shell">
                <div class="chat-thread-shell__header">
                    <div class="chat-thread-shell__identity">
                        <div class="chat-thread-shell__avatar" data-chat-avatar>{{ $activeConversation ? $activeConversation->avatarLabelFor($currentUser) : 'C' }}</div>
                        <div class="min-w-0">
                            <h3 class="chat-thread-shell__name" data-chat-title>{{ $activeConversationName }}</h3>
                            <div class="chat-thread-shell__sub" data-chat-subtitle>{{ $activeConversationSubtitle }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill d-lg-none" data-chat-mobile-inbox-toggle>
                            Inbox
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill d-lg-none" data-chat-mobile-people-toggle>
                            People
                        </button>
                        <span class="conversation-card__count d-none" data-chat-unread-badge></span>
                    </div>
                </div>

                <div
                    class="chat-thread-shell__body"
                    data-chat-thread
                    data-chat-last-message-id="{{ $messages->last()?->id ?? '' }}"
                    data-empty-state-html="<div class='chat-empty-state'><div class='chat-thread-avatar'>C</div><div class='font-weight-bold text-dark'>No messages yet</div><div class='small'>Send the first message to start the thread.</div></div>"
                >
                    @if($activeConversation)
                        @forelse($messages as $message)
                            @php $currentDay = optional($message->created_at)?->format('Y-m-d'); @endphp
                            @if($currentDay !== $lastMessageDay)
                                <div class="chat-date-divider">{{ optional($message->created_at)?->format('M d, Y') }}</div>
                                @php $lastMessageDay = $currentDay; @endphp
                            @endif

                            <article class="chat-message {{ (int) $message->sender_id === $currentUser->id ? 'chat-message--mine' : '' }}" data-chat-message-id="{{ $message->id }}" data-chat-message-body="{{ $message->body ?? '' }}">
                                @if((int) $message->sender_id !== $currentUser->id)
                                    <img class="chat-message-avatar" src="{{ $message->sender?->image_url ?? asset('assets/dist/img/user2-160x160.jpg') }}" alt="{{ $message->sender?->name ?? 'User' }}">
                                @endif
                                <div class="chat-message-content">
                                    @if((int) $message->sender_id !== $currentUser->id)
                                        <div class="chat-message-meta">{{ $message->sender?->name ?? 'User' }}</div>
                                    @endif
                                    <div class="chat-message-row">
                                        @php
                                            $messageReactions = $message->relationLoaded('reactions') ? $message->reactions : collect();
                                            $messageReactionGroups = $messageReactions->groupBy('reaction');
                                            $myReaction = $messageReactions->firstWhere('user_id', $currentUser->id)?->reaction;
                                        @endphp
                                        <div class="chat-message-actions">
                                            <div class="chat-message-reaction-menu" data-chat-message-reaction-menu>
                                                @foreach($reactionOptions as $reaction => $reactionMeta)
                                                    <button
                                                        type="button"
                                                        class="chat-message-reaction-option {{ $myReaction === $reaction ? 'is-active' : '' }}"
                                                        data-chat-message-react-option="{{ $reaction }}"
                                                        aria-label="{{ $reactionMeta['label'] }}"
                                                        title="{{ $reactionMeta['label'] }}"
                                                    >
                                                        <span>{{ $reactionMeta['emoji'] }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                            @if((int) $message->sender_id === $currentUser->id)
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
                                            @endif
                                            <button type="button" class="chat-message-action-button chat-message-react-button" data-chat-message-react-button aria-label="React to message" title="React to message">
                                                <i class="far fa-smile"></i>
                                            </button>
                                            @if((int) $message->sender_id === $currentUser->id)
                                                <button type="button" class="chat-message-action-button chat-message-more-button" data-chat-message-more-button aria-label="More options" title="More options">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="chat-message-bubble {{ (int) $message->sender_id === $currentUser->id ? 'chat-message-bubble--mine' : '' }}" data-chat-message-bubble>
                                            @if($message->body)
                                                <div class="chat-message-text">{!! nl2br(e($message->body)) !!}</div>
                                            @endif
                                            @if($message->attachment_url)
                                                <a class="chat-message-image-link" href="{{ $message->attachment_url }}" target="_blank" rel="noreferrer">
                                                    <img src="{{ $message->attachment_url }}" alt="{{ $message->attachment_name ?? 'Attachment' }}">
                                                </a>
                                            @elseif($message->attachment_path)
                                                <div class="chat-message-file chat-message-file--missing">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <span>Attachment unavailable</span>
                                                </div>
                                            @endif
                                            @if($messageReactionGroups->isNotEmpty())
                                                <div class="chat-message-reactions" data-chat-message-reactions>
                                                    @foreach($messageReactionGroups as $reaction => $group)
                                                        @php $reactionMeta = $reactionOptions[$reaction] ?? ['emoji' => '✨', 'label' => ucfirst($reaction)]; @endphp
                                                        <button type="button" class="chat-message-reaction-chip {{ $myReaction === $reaction ? 'is-mine' : '' }}" data-chat-message-react-toggle="{{ $reaction }}" aria-label="{{ $reactionMeta['label'] }}">
                                                            <span>{{ $reactionMeta['emoji'] }}</span>
                                                            <span>{{ $group->count() }}</span>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="chat-message-time">{{ optional($message->created_at)?->format('g:i A') }}</div>
                                </div>
                            </article>
                        @empty
                            <div class="chat-empty-state">
                                <div class="chat-thread-avatar">C</div>
                                <div class="font-weight-bold text-dark">No messages yet</div>
                                <div class="small">Send the first message to start the thread.</div>
                            </div>
                        @endforelse
                    @else
                        <div class="chat-empty-state">
                            <div class="chat-thread-avatar">C</div>
                            <div class="font-weight-bold text-dark">Choose a conversation</div>
                            <div class="small">Select a thread from the inbox or create a new one.</div>
                        </div>
                    @endif
                </div>

                @if($activeConversation)
                    <form class="chat-compose" method="POST" action="{{ route('communications.messages.store', $activeConversation->id) }}" enctype="multipart/form-data" data-chat-message-form>
                        @csrf
                        <div class="chat-compose-edit d-none" data-chat-compose-edit-bar>
                            <div>
                                <strong>Editing message</strong>
                                <div class="small text-muted">Update the text and save the change.</div>
                            </div>
                            <button type="button" class="chat-compose-edit__cancel" data-chat-compose-edit-cancel>Cancel</button>
                        </div>
                        <div class="form-group mb-2">
                            <textarea name="body" class="form-control chat-composer-textarea" rows="1" placeholder="Write a message..." data-chat-message-input>{{ old('body') }}</textarea>
                            <div class="chat-composer-preview d-none" data-chat-attachment-preview>
                                <img data-chat-attachment-preview-image alt="Selected attachment preview" class="chat-composer-preview__image d-none">
                                <div class="chat-composer-preview__meta">
                                    <div class="chat-composer-preview__label" data-chat-attachment-preview-label></div>
                                    <div class="chat-composer-preview__hint">Attachment ready to send</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="btn btn-sm btn-outline-secondary rounded-pill mb-0">
                                    <i class="fas fa-image mr-1"></i>
                                    <span data-chat-attachment-label>Attach image</span>
                                    <input type="file" name="attachment" class="d-none" accept="image/*" data-chat-attachment-input>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3" data-chat-submit>
                                <span data-chat-submit-label>Send</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="chat-compose">
                        <div class="text-muted small">Pick a conversation to start chatting.</div>
                    </div>
                @endif
            </div>
        </section>

        <section class="communications-panel communications-panel--info">
            <div class="chat-info">
                <div class="chat-info-card">
                    <div class="d-flex justify-content-end d-lg-none mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-chat-mobile-people-close>
                            Close
                        </button>
                    </div>
                    <div class="chat-info-card__label">People</div>
                    <div class="chat-info-card__value">Start a direct chat</div>
                    <div class="small text-muted mt-1">Select a user below to open a new conversation.</div>
                </div>

                @if($quickPeople->isNotEmpty())
                    <div class="communications-people">
                        <div class="communications-people__heading">
                            <h4 class="communications-people__title">People</h4>
                            <span class="communications-people__hint">{{ $quickPeople->count() }} shown</span>
                        </div>
                        <div class="communications-people__list">
                            @foreach($quickPeople as $person)
                                <div
                                    class="communications-people__item"
                                    data-chat-user-result
                                    data-user-id="{{ $person->id }}"
                                    data-user-name="{{ $person->name }}"
                                    data-user-email="{{ $person->email }}"
                                    data-user-phone="{{ $person->employee?->phone ?? '' }}"
                                    role="button"
                                    tabindex="0"
                                >
                                    <div class="communications-people__main">
                                        <span class="communications-search__result-avatar">{{ strtoupper(mb_substr($person->name ?? 'U', 0, 2)) }}</span>
                                        <span class="communications-people__copy">
                                            <span class="communications-people__name">{{ $person->name }}</span>
                                            <span class="communications-people__meta">
                                                {{ $person->email }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="chat-empty-state">
                        <div class="chat-thread-avatar">P</div>
                        <div class="font-weight-bold text-dark">No people available</div>
                        <div class="small">Try again later or use the search box to find someone.</div>
                    </div>
                @endif
            </div>
        </section>

    </div>
</div>

<div class="modal fade" id="newConversationModal" tabindex="-1" role="dialog" aria-labelledby="newConversationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="newConversationModalLabel">New direct chat or group</h5>
                    <small class="text-muted">Choose one user for a direct message or several for a group.</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('communications.conversations.store') }}" data-chat-new-form>
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="newConversationName">Group name</label>
                        <input type="text" name="name" id="newConversationName" class="form-control" placeholder="Optional group name">
                        <small class="form-text text-muted">Leave blank for a direct message.</small>
                    </div>
                    <div class="form-group">
                        <label for="newConversationParticipants">Participants</label>
                        <select
                            name="participant_ids[]"
                            id="newConversationParticipants"
                            class="form-control select2-checkboxes"
                            multiple
                            data-placeholder="Search users"
                            data-select2-checkboxes="1"
                        >
                            @foreach($users as $user)
                                <option
                                    value="{{ $user->id }}"
                                    data-search-text="{{ strtolower(trim($user->name . ' ' . ($user->email ?? '') . ' ' . ($user->employee?->phone ?? ''))) }}"
                                >
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Select at least one person. One participant creates a direct chat, more than one creates a group.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Create conversation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
