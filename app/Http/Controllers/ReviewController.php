<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Review;
use Carbon\Carbon;

class ReviewController extends ApiController
{


    public function OptionResponse(){
        return $this->setHeaders( [

        ], Response::HTTP_OK );
    }

    public function getAll()
    {
        $review = Review::all();;

        if($review){
            return $this->setHeaders( [
                'review'     => $review,
            ], Response::HTTP_OK );
        }
    }


    public function store(Request $request)
    {
        $review = new Review();

        $review->reviewer_name = $request->reviewer_name;
        $review->reviewer_email = $request->reviewer_email;
        $review->review_description = $request->review_description;
        $review->promotion_id = $request->promotion_id;
        $review->created_at =  Carbon::now('Asia/colombo');
        $review->modified_at =  Carbon::now('Asia/colombo');

        $review->save();

        if($review){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully saved details',
                'review'     => $review,
            ], Response::HTTP_OK );
        }
    }

    public function getReviewsByPromoId(Request $request){
        $reviews = DB::table('reviews')->where('promotion_id', $request->promotion_id)->get();

        if($reviews){
            return $this->setHeaders( [
                'review'     => $reviews,
            ], Response::HTTP_OK );
        }
    }
}
