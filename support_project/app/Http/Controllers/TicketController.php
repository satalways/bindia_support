<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function view($ticket)
    {
        try {
            $data = JWT::decode($ticket, env('ENCODE_KEY'), array('HS256'));
        } catch (\Exception $e) {
            //return \Response::view('errors.404',array(),404);
            abort(404, $e->getMessage());
        }

        return $data;

        $rows = \App\Tickets::where('id', 158)->get();
        return $rows;
        return $this->view('welcome', ['rows' => $rows]);
    }
}
