<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Exception;
use App\Promotion;
use App\Category;
use App\Company;
use Carbon\Carbon;

class PromotionController extends ApiController
{

    /**
     * Send 200 status code for all option requests
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function OptionResponse(){
        return $this->setHeaders( [], Response::HTTP_OK );
    }

    /**
     * Add new promotion
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function store(Request $request)
    {

        $promotion = new Promotion();

        $slug = $this->generateSlug($request -> promotion_name, 'promotions', 'promotion_slug');

        $promotion->promotion_name = $request -> promotion_name;
        $promotion->promotion_description = $request -> promotion_description;
        $promotion->promotion_image = $request->promotion_image;
        $promotion->promotion_views = (int)0;
        $promotion->promotion_is_featured = $request->promotion_is_featured;
        $promotion->category_id = (int)$request->category_id;
        $promotion->company_id = (isset($request->company_id)) ? (int)$request->company_id : null;
        $promotion->company_text = (isset($request->company_text)) ? $request->company_text : null;
        $promotion->created_at =  Carbon::now('Asia/colombo');
        $promotion->modified_at =  Carbon::now('Asia/colombo');
        $promotion->offer_end_date =   $request->offer_end;
        $promotion->expire_date =  Carbon::now('Asia/colombo')->addDays($request->expires_in);
        $promotion->status =  "ACTIVE";
        $promotion->promotion_slug = $slug;
        $promotion->promotion_unique_views = $request->fake_views;
        $promotion->meta_tags = $request->meta_tags;
        $promotion->date = $request->date;
        $promotion->time = $request->time;
        $promotion->venue = $request->venue;
        $promotion->reservations = $request->reservations;
        $promotion->save();

        $this -> deleteExpiredPromotions();

        if($promotion){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully saved details',
                'promotion'     => $promotion,
            ], Response::HTTP_OK );
        }
    }


    /**
     * Generate unique slug for promotions
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function generateSlug($name, $table_name, $slug_column){
        $unique = false;
        $index = 0;
        $slugged_name = $this->slugify($name);
        while($unique == false){
            if($index > 0){
                $slugged_name = $this->slugify($name).'-'.$index;
            }
            $count = DB::table($table_name)->where($slug_column, $slugged_name)->count();
            $unique = $count > 0 ? false : true;
            $index++;
        }
        return $this->slugify($slugged_name);
    }


    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }


    /**
     * Get single promotion details by promotion id
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getSinglePromotion(Request $request){
        $promotions = DB::table('promotions');
        $promotions->where('promotion_id', $request -> p_id)->first();
        $promotions->join('categories', 'categories.category_id', '=', 'promotions.category_id');
        $promotions->join('companies', 'companies.company_id', '=', 'promotions.company_id');
        $promotions->join('parent_categories', 'parent_categories.parent_category_id', '=', 'categories.parent_category_id');
        

        if($promotions){
            return $this->setHeaders( [
                'promotions'     => $promotions->get(),
            ], Response::HTTP_OK );
        }
    }


    /**
     * Get All promotions with filter
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getAllWithFilters(Request $request){

        $this -> deleteExpiredPromotions();

        $params = $request;
        $promotions = DB::table('promotions');

        //filter by order
        if($params ->order_by && $params ->order && $params->order_by != 'created_at' && $params->order_by != 'promotion_views'){
            $promotions->orderBy($params ->order_by, $params ->order);
        }

        //filter by order
        if($params ->order_by && $params ->order && $params->order_by == 'promotion_views'){
            $promotions->orderByRaw('promotions.promotion_views + promotions.promotion_unique_views '.$params ->order);
        }


        //filter by order
        if($params ->order_by && $params ->order && $params->order_by == 'created_at'){
            $promotions->orderBy('promotions.'.$params ->order_by, $params ->order);
        }

        //filter by category
        if($params ->category_id){
            $promotions->where('promotions.category_id', $params ->category_id);
        }

        if($params -> slug){
            $promotions->where('promotion_slug', $params->slug);
        }


        //filter by company
        if($params ->company_id){
            $promotions->where('promotions.company_id', $params ->company_id);
        }

        //filter by is_featured
        if($params ->promotion_is_featured){
            $promotions->where('promotion_is_featured', $params ->promotion_is_featured);
        }

        //filter by date
        if($params ->from_date && $params->to_date){
            $promotions->where('modified_at', '>=',$params ->from_date);
            $promotions->where('modified_at', '<=', $params->to_date);
        }

        //Limit results
        if($params ->limit){
            $promotions->limit($params ->limit);
        }

        //filter by offset
        if($params ->offset){
            $promotions->offset($params ->offset);
        }

        $promotions->leftJoin('companies', 'companies.company_id', '=', 'promotions.company_id');
        $promotions->join('categories', 'categories.category_id', '=', 'promotions.category_id');
        $promotions->join('parent_categories', 'parent_categories.parent_category_id', '=', 'categories.parent_category_id');


        if($params->cetegory_slug){
            $promotions->where('categories.category_slug', $params ->cetegory_slug);
        }

        if($params->parent_slug){
            $promotions->where('parent_categories.parent_category_slug', $params ->parent_slug);
        }

        //filter by category
        if($params ->parent_category_id){
            $promotions->where('parent_categories.parent_category_id', $params ->parent_category_id);
        }

        //filter by category
        if($params ->keyword){
            $promotions->where('promotions.promotion_name', 'like',  '%'.$params ->keyword . '%')
                ->orWhere('promotions.promotion_description', 'like',  '%'.$params ->keyword . '%')
                ->orwhere('companies.company_name', 'like',  '%'.$params ->keyword . '%' )
                ->orwhere('promotions.meta_tags', 'like',  '%'.$params ->keyword . '%' );
        }

        //filter by category
        if($params ->cat_name){
            $promotions->where('categories.category_name',  $params ->cat_name);
        }

        $promotions->where('promotions.status',  'ACTIVE');


        $promotions->select(
            'categories.icon as cat_icon',
            'categories.category_id',
            'categories.category_name',
            'categories.parent_category_id',
            'categories.category_slug',
            'promotions.promotion_id',
            'promotions.promotion_name',
            'promotions.promotion_description',
            'promotions.promotion_image',
            'promotions.promotion_is_featured',
            'promotions.category_id',
            'promotions.company_id',
            'promotions.company_text',
            'promotions.meta_tags',
            'promotions.date',
            'promotions.time',
            'promotions.venue',
            'promotions.reservations',
            'promotions.offer_end_date',
            'promotions.promotion_unique_views',
            'promotions.promotion_views',
            'promotions.promotion_slug',
            'promotions.company_text',
            'parent_categories.parent_category_id',
            'parent_categories.icon',
            'parent_categories.parent_category_slug',
            /*'companies.company_id',*/
            'companies.company_name',
            'companies.company_description',
            'companies.company_tel1',
            'companies.company_tel2',
            'companies.company_address',
            'companies.company_email',
            'companies.company_website',
            'companies.company_fb',
            'companies.company_twitter',
            'companies.company_linkedin',
            'companies.company_logo',
            'companies.company_instagram',
            'companies.company_slug',
            'companies.company_views',
            'companies.company_unique_views'
        );


        return $this->setHeaders( [
            'promotions'     => $promotions->get()
        ], Response::HTTP_OK );
    }

    /**
     * Update promotion record
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function updatePromotion(Request $request){
        $this -> deleteExpiredPromotions();

        $promotion = Promotion::where('promotion_id', $request->id)
            ->update([
                'promotion_name'              => $request->promotion_name,
                'promotion_description'       => $request->promotion_description,
                'promotion_image'             => $request->promotion_image,
                'promotion_views'             => (int)$request->promotion_views,
                'promotion_is_featured'       => $request->promotion_is_featured,
                'category_id'                 => (int)$request->category_id,
                'company_id'                  => (isset($request->company_id)) ? (int)$request->company_id : null,
                'company_text'                => (isset($request->company_text)) ? $request->company_text : null,
                'modified_at'                 => Carbon::now('Asia/colombo'),
                'offer_end_date'              => $request->offer_end,
                /*'expire_date'               => Carbon::now('Asia/colombo')->addDays($request->extend),*/
                'promotion_unique_views'      => $request->fake_views,
                'meta_tags'                   => $request->meta_tags,
                'date'                        => $request->date,
                'time'                        => $request->time,
                'venue'                       => $request->venue,
                'reservations'                => $request->reservations
            ]);

        if($promotion){
            return $this->setHeaders( [
                'status'                => 'success',
                'msg'                   => 'Successfully updated details',
                'parent_category'       => $promotion,
            ], Response::HTTP_OK );
        }
    }

    /**
     * Delete promotion record
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function deletePromotion(Request $request){
        Promotion::destroy($request->id);

        $this -> deleteExpiredPromotions();

        return $this->setHeaders( ['status' => 'success'], Response::HTTP_OK );
    }


    /**
     * Add partner companies for promotions
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function addPartnerCompanies(Request $request){
        DB::table('partner_companies')
            ->insert([
                'promotion_id'          => $request -> promotion_id,
                'partner_company_id'    => $request -> company_id,
                'created_at'            => Carbon::now('Asia/colombo'),
                'modified_at'            => Carbon::now('Asia/colombo'),
            ]);
        return $this->setHeaders( ['status' => 'success'], Response::HTTP_OK );
    }

    /**
     * Get partner companies by promotion id
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getPartnerCompanies(Request $request){
        $partner_companies = DB::table('partner_companies')->where('promotion_id', $request->promotion_id)
            ->join('companies', 'companies.company_id', '=', 'partner_companies.partner_company_id');

        return $this->setHeaders( [
            'partner_companies'     => $partner_companies->get()
        ], Response::HTTP_OK );
    }

    public function deleteExpiredPromotions(){
        $promotion = Promotion::where('expire_date', Carbon::now('Asia/colombo'))
            ->update([
                'status'              => 'INACTIVE',
            ]);

        return true;
    }


    /**
     * Upload promotion image
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function uploadPromotionImage(Request $request){

        $promotion_image_path = 'images/promotions/';

        $file = $request -> file('promotion_image');

        $promotion_image_name = $file->getClientOriginalName();



        if ( ! $this->checkPromotionImageExists( $request->data['promotion_id'], $promotion_image_path ) ) {
            $this->createPromotionImageDir( $request->data['promotion_id'], $promotion_image_path );
        }

        $promotion_image_path      = $promotion_image_path . $request->data['promotion_id'];

        if ( $request->hasFile( 'promotion_image' ) ) {

            $fileObject = $request->file('promotion_image');
            // move the file from tmp to the destination path
            $fileObject->move($promotion_image_path, $promotion_image_name);
        }
    }

    /**
     * Check if promotion image already exits
     * -----------------------------------------------------------------------------------------------------------------
     */
    function checkPromotionImageExists($promotion_id, $promotion_image_path) {
        return file_exists( $promotion_image_path . $promotion_id );
    }

    /**
     * Create new directory fro promotions images by promotion id
     * -----------------------------------------------------------------------------------------------------------------
     */
    function createPromotionImageDir( $promotion_id, $promotion_image_path ) {
        return File::makeDirectory( $promotion_image_path . $promotion_id, 0777, TRUE, TRUE );
    }


    /**
     * Add views when user viewing a post
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function addViews(Request $request){
        $promotion = Promotion::where('promotion_id', $request -> id)->first();

        $promotion->promotion_views = $promotion->promotion_views + 1;

        $promotion->save();

        return $this->setHeaders( [
            'promotions'     => $promotion
        ], Response::HTTP_OK );
    }


    /**
     * Reset whole database (app backdoor)
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function ResetDb(){
        DB::table('categories')->truncate();
        DB::table('companies')->truncate();
        DB::table('parent_categories')->truncate();
        DB::table('promotions')->truncate();
        DB::table('partner_companies')->truncate();
        DB::table('reviews')->truncate();
    }

}
