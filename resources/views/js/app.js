require('./bootstrap'); // Axios + Echo/Pusher

import Alpine from 'alpinejs';
window.Alpine = Alpine;

// === Composant chat ===
export function chatComponent(groupId, csrfToken, userId) {
    return {
        messages: [],
        newMessage: '',
        loading: true,
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
                    headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }
                });
                const data = await res.json();
                this.messages = data;
                if (data.length) this.lastFetchedAt = data[data.length - 1].created_at;
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            } catch(err) {
                console.error('Historique chat :', err);
            }
        },

        setupWebSocket() {
            if (!window.Echo) {
                console.warn('Echo non initialisé');
                return;
            }

            window.Echo.private(`group.${this.groupId}`)
                .listen('NewMessage', (e) => {
                    const m = e.message || e;

                    // Vérifie doublons par id serveur
                    const exists = this.messages.some(msg => msg.id === m.id);
                    if (exists) return;

                    // Remplace le message temporaire si content identique et sending
                    const tempIndex = this.messages.findIndex(msg => msg.sending && msg.content === m.content);
                    if (tempIndex !== -1) {
                        this.messages.splice(tempIndex, 1, m); // remplace le temporaire
                    } else {
                        this.messages.push(m); // sinon ajoute normalement
                    }

                    this.$nextTick(() => this.scrollToBottom());
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
                user: {
                    id: this.userId,
                    name: 'Moi',
                    avatar_url: '{{ auth()->user()->avatar_url ?? "/default-avatar.png" }}'
                },
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
                        'Content-Type':'application/json',
                        'Accept':'application/json',
                        'X-Requested-With':'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ content })
                });

                if (!res.ok) throw new Error(await res.text());
                const savedMsg = await res.json();

                // Remplace le message temporaire par le vrai message
                const index = this.messages.findIndex(m => m.id === tempMsg.id);
                if (index !== -1) this.messages.splice(index, 1, savedMsg);
                this.lastFetchedAt = savedMsg.created_at;

            } catch(err) {
                console.error('Envoi message :', err);
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
            return new Date(dt).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }
    };
}

// === Enregistrement Alpine ===
Alpine.data('chatComponent', () => chatComponent(
    document.body.getAttribute('data-group-id'),
    document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    document.body.getAttribute('data-user-id')
));

Alpine.start();
