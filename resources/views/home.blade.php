@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <ul>
                        @if (Auth::user()->type == 'customer')
                            <li><a href="/start-conversation">Start a conversation</a></li>
                        @endif
                        @if (Auth::user()->type == 'representative')
                            <li><a href="/representatives">List of representatives</a></li>
                            <li><a href="/conversations">List of conversations</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
