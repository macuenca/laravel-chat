@extends('layouts.chat')

@section('content')
<div id="chatApp">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Start a conversation with a Representative</div>
                    <div class="panel-body">
                        <table class="table table-hover" id="representatives">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Chat</th>
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
        var getChatLinkHtml = function(conversationId, representativeId) {
            return '<a href="/conversation/' + conversationId + '/' +  representativeId + '"><span class="glyphicon glyphicon-comment"></span></a>';
        };

        $(function() {
            $.ajax({
                url: '/api/v1/users?api_token=' + window.Laravel.apiToken
            }).done(function(data) {
                var conversationId = new Date().getTime();
                data = $.parseJSON(data);
                $.each(data, function(index, rep) {
                    var $tr = $('<tr>').append(
                        $('<td>').text(rep.name),
                        $('<td>').text(rep.email),
                        $('<td>').html(getChatLinkHtml(conversationId, rep.id))
                    ).appendTo('#representatives');
                });
            });
        });
    </script>
@stop
