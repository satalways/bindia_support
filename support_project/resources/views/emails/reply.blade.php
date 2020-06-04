<div class="bindia_header">
    <div style="text-align: center; margin-bottom: 10px;"><img src="https://www.bindia.dk/themes/2016/img/logo.png">
    </div>
    <hr>
</div>
Dear <b>{{ $array->admin_name }}</b>,
<p>
    {{ $array->customer_name }} replied on your ticket #{{ $array->id }}.
</p>
<p>
    <b>Ticket ID:</b> {{ $array->ticket_number }}<br>
    <b>Subject:</b> {{ $array->subject }}<br>
</p>
<p>
    <b>Customer Reply:</b><br>
    {!! $array->content !!}
</p>

<hr>
<b>View Ticket</b><br>
https://admin.bindia.{{ is_local() ? 'p' : 'd' }}k/ticket.php?id={{ urlencode($array->ticket_number) }}
