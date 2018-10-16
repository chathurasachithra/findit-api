<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Company;
use Carbon\Carbon;

class HomeController extends ApiController
{
    /**
     * Validate option requests with 200 status code
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function OptionResponse(){
        return $this->setHeaders( [], Response::HTTP_OK );
    }

    /**
     * Upload images
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function uploadHomeBannerImage(Request $request){

        $promo_image_path = 'images/home/';
        $file = $request->file('image');
        $promo_image_name = Carbon::now()->timestamp . '-' . $file->getClientOriginalName();
        if ( $request->hasFile( 'image' ) ) {
            $fileObject = $request->file('image');
            // move the file from tmp to the destination path
            $fileObject->move($promo_image_path, $promo_image_name);
            DB::table('home_banners')->insert([
                'title' => $request->data['title'],
                'url' => $request->data['url'],
                'banner' => $promo_image_name
            ]);
        }
        return $this->setHeaders( ['status'=> 'success'], Response::HTTP_OK );
    }

    /**
     * Delete home banners
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function deleteBannerImage(Request $request){

        $imageId = $request->id;
        DB::table('home_banners')->where('id', $imageId)->delete();
        return $this->setHeaders( ['status'=> 'success'], Response::HTTP_OK );
    }

    /**
     * Get banners
     * ------------------------------------------------------------------------------------------------------
     */
    public function getBanners(Request $request){

        $images = DB::table('home_banners')->get();
        return $this->setHeaders( [
            'images'     => $images
        ], Response::HTTP_OK );
    }
    
}
