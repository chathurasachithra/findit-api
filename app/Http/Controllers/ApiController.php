<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ApiController extends Controller {

    public function setHeaders( $data, $status_code ) {
        return response()->json( $data, $status_code, [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Headers' => 'Content-Type, Auth-Token, User-Id',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, DELETE, PUT',
            'Access-Control-Max-Age'       => 31536000,
            'Cache-Control'                => 'public',
        ] );
    }
}