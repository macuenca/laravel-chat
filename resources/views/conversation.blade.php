@extends('layouts.chat')

@section('content')
<div id="chatApp" class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Chat Window</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form">
                        <div id="chatBox">
                            <ol>
                                <li v-for="item in messages">
                                    @{{ item.message }}
                                </li>
                            </ol>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <textarea id="chatMessage" class="form-control" placeholder="Write message" cols="30" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button id="sendButton" class="btn btn-primary">
                                    Send
                                </button>
                            </div>
                        </div>
                    </form>
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

        Echo.private('conversation.{{ $conversationId }}')
                .listen('ChatMessageSent', (e) => {
                    chatApp.messages.push(e.chatMessage);
        });

        $("#sendButton").click(function(e) {
            e.preventDefault();
            $.post('/api/v1/chats?api_token=' + window.Laravel.apiToken, { conversation_id: {{ $conversationId }}, message: $('#chatMessage').val() });
            $('#chatMessage').val('');
        })
    </script>
@stop
