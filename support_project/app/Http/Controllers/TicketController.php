<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function view($ticket)
    {
        try {
            $data = json_decode( base64_decode( $ticket ) );
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
        if (!isset($data->id)) {
            abort(404, 'Invalid ticket id');
        }

        $row = \App\Tickets::where('id', $data->id)->first();

        return view('tickets.view', ['row' => $row]);
    }
}
