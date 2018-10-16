<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;
use Carbon\Carbon;

class UserController extends ApiController
{

    public function OptionResponse(){
        return $this->setHeaders( [

        ], Response::HTTP_OK );
    }

    public function store(Request $request)
    {
        $user = new User();

        $user->user_name = $request -> username;
        $user->user_password = bcrypt( $request->password );
        $user->created_at =  Carbon::now('Asia/colombo');
        $user->modified_at =  Carbon::now('Asia/colombo');

        $user->save();

        if($user){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully saved details',
                'user'     => $user,
            ], Response::HTTP_OK );
        }
    }

    public function login(Request $request){
        $user = User::where('user_name', $request -> user_name)->first();

        if(Hash::check($request -> password, $user->user_password)){
            $token = $this -> generateToken();

            if(!$this->ifUserLoggedBefore($user->user_id)){
                $user_session = $this->insertUserSession($user->user_id, $token);

                return $this->setHeaders( [
                    'status'   => 'success',
                    'user'     => $user,
                    'token'    => $token
                ], Response::HTTP_OK );
            }
            else{
                $user_session = $this->updateUserSession($user->user_id, $token);
                return $this->setHeaders( [
                    'status'   => 'success',
                    'user'     => $user,
                    'token'    => $token
                ], Response::HTTP_OK );
            }

        }
        else{
            return $this->setHeaders( [
                'status'   => 'Failed',
                'msg'      => 'Invalid user credentials!',
            ], Response::HTTP_UNAUTHORIZED );
        }
    }

    public function logOut(Request $request){
        $user_session = $this->updateUserSession($request->user_id, NULL);
        if($user_session){
            return response('success', 200);
        }
    }

    public function updateUserSession($user_id, $token){
        return DB::table( 'user_sessions' )->where( 'user_id', $user_id )->update( [
            'token' => $token,
            'modified_at' => Carbon::now('Asia/colombo')
        ] );
    }

    public function insertUserSession($user_id, $token){
        return DB::table( 'user_sessions' )->insert([
            'token' => $token,
            'user_id' => $user_id,
            'created_at' => Carbon::now('Asia/colombo'),
            'modified_at' => Carbon::now('Asia/colombo')
        ]);
    }

    public function ifUserLoggedBefore($user_id){
        $count = DB::table('user_sessions')->where('user_id', $user_id)->count();
        return $count == 0 ? false : true;
    }

    /**
     * Generate random string for token
     */
    private function generateToken() {
        return str_random( 100 );
    }
}
