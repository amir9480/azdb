<template>
    <div>
        <div class="ticket-view">
            <div class="msg-list">
                <div v-for="(message, index) in messages" :key="index" class="msg-item" :class="{admin: message.user_id != ticket.user_id}">
                    <div class="msg-info">
                        <i class="far fa-user-headset icon"></i>
                        <span>{{ message.created_at_diff }}</span>
                    </div>
                    <div class="msg-content">
                        <span v-if="message.user.id != ticket.user.id" class="name">پاسخ پشتیبانی</span>
                        <span v-else class="name">پیام شما</span>
                        <div class="msg-text">
                            <p>{{ message.text }}</p>
                            <a v-if="message.file" :href="message.file" class="blue-btn">فایل پیوست</a>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="send" class="msg-area">
                <textarea v-model="newMessage" id="new_message_input" maxlength="1000" placeholder="پیام شما ..."></textarea>
                <div class="char-status">
                    <span class="count">{{ newMessage ? newMessage.length : 0 }}</span>
                    <span>/</span>
                    <span class="max">1000</span>
                    <span>کاراکتر مجاز</span>
                </div>

                <button :disabled="loading" type="submit" class="blue-btn float-left mb-4">ارسال</button>
            </form>

            <button @click="closeTicket" :disabled="loading" type="button" class="close-ticket-button float-left mb-4">بستن تیکت</button>

        </div>
    </div>
</template>

<script>
    export default {
        props: {
            endPoint: {
                type: String,
                default: ""
            },
            ticket: {
                type: Object,
                default: () => {return {};}
            },
            initialMessages: {
                type: Array,
                default: () => []
            },
        },
        data() {
            return {
                loading: false,
                messages: [],
                newMessage: "",
                eventSource: null,
                ajaxTimeoutHandler: null,
                broadcastChannel: new BroadcastChannel('fanora_' + btoa(window.location.host)),
                browserTabId: sessionStorage.browserTabId ? sessionStorage.browserTabId : sessionStorage.browserTabId = ('tab_id_' + Math.floor(Math.random() * Number.MAX_SAFE_INTEGER).toString()),
                activeBrowserTabId: null,
            }
        },
        mounted () {
            var self = this;
            if (this.ticket.messages instanceof Array) {
                this.messages = this.ticket.messages;
            }
            if (typeof Storage !== "undefined") {
                let draftedMessage = localStorage.getItem('website_ticket_draft_message_' + this.ticket.id);
                if (typeof draftedMessage === 'string' && draftedMessage.length > 0) {
                    this.newMessage = draftedMessage;
                }
            }
            setTimeout(function () {
                self.scrollToBottom();
            }, 100);

            this.activeBrowserTabId = localStorage.activeBrowserTabId ? localStorage.activeBrowserTabId : this.browserTabId;
            document.addEventListener('visibilitychange', this.setActiveBrowserTabId);
            window.addEventListener('focus', this.setActiveBrowserTabId);

            document.addEventListener('visibilitychange', this.changeTypeHandler);
            window.addEventListener('focus', this.changeTypeHandler);
            this.broadcastChannel.addEventListener('message', this.changeTypeHandler);

            window.addEventListener('storage', function (event) {
                if (event.key == 'activeBrowserTabId') {
                    self.activeBrowserTabId = event.newValue;
                }
            });

            this.loadMessages();
        },
        destroyed() {
            document.removeEventListener('visibilitychange', this.changeTypeHandler);
            window.removeEventListener('focus', this.changeTypeHandler);
            this.broadcastChannel.addEventListener('message', this.changeTypeHandler);
        },
        methods: {
            loadMessages() {
                var self = this;
                this.broadcastChannel.postMessage({type: 'load_ticket_messages'});
                if (this.browserTabId == this.activeBrowserTabId) {
                    this.loadEventSource();
                } else {
                    this.loadAjax();
                }
            },
            loadEventSource() {
                var self = this;
                if (self.eventSource == null) {
                    this.eventSource = new EventSource(this.endPoint + '?last_created_at=' + this.lastMessage.created_at);
                    this.eventSource.addEventListener('message', function (event) {
                        if (event.data == 'seen') {
                            for (let i in self.messages) {
                                if (self.messages[i].seen_by == null && self.messages[i].user.id != self.ticket.user.id) {
                                    self.messages[i].seen_by = {id: self.ticket.user.id, name: self.ticket.user.name};
                                }
                            }
                            self.$forceUpdate();
                        } else if (event.data == 'close') {
                            self.eventSource.close();
                            self.eventSource = null;
                            self.loadMessages();
                        } else {
                            let newMessages = JSON.parse(event.data);
                            self.handleNewMessages(newMessages);
                        }
                    }, false);
                }
            },
            loadAjax() {
                var self = this;
                axios.get(this.endPoint + '?last_created_at=' + this.lastMessage.created_at)
                    .then(function (response) {
                        self.handleNewMessages(response.data);
                    })
                    .catch((e) => console.error(e))
                    .then(function () {
                        if (self.eventSource == null) {
                            self.ajaxTimeoutHandler = setTimeout(() => self.loadAjax(), 10000);
                        }
                    });
            },
            handleNewMessages(newMessages) {
                if (newMessages.length > 0) {
                    let playNotification = false;
                    for (let i in newMessages) {
                        this.messages.push(newMessages[i]);
                        if (newMessages[i].user.id != this.ticket.user.id) {
                            playNotification = true;
                        }
                    }
                    this.messages = this.messages.slice();
                    if (playNotification) {
                        playNotificationSound();
                    }
                    var self = this;
                    setTimeout(function () {
                        self.scrollToBottom();
                    }, 100);
                }
            },
            send() {
                var self = this;
                self.loading = true;
                axios.put(this.endPoint, {
                    text: self.newMessage,
                }).then(function (response) {
                    self.loading = false;
                    self.newMessage = "";
                }).catch(function (error) {
                    self.loading = false;
                    console.error(error);
                    showErrorMessages(error);
                });
            },
            closeTicket() {
                alert("TODO");
            },
            changeTypeHandler() {
                return setTimeout(() => this.changeType(), 1000);
            },
            changeType() {
                if (this.eventSource && this.browserTabId != this.activeBrowserTabId) {
                    this.eventSource.close();
                    this.eventSource = null;
                    this.loadMessages();
                }
                if (this.ajaxTimeoutHandler && this.browserTabId == this.activeBrowserTabId) {
                    clearTimeout(this.ajaxTimeoutHandler);
                    this.ajaxTimeoutHandler = null;
                    this.loadMessages();
                }
            },
            scrollToBottom() {
                $("html,body").animate({
                    scrollTop: $('#new_message_input').offset().top - (window.innerHeight - ($(".msg-area").height() + $(".msg-area button").height() + 50))
                });
            },
            setActiveBrowserTabId() {
                if (document.hidden == false) {
                    this.activeBrowserTabId = this.browserTabId;
                    localStorage.activeBrowserTabId = this.browserTabId;
                }
            }
        },
        computed: {
            lastMessage() {
                if (this.messages.length > 0) {
                    let lastMessage = this.messages[0];
                    for (let i in this.messages) {
                        if (this.messages[i].created_at > lastMessage.created_at) {
                            lastMessage = this.messages[i];
                        }
                    }
                    return lastMessage;
                }
                return null;
            }
        },
        watch: {
            newMessage(newValue, oldValue) {
                if (typeof Storage !== "undefined") {
                    localStorage.setItem('website_ticket_draft_message_' + this.ticket.id, newValue);
                }
            }
        },
    }
</script>
