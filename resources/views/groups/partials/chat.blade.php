<div 
    x-data="chatComponent({{ $group->id }}, '{{ csrf_token() }}', {{ auth()->id() }})"
    x-init="init()"
    class="d-flex flex-column h-100 p-3 border rounded"
>
    <!-- Historique des messages -->
    <div 
        class="flex-grow-1 overflow-auto mb-3" 
        x-ref="chatContainer" 
        style="max-height:400px;"
    >
        <template x-if="loading">
            <div class="text-center text-muted my-2">Chargement des messages...</div>
        </template>

        <template x-for="msg in messages" :key="msg.id">
            <div class="d-flex mb-2" :class="{'justify-content-end': msg.user.id === $data.userId}">
  <div class="p-2 rounded"
       :class="{
         'bg-chat-sent': msg.user.id === $data.userId,
         'bg-chat-received': msg.user.id !== $data.userId,
         'opacity-50': msg.sending
       }">
                <div class="d-flex align-items-start">
                    <!-- Avatar -->
                    <img 
                        :src="msg.user.avatar_url" 
                        class="rounded-circle me-2" 
                        width="32" height="32"
                    >

                    <!-- Bulle message -->
                    <div
                        class="p-2 rounded"
                        :class="{
                            'bg-chat-sent': msg.user.id === userId,
                            'bg-chat-received': msg.user.id !== userId,
                            'opacity-50': msg.sending
                        }"
                    >
                        <div class="small">
                            <strong x-text="msg.user.name"></strong>
                            <span class="text-muted">•</span>
                            <span class="text-muted" x-text="formatTime(msg.created_at)"></span>
                        </div>
                        <div x-text="msg.content" class="mt-1"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Input pour envoyer un message -->
    <div class="mt-auto">
        <div class="input-group">
            <input 
                type="text" 
                class="form-control" 
                placeholder="Écrire un message..." 
                x-model="newMessage"
                @keydown.enter.prevent="sendMessage()"
            >
            <button class="btn btn-primary" @click="sendMessage()">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>
</div>


<script>
function chatComponent(groupId, csrfToken) {
  return {
    messages: [],
    newMessage: '',
    loading: true,
    groupId,
    lastFetchedAt: null,

    init() {
      this.loadHistory()
        .then(() => {
          // Écoute WebSocket
          if (window.Echo) {
            Echo.private(`group.${this.groupId}`)
              .listen('NewMessage', e => {
                const m = e.message ?? e;
                this.messages.push({
                  id: m.id,
                  content: m.content,
                  user: m.user,
                  created_at: m.created_at
                });
                this.$nextTick(() => this.scrollToBottom());
              });
          }

          // Polling toutes les 10 sec pour récupérer d'éventuels manqués
          setInterval(() => this.fetchNewMessages(), 10000);
        });
    },

    async loadHistory() {
      try {
        const res = await fetch(`/groups/${this.groupId}/messages`, {
          headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }
        });
        const data = await res.json();
        this.messages = data;
        if (data.length) {
          this.lastFetchedAt = data[data.length - 1].created_at;
        }
        this.loading = false;
        this.$nextTick(() => this.scrollToBottom());
      } catch(err) {
        console.error('Historique chat :', err);
      }
    },

    async fetchNewMessages() {
      if (! this.lastFetchedAt) return;
      try {
        const res = await fetch(
          `/groups/${this.groupId}/messages?after=${encodeURIComponent(this.lastFetchedAt)}`,
          { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' } }
        );
        const newMsgs = await res.json();
        if (newMsgs.length) {
          newMsgs.forEach(m => this.messages.push(m));
          this.lastFetchedAt = newMsgs[newMsgs.length - 1].created_at;
          this.$nextTick(() => this.scrollToBottom());
        }
      } catch(err) {
        console.error('Fetch nouveaux messages :', err);
      }
    },

    sendMessage() {
    if (!this.newMessage.trim()) return;

    const content = this.newMessage.trim();

    // Ajouter le message localement pour qu'il s'affiche immédiatement
    this.messages.push({
        id: Date.now(), // ID temporaire
        content,
        user: {
            id: {{ auth()->id() }},
            name: 'Moi',
            avatar_url: '{{ auth()->user()->avatar_url ?? "/default-avatar.png" }}'
        },
        created_at: new Date().toISOString()
    });

    // Vider l'input et scroller vers le bas
    this.newMessage = '';
    this.$nextTick(() => this.scrollToBottom());

    // Envoi au serveur
    fetch(`/groups/${this.groupId}/messages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ content })
    })
    .catch(err => console.error('Envoi message :', err));
}
  }
}
</script>
