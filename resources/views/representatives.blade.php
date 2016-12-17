@extends('layouts.chat')

@section('content')
<div id="chatApp">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">List of representatives</div>
                    <div class="panel-body">
                        <table class="table table-hover" id="representatives">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
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
        var saveRep = function(id, elem) {
            $.ajax({
                method: 'PUT',
                url: '/api/v1/users/' + id + '?api_token=' + window.Laravel.apiToken,
                data: { name: elem.innerHTML }
            }).done(function() {
                console.log('Name changed');
            });
        };

        $(function() {
            $.ajax({
                url: '/api/v1/users?api_token=' + window.Laravel.apiToken
            }).done(function(data) {
                var conversationId = new Date().getTime();
                data = $.parseJSON(data);
                $.each(data, function(index, rep) {
                    var $tr = $('<tr>').append(
                        $('<td contenteditable="true" onblur="saveRep(' + rep.id + ', this)">').text(rep.name),
                        $('<td>').text(rep.email)
                    ).appendTo('#representatives');
                });

            });
        });
    </script>
@stop
