@extends('layouts.admin')

@section('content')
<style>
    /* ============================================================
       MESSENGER-STYLE CHAT LAYOUT - Responsive CSS
       Prevents main page scroll, isolates message area scroll
    ============================================================ */

    #live-chat-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100dvh - 5.5rem);
        overflow: hidden;
    }

    @media (min-width: 768px) {
        #live-chat-wrapper {
            height: calc(100dvh - 7.5rem);
        }
    }

    #chat-app-grid {
        display: grid;
        grid-template-columns: 1fr;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }

    @media (min-width: 768px) {
        #chat-app-grid {
            grid-template-columns: 320px 1fr;
        }
    }

    @media (min-width: 1024px) {
        #chat-app-grid {
            grid-template-columns: 360px 1fr;
        }
    }

    #conversations-sidebar {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
        overflow: hidden;
    }

    #conversations-list {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
    }

    #chat-panel {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
        overflow: hidden;
        position: relative;
    }

    #chat-header {
        flex-shrink: 0;
    }

    #messages-container {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #message-composer {
        flex-shrink: 0;
    }

    /* Scrollbar styling */
    #conversations-list::-webkit-scrollbar,
    #messages-container::-webkit-scrollbar,
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    #conversations-list::-webkit-scrollbar-track,
    #messages-container::-webkit-scrollbar-track,
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    #conversations-list::-webkit-scrollbar-thumb,
    #messages-container::-webkit-scrollbar-thumb,
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(100, 116, 139, 0.25);
        border-radius: 9999px;
    }
    #conversations-list::-webkit-scrollbar-thumb:hover,
    #messages-container::-webkit-scrollbar-thumb:hover,
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(16, 185, 129, 0.6);
    }

    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Message Bubbles */
    .msg-row {
        display: flex;
        width: 100%;
        margin-bottom: 8px;
    }
    .msg-row.customer {
        justify-content: flex-start;
    }
    .msg-row.admin {
        justify-content: flex-end;
    }
    .msg-content-wrapper {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        max-width: 88%;
    }
    @media (min-width: 640px) {
        .msg-content-wrapper {
            max-width: 78%;
        }
    }
    .msg-body-col {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .msg-row.customer .msg-body-col {
        align-items: flex-start;
    }
    .msg-row.admin .msg-body-col {
        align-items: flex-end;
    }
</style>

<div id="live-chat-wrapper" x-data="{
    activeSessionId: {{ Js::from($activeSessionId) }},
    isBlocked: {{ Js::from($activeIsBlocked) }},
    isApproved: {{ Js::from($activeIsApproved) }},
    showMobileChat: {{ Js::from(!empty($activeSessionId)) }},
    searchQuery: '',
    currentFilter: 'all',
    showMobileActions: false,
    isUserNearBottom: true,
    newUnreadCount: 0,
    messages: {{ Js::from($messages->map(function($m) {
        return [
            'id' => $m->id,
            'session_id' => $m->session_id,
            'sender' => $m->sender ?? 'user',
            'is_admin' => ($m->sender === 'admin' || $m->sender_type === 'admin'),
            'message' => $m->message,
            'time' => $m->created_at ? $m->created_at->format('h:i A') : '',
        ];
    })) }},
    sessions: {{ Js::from($sessions->map(function($s) {
        return [
            'session_id' => $s->session_id,
            'user_id' => $s->user_id,
            'name' => $s->display_name ?? ($s->user ? $s->user->name : ($s->session_id ? 'Guest (' . substr($s->session_id, 0, 8) . '...)' : 'Website Visitor')),
            'email' => $s->display_email ?? ($s->user ? $s->user->email : 'Guest visitor'),
            'phone' => $s->phone ?? null,
            'last_time' => $s->last_message_at ? \Carbon\Carbon::parse($s->last_message_at)->diffForHumans(null, true, true) : '',
            'unread_count' => $s->unread_count ?? 0,
            'last_message' => $s->last_message ?? '',
            'is_blocked' => $s->is_blocked ?? false,
            'is_approved' => $s->is_approved ?? false,
        ];
    })) }},
    replyMessage: '',
    isSending: false,
    pollTimer: null,
    totalUnread: 0,

    get filteredSessions() {
        return this.sessions.filter(s => {
            const matchesSearch = !this.searchQuery.trim() || 
                (s.name && s.name.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                (s.email && s.email.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                (s.last_message && s.last_message.toLowerCase().includes(this.searchQuery.toLowerCase()));
            
            if (!matchesSearch) return false;

            if (this.currentFilter === 'unread') return s.unread_count > 0;
            if (this.currentFilter === 'pending') return !s.is_approved && !s.is_blocked;
            if (this.currentFilter === 'blocked') return s.is_blocked;
            return true;
        });
    },

    get activeSession() {
        return this.sessions.find(s => s.session_id === this.activeSessionId) || null;
    },

    init() {
        this.calculateUnread();
        this.$nextTick(() => {
            this.scrollToBottom(true);
        });
        if (this.activeSessionId) {
            this.startPolling();
        }
    },
    calculateUnread() {
        this.totalUnread = this.sessions.reduce((acc, s) => acc + (s.unread_count || 0), 0);
        this.updatePageTitle();
    },
    updatePageTitle() {
        if (this.totalUnread > 0) {
            document.title = '\uD83D\uDD34 (' + this.totalUnread + ') New Message! - Live Support';
        } else {
            document.title = 'Live Support Conversations - Admin Portal';
        }
    },
    playChimeSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.35);
        } catch(e) {}
    },
    startPolling() {
        if (this.pollTimer) clearInterval(this.pollTimer);
        this.pollTimer = setInterval(() => {
            this.fetchLiveMessages();
        }, 2500);
    },
    async selectSession(sessionId) {
        this.activeSessionId = sessionId;
        this.showMobileChat = true;
        this.showMobileActions = false;
        this.newUnreadCount = 0;
        this.isUserNearBottom = true;
        const currentSess = this.sessions.find(s => s.session_id === sessionId);
        if (currentSess) {
            this.isBlocked = currentSess.is_blocked || false;
            this.isApproved = currentSess.is_approved || false;
        }
        if (window.history && window.history.pushState) {
            window.history.pushState(null, '', '?session_id=' + encodeURIComponent(sessionId));
        }
        await this.fetchLiveMessages();
        this.scrollToBottom(true);
        this.startPolling();
    },
    backToSessions() {
        this.showMobileChat = false;
        this.showMobileActions = false;
    },
    handleScroll(e) {
        const el = e.target;
        if (!el) return;
        const threshold = 120;
        const distFromBottom = el.scrollHeight - el.scrollTop - el.clientHeight;
        this.isUserNearBottom = distFromBottom <= threshold;
        if (this.isUserNearBottom) {
            this.newUnreadCount = 0;
        }
    },
    async fetchLiveMessages() {
        if (!this.activeSessionId) return;
        try {
            const res = await fetch('/admin/chats/' + encodeURIComponent(this.activeSessionId) + '/fetch', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                if (typeof data.is_blocked !== 'undefined') {
                    this.isBlocked = data.is_blocked;
                }
                if (typeof data.is_approved !== 'undefined') {
                    this.isApproved = data.is_approved;
                }
                if (Array.isArray(data.messages)) {
                    const prevCount = this.messages.length;
                    this.messages = data.messages;
                    if (data.messages.length > prevCount) {
                        const newCount = data.messages.length - prevCount;
                        const lastMsg = data.messages[data.messages.length - 1];
                        if (!lastMsg.is_admin) {
                            this.playChimeSound();
                        }
                        if (this.isUserNearBottom || lastMsg.is_admin) {
                            this.scrollToBottom();
                        } else {
                            this.newUnreadCount += newCount;
                        }
                    }
                }
                if (Array.isArray(data.sessions)) {
                    const prevTotalUnread = this.totalUnread;
                    this.sessions = data.sessions;
                    this.calculateUnread();
                    if (this.totalUnread > prevTotalUnread) {
                        this.playChimeSound();
                    }
                }
            }
        } catch(e) {}
    },
    async toggleApprove() {
        if (!this.activeSessionId) return;
        const currentSess = this.sessions.find(s => s.session_id === this.activeSessionId);
        const userId = currentSess ? currentSess.user_id : null;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('session_id', this.activeSessionId);
        if (userId) {
            formData.append('user_id', userId);
        }

        try {
            const res = await fetch('{{ route("admin.chats.toggle-approve") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                this.isApproved = data.is_approved;
                if (currentSess) {
                    currentSess.is_approved = data.is_approved;
                }
                this.fetchLiveMessages();
            }
        } catch(e) {}
    },
    async toggleBlock() {
        if (!this.activeSessionId) return;
        const currentSess = this.sessions.find(s => s.session_id === this.activeSessionId);
        const userId = currentSess ? currentSess.user_id : null;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('session_id', this.activeSessionId);
        if (userId) {
            formData.append('user_id', userId);
        }

        try {
            const res = await fetch('{{ route("admin.chats.toggle-block") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                this.isBlocked = data.is_blocked;
                if (currentSess) {
                    currentSess.is_blocked = data.is_blocked;
                }
                this.fetchLiveMessages();
            }
        } catch(e) {}
    },
    async sendReply() {
        if (!this.replyMessage.trim() || !this.activeSessionId || this.isSending || this.isBlocked) return;
        this.isSending = true;
        const msgText = this.replyMessage;
        this.replyMessage = '';

        try {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('session_id', this.activeSessionId);
            formData.append('message', msgText);

            const res = await fetch('{{ route("admin.chats.reply") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await res.json();
            if (data.success && data.data) {
                this.messages.push(data.data);
                this.isApproved = true;
                this.newUnreadCount = 0;
                this.isUserNearBottom = true;
                this.scrollToBottom(true);
            } else {
                this.fetchLiveMessages();
            }
        } catch(e) {
            this.replyMessage = msgText;
        } finally {
            this.isSending = false;
        }
    },
    scrollToBottom(force = false) {
        this.$nextTick(() => {
            setTimeout(() => {
                const el = document.getElementById('messages-container');
                if (el) {
                    el.scrollTop = el.scrollHeight;
                    if (force) {
                        this.isUserNearBottom = true;
                        this.newUnreadCount = 0;
                    }
                }
            }, 80);
        });
    },
    insertQuickReply(text) {
        if (this.isBlocked) return;
        this.replyMessage = text;
        this.$nextTick(() => {
            const input = document.getElementById('reply-input');
            if (input) {
                input.focus();
                input.selectionStart = input.selectionEnd = input.value.length;
            }
        });
    }
}">

    <!-- Top Bar & Breadcrumbs (Hidden on mobile for maximum chat real-estate) -->
    <div style="flex-shrink:0;" class="mb-2.5 hidden sm:block">
        <x-breadcrumbs :items="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Live Support Chat']
        ]" />
        <div class="flex items-center justify-between gap-3 mt-1">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base shadow-2xs">
                    💬
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white leading-tight">Live Support Conversations</h1>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Real-time messaging with instant approval & blocking controls</p>
                </div>
            </div>
            <template x-if="totalUnread > 0">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/10 dark:bg-rose-950/40 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs font-bold shadow-2xs animate-pulse">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    <span x-text="totalUnread + ' Unread Message(s)'"></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Chat App Grid (Fills 100% of remaining height) -->
    <div id="chat-app-grid" class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl sm:rounded-3xl shadow-sm overflow-hidden min-h-0 flex-1">

        <!-- LEFT: Conversations Sidebar -->
        <div id="conversations-sidebar"
             class="border-r border-slate-200/90 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-950/40"
             :class="showMobileChat ? 'hidden md:flex md:flex-col' : 'flex flex-col'">

            <!-- Sidebar Header & Search -->
            <div style="flex-shrink:0;" class="p-3 sm:p-3.5 border-b border-slate-200/80 dark:border-slate-800 space-y-2.5 bg-white/60 dark:bg-slate-900/60 backdrop-blur-xs">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                        <span>Inbox</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-mono" x-text="sessions.length"></span>
                    </h3>
                    <span class="inline-flex items-center gap-1.5 text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Live Sync
                    </span>
                </div>

                <!-- Search Filter Input -->
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 h-3.5 w-3.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        placeholder="Search customer or message..." 
                        class="w-full pl-8 pr-7 py-1.5 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-emerald-500 transition-colors"
                    />
                    <button x-show="searchQuery" @click="searchQuery = ''" type="button" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</button>
                </div>

                <!-- Status Filter Pills -->
                <div class="flex items-center gap-1 overflow-x-auto no-scrollbar pt-0.5 text-[11px] font-semibold">
                    <button 
                        @click="currentFilter = 'all'" 
                        type="button" 
                        class="px-2.5 py-1 rounded-lg transition-colors shrink-0"
                        :class="currentFilter === 'all' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    >
                        All
                    </button>
                    <button 
                        @click="currentFilter = 'unread'" 
                        type="button" 
                        class="px-2.5 py-1 rounded-lg transition-colors shrink-0 flex items-center gap-1"
                        :class="currentFilter === 'unread' ? 'bg-rose-500 text-white shadow-2xs' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    >
                        Unread
                        <span x-show="totalUnread > 0" class="px-1 py-0.2 bg-white/20 rounded-full text-[9px]" x-text="totalUnread"></span>
                    </button>
                    <button 
                        @click="currentFilter = 'pending'" 
                        type="button" 
                        class="px-2.5 py-1 rounded-lg transition-colors shrink-0"
                        :class="currentFilter === 'pending' ? 'bg-amber-500 text-white shadow-2xs' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    >
                        Pending
                    </button>
                    <button 
                        @click="currentFilter = 'blocked'" 
                        type="button" 
                        class="px-2.5 py-1 rounded-lg transition-colors shrink-0"
                        :class="currentFilter === 'blocked' ? 'bg-slate-800 text-white shadow-2xs' : 'bg-slate-200/70 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    >
                        Blocked
                    </button>
                </div>
            </div>

            <!-- Conversations List -->
            <div id="conversations-list" class="divide-y divide-slate-100 dark:divide-slate-800/60 custom-scrollbar">
                <template x-for="sess in filteredSessions" :key="sess.session_id">
                    <div @click="selectSession(sess.session_id)"
                         class="p-3 sm:p-3.5 transition-all cursor-pointer select-none active:bg-slate-100 dark:active:bg-slate-800"
                         :class="[
                             activeSessionId === sess.session_id
                                 ? 'bg-white dark:bg-slate-800/90 border-l-4 border-l-emerald-500 shadow-2xs'
                                 : 'hover:bg-white/70 dark:hover:bg-slate-800/40',
                             sess.unread_count > 0 && activeSessionId !== sess.session_id
                                 ? 'bg-rose-500/10 dark:bg-rose-950/30 border-l-4 border-l-rose-500'
                                 : ''
                         ]">
                        <div class="flex items-start gap-2.5">
                            <!-- Avatar -->
                            <div class="h-10 w-10 rounded-2xl font-extrabold flex items-center justify-center text-xs shrink-0 relative border shadow-2xs"
                                 :class="sess.is_blocked
                                     ? 'bg-slate-200 text-slate-600 border-slate-300 dark:bg-slate-800 dark:text-slate-400'
                                     : (sess.unread_count > 0
                                         ? 'bg-gradient-to-br from-rose-500 to-rose-600 text-white border-rose-400'
                                         : 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white border-emerald-400/40')">
                                <span x-text="(sess.name || 'G').charAt(0).toUpperCase()"></span>
                                <template x-if="sess.unread_count > 0">
                                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-rose-500 border-2 border-white dark:border-slate-900 animate-pulse"></span>
                                </template>
                            </div>

                            <!-- Content -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="sess.name"></h4>
                                    <span class="text-[10px] text-slate-400 font-mono shrink-0" x-text="sess.last_time"></span>
                                </div>
                                <p class="text-xs truncate font-medium"
                                   :class="sess.unread_count > 0 ? 'text-rose-600 dark:text-rose-300 font-bold' : 'text-slate-500 dark:text-slate-400'"
                                   x-text="sess.last_message || sess.email || 'No messages yet'"></p>
                                
                                <!-- Status Tags -->
                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    <template x-if="sess.is_blocked">
                                        <span class="px-1.5 py-0.2 rounded-full bg-slate-800 text-white font-bold text-[9px] uppercase tracking-wider">🚫 Blocked</span>
                                    </template>
                                    <template x-if="!sess.is_blocked && sess.is_approved">
                                        <span class="px-1.5 py-0.2 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold text-[9px]">Approved</span>
                                    </template>
                                    <template x-if="!sess.is_blocked && !sess.is_approved">
                                        <span class="px-1.5 py-0.2 rounded-full bg-amber-500/15 text-amber-600 dark:text-amber-400 font-bold text-[9px]">Pending</span>
                                    </template>
                                    <template x-if="sess.unread_count > 0">
                                        <span class="ml-auto px-1.5 py-0.2 rounded-full bg-rose-500 text-white font-extrabold text-[9px] font-mono">
                                            <span x-text="sess.unread_count"></span> new
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="filteredSessions.length === 0">
                    <div class="p-8 text-center text-xs text-slate-400 space-y-1">
                        <p class="font-bold text-slate-500 dark:text-slate-300">No conversations found</p>
                        <p class="text-[11px]">Try adjusting your search query or filters</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- RIGHT: Active Chat Panel -->
        <div id="chat-panel"
             :class="showMobileChat ? 'flex flex-col' : 'hidden md:flex md:flex-col'">

            <template x-if="activeSessionId">
                <div style="display:flex; flex-direction:column; height:100%; min-height:0; width:100%; overflow:hidden; position:relative;">

                    <!-- Chat Header -->
                    <div id="chat-header" class="p-2.5 sm:p-3.5 border-b border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md z-10">
                        <div class="flex items-center justify-between gap-2">
                            <!-- Left: Mobile Back Button + Customer Info -->
                            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                <!-- Mobile Back Button -->
                                <button 
                                    type="button" 
                                    @click="backToSessions()"
                                    class="md:hidden flex items-center justify-center h-8 w-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs shrink-0 active:scale-95 transition-all shadow-2xs"
                                    aria-label="Back to conversations"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                                </button>

                                <!-- Customer Avatar -->
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs"
                                     :class="isBlocked ? 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' : 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20'">
                                    <span x-text="isBlocked ? '🚫' : (activeSession ? (activeSession.name || 'U').charAt(0).toUpperCase() : '👤')"></span>
                                </div>

                                <!-- Customer Name & Status -->
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate flex items-center gap-1.5 leading-tight">
                                        <span x-text="activeSession ? activeSession.name : 'Guest Visitor'"></span>
                                        <span class="text-[10px] font-normal text-slate-400 hidden lg:inline truncate" x-text="activeSession && activeSession.email ? '(' + activeSession.email + ')' : ''"></span>
                                    </h3>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <template x-if="!isBlocked && isApproved">
                                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400 font-bold truncate">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                                                <span>Live Connection</span>
                                            </span>
                                        </template>
                                        <template x-if="!isBlocked && !isApproved">
                                            <span class="inline-flex items-center gap-1 text-[10px] text-amber-500 dark:text-amber-400 font-bold truncate">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-ping shrink-0"></span>
                                                <span>Waiting Approval</span>
                                            </span>
                                        </template>
                                        <template x-if="isBlocked">
                                            <span class="inline-flex items-center gap-1 text-[10px] text-rose-600 dark:text-rose-400 font-bold uppercase truncate">
                                                🚫 Customer Blocked
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Actions: Desktop Full Buttons + Mobile Clean Controls -->
                            <div class="flex items-center gap-1.5 shrink-0">
                                <!-- Approve / Unapprove Button -->
                                <button type="button" @click="toggleApprove()"
                                        class="px-2.5 sm:px-3 py-1.5 rounded-xl text-[11px] sm:text-xs font-bold transition flex items-center gap-1 border cursor-pointer active:scale-95 shadow-2xs"
                                        :class="isApproved
                                            ? 'bg-emerald-500 text-white border-emerald-600 hover:bg-emerald-600'
                                            : 'bg-amber-500 text-white border-amber-600 hover:bg-amber-600'">
                                    <span x-text="isApproved ? '✅' : '⚡'"></span>
                                    <span class="hidden xs:inline" x-text="isApproved ? 'Approved' : 'Approve'"></span>
                                </button>

                                <!-- Block / Unblock Button -->
                                <button type="button" @click="toggleBlock()"
                                        class="px-2.5 sm:px-3 py-1.5 rounded-xl text-[11px] sm:text-xs font-bold transition flex items-center gap-1 border cursor-pointer active:scale-95 shadow-2xs"
                                        :class="isBlocked
                                            ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-300 dark:border-emerald-700 hover:bg-emerald-100'
                                            : 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border-rose-300 dark:border-rose-700 hover:bg-rose-100'">
                                    <span x-text="isBlocked ? '🔓' : '🚫'"></span>
                                    <span class="hidden xs:inline" x-text="isBlocked ? 'Unblock' : 'Block'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div id="messages-container"
                         @scroll="handleScroll($event)"
                         class="p-3 sm:p-4 bg-slate-50/50 dark:bg-slate-950/30 custom-scrollbar"
                         style="display:flex; flex-direction:column; gap:8px;">

                        <!-- Spacer: pushes messages to bottom when few -->
                        <div style="flex:1 1 auto; min-height:0;"></div>

                        <template x-for="msg in messages" :key="msg.id || Math.random()">
                            <div class="msg-row" :class="msg.is_admin ? 'admin' : 'customer'">

                                <!-- ===== CUSTOMER MESSAGE: left-aligned ===== -->
                                <template x-if="!msg.is_admin">
                                    <div class="msg-content-wrapper">
                                        <div class="w-7 h-7 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[10px] flex items-center justify-center shrink-0 border border-slate-300 dark:border-slate-700 shadow-2xs">
                                            C
                                        </div>
                                        <div class="msg-body-col">
                                            <div class="px-3.5 py-2 text-xs leading-relaxed rounded-2xl rounded-bl-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs max-w-full break-words whitespace-pre-wrap">
                                                <span x-text="msg.message"></span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 px-1 font-mono" x-text="msg.time"></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- ===== ADMIN MESSAGE: right-aligned ===== -->
                                <template x-if="msg.is_admin">
                                    <div class="msg-content-wrapper">
                                        <div class="msg-body-col">
                                            <div class="px-3.5 py-2 text-xs leading-relaxed rounded-2xl rounded-br-xs bg-emerald-600 text-white shadow-2xs max-w-full break-words whitespace-pre-wrap">
                                                <span x-text="msg.message"></span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 px-1 font-mono" x-text="msg.time"></span>
                                        </div>
                                        <div class="w-7 h-7 rounded-xl bg-emerald-700 text-white font-bold text-[10px] flex items-center justify-center shrink-0 shadow-2xs">
                                            A
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Scroll to bottom pill when user scrolled up and new message arrived -->
                        <template x-if="!isUserNearBottom && newUnreadCount > 0">
                            <div class="sticky bottom-2 flex justify-center z-20">
                                <button type="button" @click="scrollToBottom(true)"
                                        class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-full shadow-lg flex items-center gap-1.5 transition-all animate-bounce cursor-pointer">
                                    <span>↓ New message (<span x-text="newUnreadCount"></span>)</span>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Message Composer (Fixed at bottom) -->
                    <div id="message-composer"
                         class="p-2.5 sm:p-3.5 border-t border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md">

                        <!-- Blocked notice -->
                        <template x-if="isBlocked">
                            <div class="p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-600 dark:text-rose-400 text-xs font-bold text-center">
                                🚫 Customer is blocked. Click "Unblock" above to restore chatting.
                            </div>
                        </template>

                        <!-- Approval notice -->
                        <template x-if="!isBlocked && !isApproved">
                            <div class="mb-2 p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-300 text-xs flex items-center justify-between gap-2">
                                <span class="truncate">⚡ Customer is waiting for approval</span>
                                <button type="button" @click="toggleApprove()" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-[11px] cursor-pointer shrink-0">
                                    Approve Now
                                </button>
                            </div>
                        </template>

                        <template x-if="!isBlocked">
                            <div>
                                <!-- Horizontal Scrollable Quick Replies -->
                                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-2 pt-0.5">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-0.5">Quick:</span>
                                    <button type="button"
                                            @click="insertQuickReply('👋 Hello! Welcome to SMT Mart BD support. How may I assist you today?')"
                                            class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-800 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-300 text-slate-700 dark:text-slate-200 font-semibold text-[11px] transition border border-slate-200 dark:border-slate-700 shrink-0">
                                        👋 Welcome
                                    </button>
                                    <button type="button"
                                            @click="insertQuickReply('📦 Please provide your order number (e.g. ORD-1004) so I can verify its status.')"
                                            class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-800 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-300 text-slate-700 dark:text-slate-200 font-semibold text-[11px] transition border border-slate-200 dark:border-slate-700 shrink-0">
                                        📦 Order Check
                                    </button>
                                    <button type="button"
                                            @click="insertQuickReply('✅ I have updated your request! Is there anything else I can help you with?')"
                                            class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 dark:bg-slate-800 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-300 text-slate-700 dark:text-slate-200 font-semibold text-[11px] transition border border-slate-200 dark:border-slate-700 shrink-0">
                                        ✅ Resolved
                                    </button>
                                </div>

                                <form @submit.prevent="sendReply()" class="flex gap-2 items-end">
                                    <div class="relative flex-1">
                                        <textarea
                                            id="reply-input"
                                            rows="2"
                                            x-model="replyMessage"
                                            @keydown.enter.exact.prevent="sendReply()"
                                            placeholder="Type your response..."
                                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs font-medium text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition resize-none leading-relaxed"
                                        ></textarea>
                                        <div class="absolute bottom-2 right-2.5 text-[9px] font-mono text-slate-400 pointer-events-none"
                                             x-text="replyMessage.length > 0 ? replyMessage.length : ''"></div>
                                    </div>
                                    <button
                                        type="submit"
                                        :disabled="!replyMessage.trim() || isSending"
                                        class="h-10 w-10 sm:h-11 sm:w-auto sm:px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-xs shadow-md hover:shadow-emerald-600/30 transition flex items-center justify-center gap-1.5 cursor-pointer shrink-0 active:scale-95"
                                        title="Send message"
                                    >
                                        <template x-if="!isSending">
                                            <span class="flex items-center gap-1.5">
                                                <span class="hidden sm:inline">Send</span>
                                                <svg class="h-4 w-4 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                            </span>
                                        </template>
                                        <template x-if="isSending">
                                            <span class="inline-block animate-spin h-3.5 w-3.5 border-2 border-white border-t-transparent rounded-full"></span>
                                        </template>
                                    </button>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <template x-if="!activeSessionId">
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center;" class="text-slate-400 p-6 text-center">
                    <div class="w-14 h-14 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-2xl mb-3 shadow-2xs">💬</div>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white">No Conversation Selected</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs">Select a conversation from the list to start real-time messaging with your customer.</p>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
