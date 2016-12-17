@extends('layouts.chat')

@section('content')
<div id="chatApp">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">List of conversations</div>
                    <div class="panel-body">
                        <table class="table" id="conversations">
                            <tr>
                                <th>Id</th>
                                <th>Message count</th>
                                <th>Date</th>
                                <th></th>
                                <th></th>
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

        // Build the link to join a conversation
        var getJoinLinkHtml = function(conversationId) {
            return '<a href="/chat/' + conversationId + '/{{ $representativeId }}"><span class="glyphicon glyphicon-comment"></span></a>';
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
                url: '/api/v1/chats?api_token=' + window.Laravel.apiToken
            }).done(function(data) {
                data = $.parseJSON(data);
                $.each(data, function(index, conv) {
                    var $tr = $('<tr>').append(
                            $('<td>').html(getConversationLinkHtml(conv.conversation_id)),
                            $('<td>').text(conv.messages),
                            $('<td>').text(conv.date),
                            $('<td>').html(getJoinLinkHtml(conv.conversation_id)),
                            $('<td>').html(getDeleteLinkHtml(conv.conversation_id))
                    ).appendTo('#conversations');
                });

            })
        });
    </script>
@stop
