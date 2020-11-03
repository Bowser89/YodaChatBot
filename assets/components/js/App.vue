<template>
    <div id="chat" class="container">
        <div class="offset-4 col-lg-4" style="margin-top: 30px;">
            <button class="btn btn-primary" v-on:click="clearHistory" style="width: 100%;">Clear History</button>
        </div>
        <div class="row">
            <div v-if="errorMessage !== null" id="errorMessage" style="background: #cc0000;
    padding: 1em;
    margin-top: 1em;
    text-align: center;
    border-radius: 10px 10px;
    width: 100%;">
                {{errorMessage}}
            </div>
        </div>
        <ul id="conversation" style="list-style-type: none;">
            <div v-for="messageItem in messages">
                <li v-bind:style="messageItem.source === 'Human' ? 'padding: 1em;width: 50%;margin-top: 1em;background: #d5cbbf;border-radius: 10px 0px 10px 10px;text-align: right;margin-left: 35%;': 'background: #b8da91;border-radius: 0px 10px 10px 10px;padding: 1em;width: 50%;margin-top: 1em;text-align: left;'">
                    <div v-if="messageItem.source === 'Human'">
                        You:
                    </div>
                    <div v-else>
                        YodaBot Says:
                    </div>
                    <div v-if="messageItem.titlePhrase">
                        {{messageItem.titlePhrase}}
                        <div v-for="message in messageItem.messages ">
                            <li>{{ message }}</li>
                        </div>
                    </div>
                    <div v-else>
                        <div v-for="message in messageItem.messages ">
                            {{ message }}
                        </div>
                    </div>
                </li>
            </div>
        </ul>
        <div v-if="isWriting" style="text-align: center">Yoda is looking for knowledge . . .</div>
        <div class="chat-form">
            <div class="row">
                <div class="col-lg-12">
                    <form v-on:submit.prevent="sendMessage" class="form-inline">
                        <input type="text" placeholder="Ask something to the real Yoda!" v-model="form.message" required style="display: inline-block!important;width: 85%!important;">
                        <button class="btn btn-primary ml-2">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            previousConversation: {
                type : Array,
                default: []
            }
        },
        data: function() {
            return{
                form: {
                    message: ''
                },
                isWriting: false,
                messages: this.previousConversation,
                errorMessage: null
            }
        },
        methods:{
            sendMessage() {
                var userQuestion = {'messages' : [this.form.message], 'source' : 'Human', 'titlePhrase' : null};
                var that = this;
                this.appendToConversation(userQuestion);
                this.isWriting = true;

                axios.post('/api/send_message', this.form)
                    .then((response) => {
                        this.isWriting = false;
                        this.appendToConversation(response.data.message);
                    })
                    .catch((error) => {
                        that.messages = '';
                        that.errorMessage = 'Oh! There was an error. Please clear history and refresh page';
                    }).finally(() => {
                    that.form.message = '';
                    that.isWriting = false;
                });
            },
            appendToConversation(data) {
                this.messages.push({'messages' : data.messages, 'source' : data.source , 'titlePhrase' : data.titlePhrase });
            },
            clearHistory() {
                var self = this;
                axios.post('/api/clear_history')
                    .then(function () {
                        self.messages = [];
                    });
            }
        }
    }
</script>
