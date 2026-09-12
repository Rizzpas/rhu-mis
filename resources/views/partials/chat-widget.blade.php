{{-- Real-Time Staff Chat Widget --}}
{{-- Floating chat bubble + slide-out panel for 1:1 messaging --}}
@auth
<div id="chat-widget-root"
     x-data="chatWidget()"
     x-init="init()"
     x-cloak
     class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[9999]"
     @keydown.escape.window="if(panelOpen) closePanel()">

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- CHAT BUBBLE BUTTON --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <button @click="togglePanel()"
            id="chat-bubble-btn"
            class="group relative w-14 h-14 rounded-full bg-slate-800 dark:bg-slate-700 text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 flex items-center justify-center"
            :class="panelOpen ? 'ring-2 ring-slate-500 dark:ring-slate-400' : ''">
        {{-- Chat icon --}}
        <svg x-show="!panelOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        {{-- Close icon --}}
        <svg x-show="panelOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{-- Unread badge --}}
        <span x-show="totalUnread > 0 && !panelOpen"
              x-text="totalUnread > 99 ? '99+' : totalUnread"
              class="absolute -top-1 -right-1 min-w-[22px] h-[22px] flex items-center justify-center px-1.5 text-xs font-bold text-white bg-red-500 rounded-full shadow-md animate-pulse">
        </span>
    </button>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- CHAT PANEL --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div x-show="panelOpen"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed inset-x-3 bottom-[76px] sm:bottom-[72px] sm:right-0 sm:left-auto sm:inset-x-auto sm:absolute w-auto sm:w-[380px] max-w-[420px] mx-auto sm:mx-0 h-[calc(100dvh-92px)] sm:h-[560px] max-h-[580px] bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-700/80 flex flex-col overflow-hidden"
         style="display: none;">

        {{-- ─── CONVERSATION LIST VIEW ─── --}}
        <template x-if="currentView === 'list'">
            <div class="flex flex-col h-full">
                {{-- Header --}}
                <div class="px-4 py-3 bg-emerald-600 dark:bg-slate-800 border-b border-emerald-700/30 dark:border-slate-700/80 flex items-center justify-between shrink-0 text-white">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-white/15 dark:bg-slate-700 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-white">Messages</h2>
                        <span x-show="totalUnread > 0"
                              x-text="totalUnread"
                              class="px-1.5 py-0.5 text-[11px] font-bold bg-white/25 dark:bg-emerald-500/80 text-white rounded-full"></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button @click="showNewChat()"
                                class="p-1.5 rounded-lg bg-white/15 hover:bg-white/25 text-white dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 transition-colors"
                                title="New message">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L11.582 15.07a4.5 4.5 0 01-1.897 1.13L6 17l.8-3.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                        </button>
                        <button @click="closePanel()"
                                class="p-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/15 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 transition-colors"
                                title="Close chat">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Conversation Search --}}
                <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="conversationSearch" placeholder="Search conversations..." class="no-uppercase w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-emerald-500 dark:focus:border-slate-500 transition-colors">
                    </div>
                </div>

                {{-- Conversation Items --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <template x-if="filteredConversations.length === 0 && !loadingConversations">
                        <div class="flex flex-col items-center justify-center h-full px-6 text-center">
                            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No conversations yet</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Start a new message to a colleague</p>
                            <button @click="showNewChat()" class="mt-4 px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 dark:bg-slate-600 dark:hover:bg-slate-500 rounded-xl transition-colors">
                                New Message
                            </button>
                        </div>
                    </template>

                    <template x-if="loadingConversations">
                        <div class="p-6 flex items-center justify-center">
                            <svg class="w-6 h-6 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </template>

                    <template x-for="conv in filteredConversations" :key="conv.id">
                        <button @click="openConversation(conv)"
                                class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors border-b border-slate-50 dark:border-slate-800/50"
                                :class="conv.unread_count > 0 ? 'bg-slate-50/60 dark:bg-slate-800/30' : ''">
                            {{-- Avatar --}}
                            <div class="relative shrink-0">
                                <div class="w-11 h-11 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden">
                                    <template x-if="conv.other_user?.avatar_url">
                                        <img :src="conv.other_user.avatar_url" :alt="conv.other_user.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!conv.other_user?.avatar_url">
                                        <span class="text-sm font-bold text-slate-500 dark:text-slate-300" x-text="conv.other_user?.initials || '?'"></span>
                                    </template>
                                </div>
                                {{-- Online indicator --}}
                                <div x-show="conv.other_user?.is_present"
                                     class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
                            </div>
                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-0.5">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white truncate" x-text="conv.other_user?.name || 'Unknown'" :class="conv.unread_count > 0 ? 'font-bold' : 'font-semibold'"></p>
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 shrink-0 ml-2" x-text="formatTimestamp(conv.latest_message?.created_at || conv.updated_at)"></span>
                                </div>
                                <div class="flex items-center justify-between gap-1.5">
                                    <p class="text-xs truncate flex-1 min-w-0"
                                       :class="conv.unread_count > 0 ? 'text-slate-700 dark:text-slate-200 font-medium' : 'text-slate-500 dark:text-slate-400'"
                                       x-text="conv.latest_message ? (conv.latest_message.sender_id == currentUserId ? 'You: ' : '') + conv.latest_message.body : 'No messages yet'"></p>
                                    <span x-show="conv.unread_count > 0"
                                          x-text="conv.unread_count"
                                          class="ml-1 min-w-[18px] h-[18px] flex items-center justify-center px-1.5 text-[10px] font-bold text-white bg-emerald-600 dark:bg-slate-500 rounded-full shrink-0"></span>
                                </div>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 capitalize mt-0.5 truncate" x-text="conv.other_user?.role?.replace(/_/g, ' ') || ''"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        {{-- ─── NEW CHAT VIEW ─── --}}
        <template x-if="currentView === 'new'">
            <div class="flex flex-col h-full">
                {{-- Header --}}
                <div class="px-4 py-3 bg-emerald-600 dark:bg-slate-800 border-b border-emerald-700/30 dark:border-slate-700/80 flex items-center justify-between shrink-0 text-white">
                    <div class="flex items-center gap-2 min-w-0">
                        <button @click="currentView = 'list'"
                                class="p-1.5 rounded-lg text-white/90 hover:text-white hover:bg-white/15 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 transition-colors"
                                title="Back to messages">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <h2 class="text-base font-bold text-white truncate">New Message</h2>
                    </div>
                    <button @click="closePanel()"
                            class="p-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/15 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 transition-colors"
                            title="Close chat">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- User Search --}}
                <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="userSearch" @input.debounce.300ms="searchUsers()" placeholder="Search staff by name or role..." class="no-uppercase w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-emerald-500 dark:focus:border-slate-500 transition-colors" autofocus>
                    </div>
                </div>

                {{-- User List --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <template x-if="loadingUsers">
                        <div class="p-6 flex items-center justify-center">
                            <svg class="w-6 h-6 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </template>
                    <template x-for="user in availableUsers" :key="user.id">
                        <button @click="startConversation(user)"
                                class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors border-b border-slate-50 dark:border-slate-800/50">
                            <div class="relative shrink-0">
                                <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden">
                                    <template x-if="user.avatar_url">
                                        <img :src="user.avatar_url" :alt="user.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!user.avatar_url">
                                        <span class="text-sm font-bold text-slate-500 dark:text-slate-300" x-text="user.initials"></span>
                                    </template>
                                </div>
                                <div x-show="user.is_present" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-white truncate" x-text="user.name"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 capitalize truncate" x-text="user.role_label"></p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </template>
                    <template x-if="availableUsers.length === 0 && !loadingUsers && userSearch.length > 0">
                        <div class="p-6 text-center">
                            <p class="text-sm text-slate-500 dark:text-slate-400">No staff found</p>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- ─── CHAT VIEW ─── --}}
        <template x-if="currentView === 'chat'">
            <div class="flex flex-col h-full">
                {{-- Chat Header --}}
                <div class="px-4 py-3 bg-emerald-600 dark:bg-slate-800 border-b border-emerald-700/30 dark:border-slate-700/80 flex items-center justify-between gap-2 shrink-0 text-white">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <button @click="backToList()"
                                class="p-1.5 rounded-lg text-white/90 hover:text-white hover:bg-white/15 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 transition-colors shrink-0"
                                title="Back to messages">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <div class="relative shrink-0">
                            <div class="w-9 h-9 rounded-full bg-white/20 dark:bg-slate-700 flex items-center justify-center overflow-hidden ring-1 ring-white/30 dark:ring-slate-600">
                                <template x-if="activeChat?.other_user?.avatar_url">
                                    <img :src="activeChat.other_user.avatar_url" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!activeChat?.other_user?.avatar_url">
                                    <span class="text-xs font-bold text-white dark:text-slate-200" x-text="activeChat?.other_user?.initials || '?'"></span>
                                </template>
                            </div>
                            <div x-show="activeChat?.other_user?.is_present" class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-300 border-2 border-emerald-600 dark:border-slate-900 rounded-full"></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-white truncate" x-text="activeChat?.other_user?.name || 'Chat'"></p>
                            <p class="text-[11px] capitalize truncate"
                               :class="activeChat?.other_user?.is_present ? 'text-emerald-100 dark:text-emerald-400 font-medium' : 'text-emerald-100/75 dark:text-slate-400'"
                               x-text="isTyping ? 'typing...' : (activeChat?.other_user?.is_present ? 'Online' : activeChat?.other_user?.role?.replace(/_/g, ' ') || '')"></p>
                        </div>
                    </div>
                    <button @click="closePanel()"
                            class="p-1.5 rounded-lg text-white/80 hover:text-white hover:bg-white/15 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 transition-colors shrink-0"
                            title="Close chat">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar px-3 sm:px-4 py-3 space-y-1" id="chat-messages-container" x-ref="messagesContainer" @scroll="handleScroll()">
                    {{-- Load more --}}
                    <div x-show="hasMoreMessages" class="text-center py-2">
                        <button @click="loadMoreMessages()" class="text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors" :disabled="loadingMessages">
                            <span x-show="!loadingMessages">Load earlier messages</span>
                            <svg x-show="loadingMessages" class="w-4 h-4 animate-spin mx-auto text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>

                    {{-- Loading initial --}}
                    <template x-if="loadingMessages && messages.length === 0">
                        <div class="flex items-center justify-center h-full">
                            <svg class="w-6 h-6 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="messages.length === 0 && !loadingMessages">
                        <div class="flex flex-col items-center justify-center h-full text-center">
                            <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                                <svg class="w-7 h-7 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No messages yet</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Send a message to start the conversation</p>
                        </div>
                    </template>

                    {{-- Message bubbles --}}
                    <template x-for="(msg, index) in messages" :key="msg.id">
                        <div>
                            {{-- Date separator --}}
                            <template x-if="shouldShowDate(index)">
                                <div class="flex items-center justify-center my-3">
                                    <span class="text-[10px] font-medium text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full" x-text="formatDateLabel(msg.created_at)"></span>
                                </div>
                            </template>
                            {{-- Message --}}
                            <div class="flex mb-1" :class="msg.sender_id == currentUserId ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[85%] sm:max-w-[80%]">
                                    <div class="px-3.5 py-2 rounded-2xl text-sm leading-relaxed break-words"
                                         :class="msg.sender_id == currentUserId
                                            ? 'bg-slate-800 dark:bg-slate-600 text-white rounded-br-md'
                                            : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-bl-md'"
                                         x-text="msg.body">
                                    </div>
                                    <div class="flex items-center gap-1 mt-0.5 px-1" :class="msg.sender_id == currentUserId ? 'justify-end' : 'justify-start'">
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500" x-text="formatMessageTime(msg.created_at)"></span>
                                        {{-- Read receipt for own messages --}}
                                        <template x-if="msg.sender_id == currentUserId">
                                            <span class="text-[10px]" :class="isMessageRead(msg) ? 'text-emerald-500' : 'text-slate-400 dark:text-slate-500'">
                                                <svg x-show="isMessageRead(msg)" class="w-3.5 h-3.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M1.5 12.75l6 6"/></svg>
                                                <svg x-show="!isMessageRead(msg)" class="w-3.5 h-3.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Typing indicator --}}
                    <div x-show="isTyping" class="flex justify-start mb-1">
                        <div class="bg-slate-100 dark:bg-slate-800 px-4 py-2.5 rounded-2xl rounded-bl-md flex items-center gap-1">
                            <div class="w-2 h-2 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                            <div class="w-2 h-2 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                            <div class="w-2 h-2 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                        </div>
                    </div>
                </div>

                {{-- Input area --}}
                <div class="px-3 py-2.5 sm:py-3 border-t border-slate-100 dark:border-slate-800 shrink-0 bg-white dark:bg-slate-900">
                    <form @submit.prevent="sendMessage()" class="flex items-end gap-2">
                        <div class="flex-1 relative">
                            <textarea x-model="messageInput"
                                      @input="handleTyping(); autoResize($event.target)"
                                      @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                                      placeholder="Type a message..."
                                      rows="1"
                                      class="chat-input-textarea no-uppercase w-full px-3.5 sm:px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-emerald-500 dark:focus:border-slate-500 resize-none transition-colors"
                                      style="min-height: 42px; max-height: 180px; height: 42px; overflow-y: hidden;"
                                      x-ref="messageTextarea"></textarea>
                        </div>
                        <button type="submit"
                                :disabled="!messageInput.trim() || sendingMessage"
                                class="p-2.5 rounded-2xl text-white transition-all duration-200 shrink-0 h-[42px] w-[42px] flex items-center justify-center self-end"
                                :class="messageInput.trim() && !sendingMessage
                                    ? 'bg-emerald-600 hover:bg-emerald-700 dark:bg-slate-600 dark:hover:bg-slate-500 cursor-pointer'
                                    : 'bg-slate-300 dark:bg-slate-700 cursor-not-allowed'">
                            <svg x-show="!sendingMessage" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                            <svg x-show="sendingMessage" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatWidget', () => ({
        // State
        panelOpen: false,
        currentView: 'list',
        currentUserId: {{ auth()->id() }},

        // Conversations
        conversations: [],
        conversationSearch: '',
        loadingConversations: false,

        // New chat
        availableUsers: [],
        userSearch: '',
        loadingUsers: false,

        // Active chat
        activeChat: null,
        messages: [],
        messageInput: '',
        sendingMessage: false,
        loadingMessages: false,
        hasMoreMessages: false,
        nextCursor: null,

        // Real-time
        totalUnread: 0,
        isTyping: false,
        typingTimeout: null,
        whisperTimeout: null,
        echoChannel: null,
        otherUserLastReadAt: null,

        // Polling
        pollTimer: null,

        init() {
            // Immediately fetch conversations on load so unread badge and list are ready
            this.fetchConversations(true);

            // Live continuous poll every 2 seconds:
            // - If in a conversation: polls new messages
            // - If in conversations list or closed: polls conversations list & unread count
            this.pollTimer = setInterval(() => {
                if (this.currentView === 'chat' && this.activeChat) {
                    this.pollNewMessages();
                } else {
                    this.fetchConversations(true);
                }
            }, 2000);

            // When user switches back to this browser tab, refresh immediately
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    if (this.currentView === 'chat' && this.activeChat) {
                        this.pollNewMessages();
                    } else {
                        this.fetchConversations(true);
                    }
                }
            });
        },

        togglePanel() {
            this.panelOpen = !this.panelOpen;
            if (this.panelOpen) {
                this.fetchConversations(false);
            } else {
                this.closePanel();
            }
        },

        closePanel() {
            this.panelOpen = false;
            this.leaveChannel();
            this.currentView = 'list';
            this.activeChat = null;
            this.messages = [];
        },

        // ─── CONVERSATIONS ───
        async fetchConversations(silent = false) {
            if (!silent) this.loadingConversations = true;
            try {
                const res = await fetch('/chat/conversations', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (res.ok) {
                    const data = await res.json();
                    const prevUnread = this.totalUnread;
                    this.conversations = data;
                    this.updateTotalUnread();

                    // Play sound if a new unread message arrived
                    if (prevUnread !== null && this.totalUnread > prevUnread) {
                        this.playNotificationSound();
                    }
                }
            } catch (e) {
                if (!silent) console.error('Failed to fetch conversations:', e);
            }
            if (!silent) this.loadingConversations = false;
        },

        get filteredConversations() {
            if (!this.conversationSearch.trim()) return this.conversations;
            const q = this.conversationSearch.toLowerCase();
            return this.conversations.filter(c =>
                c.other_user?.name?.toLowerCase().includes(q) ||
                c.other_user?.role?.toLowerCase().includes(q)
            );
        },

        // ─── NEW CHAT ───
        showNewChat() {
            this.currentView = 'new';
            this.userSearch = '';
            this.searchUsers();
        },

        async searchUsers() {
            this.loadingUsers = true;
            try {
                const res = await fetch(`/chat/users?search=${encodeURIComponent(this.userSearch)}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                this.availableUsers = await res.json();
            } catch (e) {
                console.error('Failed to search users:', e);
            }
            this.loadingUsers = false;
        },

        async startConversation(user) {
            try {
                const res = await fetch('/chat/conversations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ user_id: user.id })
                });
                const conv = await res.json();
                this.openConversation(conv);
            } catch (e) {
                console.error('Failed to start conversation:', e);
            }
        },

        // ─── CHAT VIEW ───
        async openConversation(conv) {
            this.activeChat = conv;
            this.currentView = 'chat';
            this.messages = [];
            this.nextCursor = null;
            this.hasMoreMessages = false;
            this.isTyping = false;
            this.otherUserLastReadAt = null;

            await this.fetchMessages();
            this.markAsRead();
            this.joinChannel(conv.id);
            this.messageInput = '';
            this.$nextTick(() => {
                this.scrollToBottom();
                if (this.$refs.messageTextarea) {
                    this.autoResize(this.$refs.messageTextarea);
                }
            });
        },

        backToList() {
            this.leaveChannel();
            this.currentView = 'list';
            this.activeChat = null;
            this.messages = [];
            this.fetchConversations();
        },

        startActiveChatPoll() {
            this.stopActiveChatPoll();
            this.activeChatPollInterval = setInterval(async () => {
                if (this.activeChat && this.currentView === 'chat') {
                    await this.pollNewMessages();
                }
            }, 3000);
        },

        stopActiveChatPoll() {
            if (this.activeChatPollInterval) {
                clearInterval(this.activeChatPollInterval);
                this.activeChatPollInterval = null;
            }
        },

        async pollNewMessages() {
            if (!this.activeChat || this.loadingMessages || this.sendingMessage) return;
            try {
                const res = await fetch(`/chat/conversations/${this.activeChat.id}/messages`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (!res.ok) return;
                const data = await res.json();
                const remoteMsgs = data.data || [];

                if (data.other_user_last_read_at) {
                    this.otherUserLastReadAt = data.other_user_last_read_at;
                }

                let hasNew = false;
                const existingIds = new Set(this.messages.map(m => m.id));

                const reversed = [...remoteMsgs].reverse();
                for (const msg of reversed) {
                    if (!existingIds.has(msg.id)) {
                        this.messages.push(msg);
                        hasNew = true;
                    }
                }

                if (hasNew) {
                    this.$nextTick(() => this.scrollToBottom());
                    this.markAsRead();
                    this.playNotificationSound();
                }

                // Keep this conversation's preview up to date in this.conversations
                if (remoteMsgs.length > 0) {
                    const latest = remoteMsgs[0];
                    const conv = this.conversations.find(c => c.id === this.activeChat.id);
                    if (conv) {
                        conv.latest_message = {
                            body: latest.body,
                            sender_id: latest.sender_id,
                            sender_name: latest.sender_name,
                            created_at: latest.created_at,
                            type: latest.type,
                        };
                        conv.updated_at = latest.created_at;
                    }
                }
            } catch (e) { /* silent */ }
        },

        async fetchMessages() {
            this.loadingMessages = true;
            try {
                let url = `/chat/conversations/${this.activeChat.id}/messages`;
                if (this.nextCursor) url += `?cursor=${this.nextCursor}`;

                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();

                if (data.other_user_last_read_at) {
                    this.otherUserLastReadAt = data.other_user_last_read_at;
                }

                // Messages come newest-first from API, we reverse for display
                const newMsgs = data.data.reverse();
                this.messages = [...newMsgs, ...this.messages];
                this.nextCursor = data.next_cursor;
                this.hasMoreMessages = data.has_more;
            } catch (e) {
                console.error('Failed to fetch messages:', e);
            }
            this.loadingMessages = false;
        },

        async loadMoreMessages() {
            const container = this.$refs.messagesContainer;
            const prevHeight = container?.scrollHeight || 0;
            await this.fetchMessages();
            this.$nextTick(() => {
                if (container) {
                    container.scrollTop = container.scrollHeight - prevHeight;
                }
            });
        },

        async sendMessage() {
            const body = this.messageInput.trim();
            if (!body || this.sendingMessage) return;

            this.sendingMessage = true;
            const optimisticMsg = {
                id: 'temp-' + Date.now(),
                conversation_id: this.activeChat.id,
                sender_id: this.currentUserId,
                body: body,
                type: 'text',
                created_at: new Date().toISOString(),
                _pending: true,
            };
            this.messages.push(optimisticMsg);
            this.messageInput = '';
            this.$nextTick(() => {
                this.scrollToBottom();
                this.autoResize(this.$refs.messageTextarea);
            });

            try {
                const res = await fetch(`/chat/conversations/${this.activeChat.id}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ body: body })
                });
                const msg = await res.json();
                // Replace optimistic message
                const idx = this.messages.findIndex(m => m.id === optimisticMsg.id);
                if (idx !== -1) this.messages[idx] = msg;
            } catch (e) {
                console.error('Failed to send message:', e);
                // Remove optimistic on failure
                this.messages = this.messages.filter(m => m.id !== optimisticMsg.id);
                this.messageInput = body;
            }
            this.sendingMessage = false;
        },

        async markAsRead() {
            if (!this.activeChat) return;
            try {
                await fetch(`/chat/conversations/${this.activeChat.id}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                // Update local unread count
                const conv = this.conversations.find(c => c.id === this.activeChat.id);
                if (conv) conv.unread_count = 0;
                this.updateTotalUnread();
            } catch (e) { /* silent */ }
        },

        // ─── ECHO / REAL-TIME ───
        joinChannel(conversationId) {
            this.leaveChannel();
            if (!window.Echo) return;

            this.echoChannel = window.Echo.private(`chat.${conversationId}`);

            this.echoChannel.listen('MessageSent', (e) => {
                // Don't duplicate own messages
                if (e.sender_id === this.currentUserId) return;

                this.messages.push(e);
                this.$nextTick(() => this.scrollToBottom());
                this.markAsRead();

                // Update conversation list preview
                const conv = this.conversations.find(c => c.id === e.conversation_id);
                if (conv) {
                    conv.latest_message = { body: e.body, sender_id: e.sender_id, sender_name: e.sender_name, created_at: e.created_at, type: e.type };
                }
            });

            this.echoChannel.listen('MessageRead', (e) => {
                if (e.user_id !== this.currentUserId) {
                    this.otherUserLastReadAt = e.last_read_at;
                }
            });

            // Typing indicator via whispers
            this.echoChannel.listenForWhisper('typing', (e) => {
                if (e.userId !== this.currentUserId) {
                    this.isTyping = true;
                    clearTimeout(this.typingTimeout);
                    this.typingTimeout = setTimeout(() => { this.isTyping = false; }, 2000);
                }
            });
        },

        leaveChannel() {
            if (this.echoChannel && window.Echo) {
                window.Echo.leave(`chat.${this.activeChat?.id}`);
                this.echoChannel = null;
            }
        },

        handleTyping() {
            if (!this.echoChannel) return;
            clearTimeout(this.whisperTimeout);
            this.whisperTimeout = setTimeout(() => {
                this.echoChannel.whisper('typing', { userId: this.currentUserId });
            }, 300);
        },

        // ─── UNREAD ───
        async fetchUnreadCount() {
            try {
                const res = await fetch('/chat/unread-count', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (res.ok) {
                    const data = await res.json();
                    this.totalUnread = data.count;
                }
            } catch (e) { /* silent */ }
        },

        updateTotalUnread() {
            this.totalUnread = this.conversations.reduce((sum, c) => sum + (c.unread_count || 0), 0);
        },

        // ─── HELPERS ───
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        handleScroll() {
            // Could add "load more on scroll to top" here
        },

        autoResize(el) {
            if (!el) return;
            el.style.height = 'auto';
            const maxHeight = 180;
            if (!this.messageInput || this.messageInput.trim() === '') {
                el.style.height = '42px';
                el.style.overflowY = 'hidden';
                return;
            }
            if (el.scrollHeight > maxHeight) {
                el.style.height = maxHeight + 'px';
                el.style.overflowY = 'auto';
            } else {
                el.style.height = Math.max(el.scrollHeight, 42) + 'px';
                el.style.overflowY = 'hidden';
            }
        },

        isMessageRead(msg) {
            if (!this.otherUserLastReadAt) return false;
            return new Date(msg.created_at) <= new Date(this.otherUserLastReadAt);
        },

        shouldShowDate(index) {
            if (index === 0) return true;
            const curr = new Date(this.messages[index].created_at).toDateString();
            const prev = new Date(this.messages[index - 1].created_at).toDateString();
            return curr !== prev;
        },

        formatDateLabel(dateStr) {
            const d = new Date(dateStr);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);

            if (d.toDateString() === today.toDateString()) return 'Today';
            if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: d.getFullYear() !== today.getFullYear() ? 'numeric' : undefined });
        },

        formatMessageTime(dateStr) {
            return new Date(dateStr).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        },

        formatTimestamp(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const now = new Date();
            const diff = (now - d) / 1000;

            if (diff < 60) return 'now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm';
            if (diff < 86400 && d.toDateString() === now.toDateString()) {
                return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            }
            if (diff < 604800) {
                return d.toLocaleDateString('en-US', { weekday: 'short' });
            }
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        },

        playNotificationSound() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.08);
                gain.gain.setValueAtTime(0.06, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.22);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.22);
            } catch (e) { /* ignore audio policy */ }
        },
    }));
});
</script>

<style>
    .chat-input-textarea {
        scrollbar-width: thin;
        scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
    }
    .dark .chat-input-textarea {
        scrollbar-color: rgba(100, 116, 139, 0.5) transparent;
    }
    .chat-input-textarea::-webkit-scrollbar {
        width: 5px;
    }
    .chat-input-textarea::-webkit-scrollbar-track {
        background: transparent;
        margin: 6px 0;
    }
    .chat-input-textarea::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.4);
        border-radius: 9999px;
    }
    .chat-input-textarea::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.7);
    }
    .dark .chat-input-textarea::-webkit-scrollbar-thumb {
        background: rgba(100, 116, 139, 0.5);
    }
    .dark .chat-input-textarea::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.8);
    }
</style>
@endauth
