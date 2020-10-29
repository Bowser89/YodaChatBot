<template>
    <div>
        <div id="crap"></div>
        <h2>Talk with Yoda!</h2>
        <div id="conversation"></div>
        <p v-if="isWriting">Yoda is writing . . .</p>
        <form v-on:submit.prevent="sendMessage" ><br>
            <input type="text" id="messageyoda" placeholder="Ask something to Yoda!" v-model="form.message" required>
            <button class="btn btn-primary">Submit</button>
        </form>
    </div>
</template>

<script>
    export default {
        data: function() {
            return{
                form: {
                    message: ''
                },
                isWriting: false
            }
        },
        methods:{
            sendMessage() {
                $('#conversation').append('Me: '+this.form.message+'<br><br>');
                this.isWriting = true;

                axios.post('/api/send_message', this.form)
                    .then((response) => {
                        this.isWriting = false;
                        this.appendToConversation(response.data);
                    })
                    .catch((error) => {
                        // error.response.status Check status code
                    }).finally(() => {
                    //Perform action in always
                });
            },
            appendToConversation(data) {
                var conversation;
                console.log(data.messages[0]);
                $('#crap').append(data.counter);
                conversation = '<b>'+data.source+' Says:</b> ';

                if (data.titlePhrase != null) {
                    conversation += data.titlePhrase+'</br>';
                    for (var i = 0; i < data.messages.length; i++) {
                        conversation += '<li>'+data.messages[i]+'</li></br>';
                    }
                } else {
                    for (var i = 0; i < data.messages.length; i++) {
                        conversation += data.messages[i]+'</br>';
                    }
                }

                $('#conversation').append(conversation);
            }
        }
    }
</script>
