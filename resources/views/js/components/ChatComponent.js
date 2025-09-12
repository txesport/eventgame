// File: resources/js/chatComponent.js
import Alpine from 'alpinejs';
import './bootstrap'; // ton bootstrap.js pour Echo / Pusher

export function chatComponent(groupId, csrfToken, userId) {
    return {
        messages: [],
        newMessage: '',
        sending: false,
        groupId,
        userId,
        lastFetchedAt: null,

        init() {
            this.loadHistory()
                .then(() => this.setupWebSocket())
                .catch(console.error);
        },

        async loadHistory() {
            try {
                const res = await fetch(`/groups/${this.groupId}/messages`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error(`Status ${res.status}`);
                const data = await res.json();
                this.messages = data;
                if (data.length) this.lastFetchedAt = data[data.length - 1].created_at;
                this.$nextTick(() => this.scrollToBottom());
            } catch(err) {
                console.error('Erreur loadHistory:', err);
            }
        },

        setupWebSocket() {
            if (!window.Echo) {
                console.warn('Echo non initialisé');
                return;
            }
            window.Echo.private(`group.${this.groupId}`)
                .listen('NewMessage', e => {
                    const m = e.message || e;
                    // Éviter doublons
                    if (!this.messages.some(msg => msg.id === m.id)) {
                        this.messages.push(m);
                        this.$nextTick(() => this.scrollToBottom());
                    }
                });
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;
            this.sending = true;

            const content = this.newMessage.trim();
            // Message temporaire
            const tempMsg = {
                id: Date.now(),
                content,
                user: { id: this.userId, name: 'Moi', avatar_url: '/default-avatar.png' },
                created_at: new Date().toISOString(),
                sending: true
            };
            this.messages.push(tempMsg);
            this.newMessage = '';
            this.$nextTick(() => this.scrollToBottom());

            try {
                const res = await fetch(`/groups/${this.groupId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ content })
                });
                if (!res.ok) throw new Error(await res.text());
                const savedMsg = await res.json();

                // Remplacer message temporaire
                const index = this.messages.findIndex(m => m.id === tempMsg.id);
                if (index !== -1) this.messages.splice(index, 1, savedMsg);
            } catch(err) {
                console.error('Erreur sendMessage:', err);
                tempMsg.user.name = 'Erreur';
                tempMsg.sending = false;
            } finally {
                this.sending = false;
            }
        },

        scrollToBottom() {
            const c = this.$refs.chatContainer;
            if (c) c.scrollTop = c.scrollHeight;
        },

        formatTime(dt) {
            return new Date(dt).toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    };
}

// Enregistrer pour Alpine
Alpine.data('chatComponent', (groupId, csrfToken, userId) => chatComponent(groupId, csrfToken, userId));
