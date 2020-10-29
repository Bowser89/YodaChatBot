<template>
    <div>
        <h2>Talk with Yoda!</h2>
        <div id="conversation">
            <div v-for="messageItem in messages">
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
            </div>
        </div>
        <p v-if="isWriting">Yoda is looking for knowledge . . .</p>
        <form v-on:submit.prevent="sendMessage"><br>
            <input type="text" placeholder="Ask something to the real Yoda!" v-model="form.message" required>
            <button class="btn btn-primary">Submit</button>
        </form>
        <button class="btn btn-primary" v-on:click="clearHistory">Clear History</button>

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
            }
        },
        methods:{
            sendMessage() {
                var userQuestion = {'messages' : [this.form.message], 'source' : 'Human', 'titlePhrase' : null};
                this.appendToConversation(userQuestion);
                this.isWriting = true;

                axios.post('/api/send_message', this.form)
                    .then((response) => {
                        this.form.message = '';
                        this.isWriting = false;
                        this.appendToConversation(response.data.message);
                    })
                    .catch((error) => {
                        this.form.message = '';
                    }).finally(() => {
                    this.form.message = '';
                });
            },
            appendToConversation(data) {
                this.messages.push({'messages' : data.messages, 'source' : data.source , 'titlePhrase' : data.titlePhrase });
            },
            clearHistory() {
                this.messages = [];
            }
        }
    }
</script>
