<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketsDetails extends Model
{
    protected $table = 'tickets_details';
    public $timestamps = true;
    //protected $dates = ['created_at'];

    public function ticket()
    {
        return $this->belongsTo(Tickets::class, 'ticket_id', 'id');
        //return $this->hasOne(Tickets::class, 'ticket_id');
    }

    public function get_image_url()
    {
        return $this->exists ? "https://www.gravatar.com/avatar/" . md5(strtolower($this->from_email)) . "?s=150" : '#';
    }

    public function get_files()
    {
        $storage_name = is_local() ? 'public' : 'ftp';

        if (!Storage::disk($storage_name)->exists($this->id)) {
            return (object)[];
        } else {
            $files = Storage::disk($storage_name)->files($this->id);
            $out_files = [];
            foreach ($files as $file) {
                if (basename($file) === 'index.html') continue;
                $out_files[] = (object)[
                    'path' => $file,
                    'filesize' => human_filesize(Storage::disk($storage_name)->size($file)),
                    'filename' => basename($file),
                    'url' => route('view.file', ['id' => $this->id, 'file' => urlencode(base64_encode($file))])
                ];
            }

            return (object)$out_files;
        }
    }
}
