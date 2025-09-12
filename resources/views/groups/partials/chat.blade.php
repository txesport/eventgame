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
function chatComponent(groupId, csrfToken, currentUser) {
  return {
    messages: [],
    newMessage: '',
    loading: true,
    groupId,
    userId: currentUser.id,
    user: currentUser,
    lastFetchedAt: null,

    init() {
      this.loadHistory().then(() => {
        if (window.Echo) {
          Echo.private(`group.${this.groupId}`)
            .listen('NewMessage', e => {
              const m = e.message ?? e;
              this.ingestMessage(m);
            });
        }
        setInterval(() => this.fetchNewMessages(), 10000);
      });
    },

    ingestMessage(m) {
      if (this.messages.some(x => x.id === m.id)) return;
      
      // Remplace un message temporaire de même contenu
      const idxTemp = this.messages.findIndex(x => x.sending && x.content === m.content && x.user.id === m.user.id);
      if (idxTemp !== -1) {
        this.messages.splice(idxTemp, 1, m);
      } else {
        this.messages.push(m);
      }
      this.lastFetchedAt = m.created_at;
      this.$nextTick(() => this.scrollToBottom());
    },

    async loadHistory() {
      try {
        const res = await fetch(`/groups/${this.groupId}/messages`, {
          headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        this.messages = data;
        if (data.length) this.lastFetchedAt = data[data.length - 1].created_at;
      } catch (err) {
        console.error('Historique chat :', err);
      } finally {
        this.loading = false;
        this.$nextTick(() => this.scrollToBottom());
      }
    },

    async fetchNewMessages() {
      if (!this.lastFetchedAt) return;
      try {
        const res = await fetch(`/groups/${this.groupId}/messages?after=${encodeURIComponent(this.lastFetchedAt)}`, {
          headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const newMsgs = await res.json();
        if (newMsgs.length) newMsgs.forEach(m => this.ingestMessage(m));
      } catch (err) {
        console.error('Fetch nouveaux messages :', err);
      }
    },

    async sendMessage() {
      if (!this.newMessage.trim()) return;
      const content = this.newMessage.trim();

      const temp = {
        id: `tmp_${Date.now()}`,
        content,
        user: this.user,
        created_at: new Date().toISOString(),
        sending: true
      };
      this.messages.push(temp);
      this.lastFetchedAt = temp.created_at;
      this.newMessage = '';
      this.$nextTick(() => this.scrollToBottom());

      try {
        const res = await fetch(`/groups/${this.groupId}/messages`, {
          method: 'POST',
          headers: {
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-Requested-With':'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'X-Socket-Id': window.Echo ? window.Echo.socketId() : ''
          },
          body: JSON.stringify({ content })
        });
        if (!res.ok) throw new Error(`HTTP ${res.status} ${await res.text()}`);
        const saved = await res.json();
        const idx = this.messages.findIndex(m => m.id === temp.id);
        if (idx !== -1) this.messages.splice(idx, 1, saved);
        this.lastFetchedAt = saved.created_at;
      } catch (err) {
        console.error('Envoi message :', err);
        const idx = this.messages.findIndex(m => m.id === temp.id);
        if (idx !== -1) this.messages.splice(idx, 1);
        this.newMessage = content;
      }
    },

    scrollToBottom() {
      const el = this.$refs.chatContainer;
      if (!el) return;
      el.scrollTop = el.scrollHeight;
    },

    formatTime(iso) {
      return new Date(iso).toLocaleString('fr-FR', { hour:'2-digit', minute:'2-digit' });
    }
  }
}
</script>

