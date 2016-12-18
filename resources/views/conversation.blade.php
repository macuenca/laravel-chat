@extends('layouts.chat')

@section('content')
<div id="chatApp">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    <div class="panel-heading">Messages in conversation ID {{ $conversationId }}</div>
                    <div class="panel-body">
                        <table class="table" id="conversations">
                            <tr>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('footer')
    <script>
        // Build the chat link for a new conversation
        var getConversationLinkHtml = function(conversationId) {
            return '<a href="/conversation/' + conversationId + '">' + conversationId + '</a>';
        };

        // Build the link to delete a conversation
        var getDeleteLinkHtml = function(conversationId) {
            return '<a href="javascript:void(0)" onClick="deleteConversation(' + conversationId + ', this)"><span class="glyphicon glyphicon-trash"></span></a>';
        };

        var deleteConversation = function(id, elem) {
            $.ajax({
                method: 'DELETE',
                url: '/api/v1/chats/' + id + '?api_token=' + window.Laravel.apiToken
            }).done(function() {
                elem.parentNode.parentNode.remove();
                console.log('Conversation deleted');
            });
        };

        $(function() {
            $.ajax({
                url: '/api/v1/chats/' + {{ $conversationId }} + '?api_token=' + window.Laravel.apiToken
            }).done(function(data) {
                data = $.parseJSON(data);
                $.each(data, function(index, conv) {
                    var $tr = $('<tr>').append(
                            $('<td>').html('<i>' + conv.sender_name + ':</i> ' + conv.message),
                            $('<td>').text(conv.created_at)
                    ).appendTo('#conversations');
                });

            })
        });
    </script>
@stop
