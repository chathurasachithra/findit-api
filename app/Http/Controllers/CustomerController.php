<?php

namespace App\Http\Controllers;

use Doctrine\DBAL\DBALException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerController extends ApiController
{
    public function store(Request $request){

        $customer = DB::table('customers')
            ->insert([
                'fist_name'          => $request->first_name,
                'second_name'        => $request->second_name,
                'age'                => (int)($request->age),
                'province'           => $request->province,
                'mobile_no'          => $request->mobile_no,
                'land_no'            => $request->land_no,
                'email'              => $request->email,
                'gender'             => $request->gender,
                'created_at'         => Carbon::now('Asia/colombo'),
                'modified_at'        => Carbon::now('Asia/colombo')
            ]);

        $customer = DB::table('customers')->where('email', $request->email)->first();

         $custom_message = "Hello Malitha, \n" 
        .$custom_message = "New visitor has registered with our app with following details \n"
        .$custom_message = "Name : ".$customer->fist_name." ".$customer->second_name."\n"
        .$custom_message = "Age : ".$customer->age."\n"
        .$custom_message = "Province : ".$customer->province."\n"
        .$custom_message = "Mobile : ".$customer->mobile_no."\n"
        .$custom_message = "Land : ".$customer->land_no."\n"
        .$custom_message = "Email : ".$customer->email."\n"
        .$custom_message = "Gender : ".$customer->gender."\n"
        .$custom_message = "------------------------------------------------------------ \n"
        .$custom_message = Carbon::now('Asia/colombo');

        $this->sendEmail($custom_message, $customer->fist_name." ".$customer->second_name, $customer);


        return $this->setHeaders( [
            'status'   => 'success',
            'msg'      => 'Successfully saved details',
            'customer' => DB::table('customers')->where('email', $request->email)->first(),
        ], Response::HTTP_OK );
    }

    public function sendEmail($custom_message, $name, $customer) {

        try {
            Mail::raw($custom_message, function ($message) use ($name) {
                $message->to(env('MAIL_TO'));
                $message->from(env('MAIL_FROM'), env('MAIL_SENDER_NAME'));
                $message->subject("New Visitor Registration - " . $name);
            });

            $custom_message = "You have been added to our mailing list and will now be among the first to hear about".
                                "new offers,discounts,events,promotions & many more. \n \n"
                .$custom_message = "Thanks & Regards\n"
                .$custom_message = "Team Findit.lk\n"
                .$custom_message = "www.findit.lk";

            Mail::raw($custom_message, function ($message) use ($customer) {
                $message->to($customer->email);
                $message->from(env('MAIL_FROM'), env('MAIL_SENDER_NAME'));
                $message->subject("Thank You For Subscribing! - Findit.lk");
            });
        } catch (\Exception $ex) {

        }

        return true;
    }

    public function addInterests(Request $request){
        DB::table('customers_interests')
            ->insert([
                'customer_id'       => (int)$request->customer_id,
                'category_id'       => (int)$request->category_id,
                'created_at'        => Carbon::now('Asia/colombo'),
                'modified_at'       => Carbon::now('Asia/colombo')
            ]);

        return $this->setHeaders( [
            'status'   => 'success',
        ], Response::HTTP_OK );
    }

    public function getAll(){
        $customers = DB::table('customers_interests')->all();

        return $this->setHeaders( [
            'status'   => 'success',
            'customers' => $customers,
        ], Response::HTTP_OK );
    }
}
