<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CustomHeadersMiddleware
{

    protected $except = [
        'api/v1/user/auth',
    ];


    public function handle( $request, Closure $next ) {

        if ( $request->isMethod( 'post' ) ) {

            if ( $this->shouldPassThrough( $request ) || ( $request->header( 'Auth-Token' ) && $this->validateToken( $request->header( 'Auth-Token' ) ) ) ) {
                return $next( $request );
            } else {
                return response('Unauthorized.', 401);
            }
        } else {
            return $next( $request );
        }

    }


    private function shouldPassThrough( $request ) {

        foreach ( $this->except as $except ) {
            if ( $except !== '/' ) {
                $except = trim( $except, '/' );
            }

            if ( $request->is( $except ) ) {
                return TRUE;
            }
        }

        return FALSE;
    }


    private function validateToken( $token ) {
        $count = DB::table('user_sessions')->where('token', $token)->count();
        if($count == 1){
            return TRUE;
        }
        else{
            return response('Unauthorized.', 401);
        }
    }
}