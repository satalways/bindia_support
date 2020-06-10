<?php

namespace App\Http\Controllers;

use App\Admins;
use App\Tickets;
use App\TicketsDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use function GuzzleHttp\Psr7\mimetype_from_filename;
use Illuminate\Support\Facades\Crypt;

class TicketController extends Controller
{
    public $primaryKey = 'id';

    public function view($ticket)
    {
        //return Crypt::encryptString('Hello world.');
        //return Crypt::decryptString('eyJpdiI6Ik8vblEyZnh5QXc4TERtWXR5bUtLZUE9PSIsInZhbHVlIjoidm93M3E5Z1hNRGZGS0JmS1VCVFVnUT09IiwibWFjIjoiZGE0MTQ0NzIwYjA1MmM2ODc4Mzk5NWM1ZTU5ZTI1NWNhYzc4N2Q5ZjlmOGYyOGQ2YzhkODRkYTU0MTlmM2Y5ZCJ9');
        //return decrypt('eyJpdiI6Ik8vblEyZnh5QXc4TERtWXR5bUtLZUE9PSIsInZhbHVlIjoidm93M3E5Z1hNRGZGS0JmS1VCVFVnUT09IiwibWFjIjoiZGE0MTQ0NzIwYjA1MmM2ODc4Mzk5NWM1ZTU5ZTI1NWNhYzc4N2Q5ZjlmOGYyOGQ2YzhkODRkYTU0MTlmM2Y5ZCJ9');
        //return decrypt('eyJpdiI6Im1YUHhrZVg3NnQ0RHlZUkV1Ny90V1E9PSIsInZhbHVlIjoiY3pERzd6VkIvMzI0cExLcmVpYmRJODhJTU43eWE2dm4yQ09zOGlpUE9pUT0iLCJtYWMiOiJiYmRjNzVmMTg3MTcwOGVkN2Y5N2ZhNmJjMDE4ZjlmY2JlZWY4NTFjMDkyZTBlZDljY2ZhZGQ2MzFkNTI2MGY2In0=');

        try {
            $data = json_decode(base64_decode($ticket));
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
        if (!isset($data->id)) {
            abort(404, 'Invalid ticket id');
        }

        $row = \App\Tickets::findorFail($data->id);

        return view('tickets.view', [
            'row' => $row,
            'title' => 'View Ticket #' . $row->ticket_number,
            'ticket' => $ticket
        ]);
    }

    public function save_reply($ticket)
    {
        try {
            $data = json_decode(base64_decode($ticket));
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
        if (!isset($data->id)) {
            abort(404, 'Invalid ticket id');
        }

        request()->validate([
            'content' => 'required'
        ]);

        $storage_name = is_local() ? 'public' : 'ftp';

        $oldRow = Tickets::find($data->id);

        $oldRow->content = \request()->input('content');
        $oldRow->updated_at = Carbon::now();
        $oldRow->save();

        $error = null;
        try {
            $Details = new TicketsDetails();
            $Details->ticket_id = $oldRow->id;
            $subject = 'Re: ' . $oldRow->subject . '    ' . $oldRow->ticket_number;
            $Details->subject = $subject;
            $Details->content = $oldRow->content;
            $Details->from_name = $oldRow->from_name;
            $Details->from_email = $oldRow->from_email;
            $Details->to_email = $oldRow->to_email;
            $Details->attachments = 0;
            $Details->created_at = Carbon::now();
            $Details->created_by = 0;
            $Details->mail_sent = 1;
            $Details->is_reply = 1;
            $Details->raw_headers = 'Sent from Bindia back-end system\nFrom IP: ' . my_ip();
            $Details->save();

            if (request()->hasFile('files')) {
                $files = request()->file('files');
                foreach ($files as $file) {
                    if (Storage::disk($storage_name)->put($Details->id . '/' . $file->getClientOriginalName(), $file->get())) {
                        $Details->attachments = 1;
                    }
                }
            }
            $Details->save();
            $error = null;

            $Admins = new Admins();
            $admin = $Admins->find($oldRow->assigned_to);
            $array = (object)[
                'assigned_to' => $oldRow->assigned_to,
                'to_email' => $admin->email,
                'admin_name' => $admin->username,
                'id' => $oldRow->id,
                'customer_name' => $oldRow->from_name,
                'ticket_number' => $oldRow->ticket_number,
                'subject' => $oldRow->subject,
                'content' => $oldRow->content,
                'attached' => $Details->attachments,
                'storage_name' => $storage_name
            ];

            Mail::send('emails.reply', ['array' => $array], function ($m) use ($array) {
                if (is_local()) {
                    $m->to('shakeel@shakeel.pk');
                } else {
                    $m->to($array->to_email);
                }

                $m->from('office@bindia.dk', 'Bindia');
                if ($array->attached == 1) {
                    foreach (request()->file('files') as $file) {
                        $m->attachData($file->get(), $file->getClientOriginalName());
                    }
                }
                $m->subject('Customer replied on your ticket #' . $array->id);
            });
        } catch (\Exception $e) {
            return redirect()
                ->route('view.ticket', urlencode($ticket))
                ->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()
            ->route('view.ticket', urlencode($ticket));
        //return redirect()->back()->with('error', $error);
    }

    public function file($id, $file)
    {
        $path = base64_decode($file);

        $piece = explode('/', $path);
        if ($id != $piece[0]) {
            abort(404, 'Invalid file id');
        }

        $storage_name = is_local() ? 'public' : 'ftp';

        if (!Storage::disk($storage_name)->exists($path)) {
            abort(404, 'File not found on server');
        }

        $fileContent = Storage::disk($storage_name)->get($path);
        $mime = mimetype_from_filename($path);

        return response()->make($fileContent, '200', [
            'Content-Type' => $mime,
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public',
            'Content-Length' => strlen($fileContent),
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Content-Transfer-Encoding' => 'binary',
            'Accept-Ranges' => 'bytes'
        ]);
    }
}
