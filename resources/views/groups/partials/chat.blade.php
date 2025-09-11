<div 
    x-data="{
        messages: [],
        newMessage: '',
        loading: true,
        groupId: {{ $group->id }},
        init() {
            fetch(`/groups/${this.groupId}/messages`)
                .then(res => res.json())
                .then(data => {
                    this.messages = data;
                    this.loading = false;
                    this.$nextTick(() => this.scrollToBottom());
                });

            Echo.private(`group.${this.groupId}`)
                .listen('NewMessage', (e) => {
                    this.messages.push(e);
                    this.$nextTick(() => this.scrollToBottom());
                });
        },
        sendMessage() {
            if (! this.newMessage.trim()) return;
            fetch(`/groups/${this.groupId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ content: this.newMessage })
            })
            .then(res => res.json())
            .then(() => {
                this.newMessage = '';
            });
        },
        scrollToBottom() {
            const container = this.$refs.chatContainer;
            container.scrollTop = container.scrollHeight;
        }
    }"
    x-init="init()"
    class="d-flex flex-column h-100 p-3 border rounded"
>
    <div class="flex-grow-1 overflow-auto mb-3" x-ref="chatContainer" style="max-height: 400px;">
        <template x-if="loading">
            <div class="text-center text-muted py-4">Chargement du chat...</div>
        </template>
        <template x-for="msg in messages" :key="msg.id">
            <div class="d-flex mb-2" :class="{'justify-content-end': msg.user.id === {{ auth()->id() }}}">
                <div class="d-flex align-items-start">
                    <img :src="msg.user.avatar_url" alt="" class="rounded-circle me-2" width="32" height="32">
                    <div class="bg-light p-2 rounded" :class="{'bg-primary text-white': msg.user.id === {{ auth()->id() }}}">
                        <div class="small">
                            <strong x-text="msg.user.name"></strong>
                            <span class="text-muted">•</span>
                            <span class="text-muted" x-text="new Date(msg.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })"></span>
                        </div>
                        <div x-text="msg.content" class="mt-1"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div>
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