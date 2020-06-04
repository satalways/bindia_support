@extends('layouts.emails')

@section('content')
    <p>
        Ticket ID: {{ $array->ticket_number }}
    </p>
    Dear {{ $array->admin_name }},
    <p>
        <b>{{ $array->customer_name }}</b> replied on your ticket <a href="https://admin.bindia.{{ is_local() ? 'p' : 'd' }}k/ticket.php?id={{ urlencode($array->ticket_number) }}">#{{ $array->id }}</a>.
    </p>
    <p>
        <b>Subject:</b> {{ $array->subject }}<br>
    </p>
    <p>
        <div style="border-bottom: 1px solid #c9c9c9">Customer Reply:</div>
        {!! $array->content !!}
    </p>

    <div style="text-align: center;">
        <a class="btn btn-sm btn-primary"
           style="display: inline-block; padding: 5px 10px; background-color: #f58220; color: #fff2da; text-decoration: none; border-radius: 4px; border: 0;"
           href="https://admin.bindia.{{ is_local() ? 'p' : 'd' }}k/ticket.php?id={{ urlencode($array->ticket_number) }}">
            View Ticket
        </a>
    </div>
@endsection
