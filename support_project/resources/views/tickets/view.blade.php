@extends('layouts.main')

@section('content')
    <div class="text-center p-3">
        <img src="https://www.bindia.dk/themes/2016/img/logo.png" alt=""><br>
        Support Tickets
    </div>

    <div class="row header_row">
        <div class="col-md-6">
            Ticket By <b>{{ $row->from_name }} &lt;{{ $row->from_email }}&gt;</b>
        </div>
        <div class="col-md-6">
            Created at <b>{{ $row->created_at->diffForHumans() }}</b>
            {{ $row->created_at ? '(' . $row->created_at->format( config('options.datetime_format') ) . ')' : '' }}
        </div>
    </div>
    <div class="row header_row">
        <div class="col-md-6">
            Ticket Number: <b>{{ $row->ticket_number }}</b>
        </div>
        <div class="col-md-6">
            Last Updated:
            <b>{{ $row->details[0]->created_at ? $row->details[0]->created_at->diffForHumans() : '' }}</b>
            {{ $row->details[0]->created_at ? '(' . $row->details[0]->created_at->format(config('options.datetime_format')) . ')' : '' }}
        </div>
    </div>
    <div class="row header_row">
        <div class="col-md-6">
            Responsible: {!! $row->assigned_to ? '<b>'.user($row->assigned_to)->username.'</b>' : '<i>Not yet</i>' !!}
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">Error: {{$errors->first()}}</div>
    @endif

    <div class="messages">
        <div class="subject">
            Subject: {{ $row->subject }}
        </div>
        @foreach($row->details as $message)
            <div class="row">
                <div class="col-md-12 message_row">
                    <div class="title">
                        <div class="float-right">
                            <a href="#" class="reply_link">Reply</a>
                        </div>
                        From: <img src="{{ $message->get_image_url() }}"> <b>{{ $message->from_name }}</b>
                        &lt;{{ $message->from_email }}&gt;<br>
                        Time: {{ $message->created_at ? $message->created_at->diffForHumans() : 'Unknown' }}
                        {{ $message->created_at ? '(' . $message->created_at->format(config('options.datetime_format')) . ')' : '' }}
                    </div>
                    <div class="content">
                        {!! $message->content !!}

                        <div class="files">
                            <hr>
                            @forelse($message->get_files() as $file)
                                <a target="_blank" class="btn btn-sm btn-primary"
                                   href="{{ $file->url }}">
                                    {{ $file->filename }} (<small style="color: #fff">{{ $file->filesize }}</small>)
                                </a>
                            @empty
                                <small>No file is attached</small>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="row">
            <div class="col-md-12 reply">
                <hr>
                <h3 id="reply_title">Reply Ticket</h3>
                <form action="{{ route('view.ticket.put', urlencode($ticket)) }}" id="ticket_reply_form" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Your name:</label>
                                <input value="{{ $row->from_name }}" type="text" class="form-control"
                                       placeholder="Your name" readonly>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Your email:</label>
                                <input value="{{ $row->from_email }}" type="text" class="form-control"
                                       placeholder="Your email address" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="color: #fff;">Reply now</label><br>
                                <button class="btn btn-primary">Reply Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            @error('content')
                            <div class="alert alert-danger">{{ $message }}</div>@enderror
                            <textarea name="content" class="summernote">{{ old('content') ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="col">
                        <input type="file" name="files[]" multiple="multiple" class="ezdz"
                               accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.zip,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css"
          integrity="sha256-ztUDTRE0Jq4ZR/ZKD+fivOhevPPuiXD0ua7M+3OE+t4=" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ezdz@0.4.3/src/jquery.ezdz.css">
    <link rel="stylesheet" href="{{ url('assets/styles.css') }}">
@endsection


@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"
            integrity="sha256-oOIhv6MPxuIfln8IN7mwct6nrUhs7G1zvImKQxwkL08=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/ezdz@0.4.3/src/jquery.ezdz.min.js"></script>
    <script src="{{ url('assets/js.js') }}"></script>
@endsection
