<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\DB;
use App\Promotion;
use Illuminate\Support\Facades\Mail;

class DeleteAndSendEmailPromos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-and-send-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get expired promos and send notifications';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //1	Promotions
        //2	Events
        //3	Discount Zone
        $events = DB::table('promotions')
            ->join('categories', 'categories.category_id', '=', 'promotions.category_id')
            ->join('parent_categories', 'parent_categories.parent_category_id', '=', 'categories.parent_category_id')
            ->whereIn('parent_categories.parent_category_id', [1])
            ->where('status', 'ACTIVE')->where('expire_date', '>', Carbon::now()->subDay(3))
            ->where('expire_date', '<', Carbon::now())->get();
        foreach ($events as $event) {

            $customMessage = "Promotion (". $event->promotion_name ."),  expire on " . Carbon::parse($event->expire_date)->toDateString() .
                ". \n"
                .$customMessage = "Please take necessary actions.\n\n"
                .$customMessage = "Thanks & Regards\n"
                .$customMessage = "Team Findit.lk\n"
                .$customMessage = "www.findit.lk";
            Mail::raw($customMessage, function ($message) use ($event) {
                $message->to([env('MAIL_TO_NOTIFY'), env('MAIL_TO'), 'chathurasachithra@gmail.com']);
                $message->from(env('MAIL_FROM'), env('MAIL_SENDER_NAME'));
                $message->subject("A promotion has expired - ". $event->promotion_name);
            });
        }

        $events = DB::table('promotions')
            ->join('categories', 'categories.category_id', '=', 'promotions.category_id')
            ->join('parent_categories', 'parent_categories.parent_category_id', '=', 'categories.parent_category_id')
            ->whereIn('parent_categories.parent_category_id', [2,3])
            ->where('status', 'ACTIVE')->where('expire_date', '<', Carbon::now())->get();
        foreach ($events as $event) {
            DB::table('promotions')->where('promotion_id', $event->promotion_id)->update(['status' => 'INACTIVE']);
            $customMessage = "Promotion (". $event->promotion_name ."), remove from the app since it's expire on " . Carbon::parse($event->expire_date)->toDateString() .
                ". \n\n"
                .$customMessage = "Thanks & Regards\n"
                .$customMessage = "Team Findit.lk\n"
                .$customMessage = "www.findit.lk";
            Mail::raw($customMessage, function ($message) use ($event) {
                $message->to([env('MAIL_TO_NOTIFY'), env('MAIL_TO'), 'chathurasachithra@gmail.com']);
                $message->from(env('MAIL_FROM'), env('MAIL_SENDER_NAME'));
                $message->subject("Event/Discount zone ad has removed from the app - ". $event->promotion_name);
            });
        }
        echo 'Done';
    }
}
