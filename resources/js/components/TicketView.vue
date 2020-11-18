<template>
    <div class="container">
        <div>
            <div v-for="(message, index) in messages" :key="index" class="row">
                <div
                    class="col-10 my-2 py-2"
                    :class="{
                        'float-left bg-chat-1 offset-2 rounded-right': message.user.id != ticket.user.id,
                        'bg-chat-2 rounded-left': message.user.id == ticket.user.id}"
                    >
                    <div>
                        <div class="row justify-content-between">
                            <span class="col-auto mr-auto" v-if="message.user.id != ticket.user.id">پاسخ پشتیبانی</span>
                            <span class="col-auto mr-auto" v-else>پیام شما</span>
                            <small class="col-auto">{{ message.created_at_diff }}</small>
                        </div>
                        <div>
                            <p class="white-space-pre">{{ message.text }}</p>
                            <a v-if="message.file" :href="message.file" class="blue-btn">فایل پیوست</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="chat_input_form" @submit.prevent="send" @keyup.enter.ctrl="send">
            <div class="form-group">
                <textarea v-model="newMessage" id="new_message_input" class="form-control" maxlength="1000" placeholder="پیام شما ..."></textarea>
            </div>
            <div class="btn-group">
                <button :disabled="loading" type="submit" class="btn btn-primary float-left mb-4">{{ __('Send') }}</button>
                <button @click="closeTicket" :disabled="loading" type="button" class="btn btn-danger float-left mb-4">بستن تیکت</button>
            </div>
        </form>
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
            user: {
                type: Object,
                default: () => {return {};}
            }
        },
        data() {
            return {
                loading: false,
                messages: [],
                newMessage: "",
                eventSource: null,
                ajaxTimeoutHandler: null,
                broadcastChannel: new BroadcastChannel('sanjab_ticket_sample_' + btoa(window.location.host)),
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
                        if (newMessages[i].user.id != this.user.id) {
                            playNotification = true;
                        }
                    }
                    this.messages = this.messages.slice();
                    if (playNotification) {
                        playNotificationSound();
                    }
                    var self = this;
                    setTimeout(() => self.scrollToBottom(), 100);
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
                    scrollTop: $('#chat_input_form').offset().top
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
