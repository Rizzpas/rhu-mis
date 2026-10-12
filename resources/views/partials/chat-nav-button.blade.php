{{-- Chat / Messages Navbar Button --}}
{{-- Positioned between the Dark Mode Toggle and Account Avatar Menu --}}
@auth
<div class="relative shrink-0"
     x-data="{
         get totalUnread() {
             return this.$store?.chat?.totalUnread ?? (window.Alpine?.store('chat')?.totalUnread ?? 0);
         },
         get isOpen() {
             return this.$store?.chat?.panelOpen ?? (window.Alpine?.store('chat')?.panelOpen ?? false);
         },
         toggleChat() {
             if (this.$store?.chat?.toggle) {
                 this.$store.chat.toggle();
             } else if (window.Alpine?.store('chat')?.toggle) {
                 window.Alpine.store('chat').toggle();
             } else {
                 window.dispatchEvent(new CustomEvent('chat-toggle'));
             }
         }
     }">
    <button type="button"
            id="navbar-chat-btn"
            @click="toggleChat()"
            class="navbar-chat-btn group relative w-10 h-10 shrink-0 cursor-pointer rounded-full bg-white/10 hover:bg-white/20 active:bg-white/25 dark:bg-emerald-950/60 dark:hover:bg-emerald-950/90 text-white backdrop-blur-md ring-1 ring-white/30 hover:ring-white/60 dark:ring-white/15 dark:hover:ring-emerald-400/50 shadow-xs hover:shadow-md transition-all duration-200 active:scale-95 flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 select-none"
            :class="isOpen ? 'ring-2 ring-white ring-offset-2 ring-offset-emerald-600 dark:ring-offset-emerald-800 scale-102 bg-white/20 shadow-md' : ''"
            aria-label="Staff Messages"
            title="Staff Messages">
        <span class="sr-only">Staff Messages</span>

        {{-- Chat Message Bubble Icon --}}
        <svg class="w-5 h-5 text-white/95 group-hover:text-white transition-all duration-200 group-hover:scale-110 drop-shadow-[0_1px_2px_rgba(0,0,0,0.15)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>

        {{-- Red Unread Count Badge with Cutout Ring --}}
        <span x-show="totalUnread > 0 && !isOpen"
              x-text="totalUnread > 99 ? '99+' : totalUnread"
              x-cloak
              style="display: none;"
              class="absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center px-1 text-[10px] font-black text-white bg-rose-500 rounded-full shadow-xs ring-2 ring-emerald-600 dark:ring-emerald-800/95 animate-pulse">
        </span>
    </button>
</div>
@endauth
