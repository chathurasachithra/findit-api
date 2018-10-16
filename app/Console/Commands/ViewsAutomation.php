<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\DB;
use App\Promotion;

class ViewsAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ViewsAutomation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic add views';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = DB::table('promotions')->where('status', 'ACTIVE')->count();


        $index = 0;

        while($index <= 20) {
            $index++;
            $random_promotion_id = rand(1, $count);

            $promotion =DB::table('promotions')->where('promotion_id', (int)$random_promotion_id)->first();

            if($promotion){
                $new_views = (int)$promotion->promotion_unique_views + 10;

                DB::table('promotions')->where('promotion_id', (int)$random_promotion_id)
                    ->update([
                        'promotion_unique_views' =>  (int)$new_views
                    ]);
            }

        }
    }
}
