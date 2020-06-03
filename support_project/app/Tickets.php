<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    protected $table = 'tickets';
    //public $timestamps = true;

    public $primaryKey = 'id';

    public function details()
    {
        return $this->hasMany(TicketsDetails::class, 'ticket_id')
            ->where('is_comment', 0)
            ->orderBy('id', 'DESC');
    }
}
