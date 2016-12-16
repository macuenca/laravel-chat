@extends('layouts.chat')

@section('content')
<div id="chatApp">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Chat Window</div>
                    <div class="panel-body">
                        <div id="chatBox">
                            <ol>
                                <li v-for="item in messages">
                                    @{{ item.message }}
                                </li>
                            </ol>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <textarea id="chatMessage" class="form-control" placeholder="Write message" cols="30" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <button id="sendButton" class="btn btn-primary">
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('footer')
    <script>
        var chatApp = new Vue({
            el: "#chatBox",
            data: {
                messages: []
            }
        });

        Echo.private('conversation.1')
                .listen('ChatMessageSent', (e) => {
                    chatApp.messages.push(e.chatMessage);
        });

        $("#sendButton").click(function() {
            $.post('/api/v1/chats?api_token=' + window.Laravel.apiToken, { conversation_id: 1, message: $('#chatMessage').val() });
            console.log($('#chatMessage').val());
        })
    </script>
@stop
