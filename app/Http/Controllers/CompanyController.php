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

class CompanyController extends ApiController
{
    /**
     * Validate option requests with 200 status code
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function OptionResponse(){
        return $this->setHeaders( [

        ], Response::HTTP_OK );
    }

    /**
     * Login for companies
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function AuthCompany(Request $request){
        $logged_company = DB::table('users')
            ->where('user_name', $request->username)
            ->where('user_password', $request->password)
            ->first();

        if($logged_company){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully authenticated!',
                'company'     => Company::find($logged_company->company_id)
            ], Response::HTTP_OK );
        }
        else{
            return $this->setHeaders( [
                'status'   => 'error',
                'msg'      => 'Invalid credentials',
            ], Response::HTTP_NOT_ACCEPTABLE );
        }
    }

    /**
     * Send to review company record
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveCompany(Request $request)
    {
        $company = new Company();

        $company->company_name = $request->name;
        $company->company_description = $request->desc;
        $company->company_tel1 = $request->tel1;
        $company->company_tel2 = $request->tel2;
        $company->company_address = $request->address;
        $company->company_email = $request->email;
        $company->company_website = $request->website;
        $company->company_fb = $request->fb;
        $company->company_twitter = $request->twitter;
        $company->company_linkedin = $request->linkedin;
        $company->company_youtube = $request->youtube;
        $company->company_instagram = $request->instagram;
        $company->category_id = (int)$request->category_id;
        $company->company_logo = $request->company_logo;
        $company->company_slug = $this->generateSlug($request->name, 'companies', 'company_slug');
        $company->company_unique_views = (int)$request->fake_views;
        $company->company_views = (int)0;
        $company->created_at =  Carbon::now('Asia/colombo');
        $company->modified_at =  Carbon::now('Asia/colombo');
        $company->company_banner = $request -> banner;
        $company->company_latitude = $request->latitude;
        $company->company_longitude = $request->longitude;
        $company->meta_tags = $request->meta_tags;
        $company->status = 0;

        $company->save();

        $custom_message = "Hello Admin, \n"
            .$custom_message = "New company has registered with find-it app. \n"
                .$custom_message = "Go to admin panel for more details. \n";

        $this->sendEmail($custom_message, $request->name);

        $custom_message2 = "Details successfully submitted. We will review your details and add into".
            " find-it within couple of hours. \n \n"
            .$custom_message = "Thanks & Regards\n"
                .$custom_message = "Team Findit.lk\n"
                    .$custom_message = "www.findit.lk";

        Mail::raw($custom_message2, function ($message) use ($company) {
            $message->to($company->company_email);
            $message->from(env('MAIL_FROM'), env('MAIL_SENDER_NAME'));
            $message->subject("Thank You For Registering! - Findit.lk");
        });

        if($company){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully saved details',
                'company'     => $company,
            ], Response::HTTP_OK );
        }
    }
    
    /**
     * Add company record
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function store(Request $request)
    {
        $company = new Company();
        
        $company->company_name = $request->name;
        $company->company_description = $request->desc;
        $company->company_tel1 = $request->tel1;
        $company->company_tel2 = $request->tel2;
        $company->company_address = $request->address;
        $company->company_email = $request->email;
        $company->company_website = $request->website;
        $company->company_fb = $request->fb;
        $company->company_twitter = $request->twitter;
        $company->company_linkedin = $request->linkedin;
        $company->company_youtube = $request->youtube;
        $company->company_instagram = $request->instagram;
        $company->category_id = (int)$request->category_id;
        $company->company_logo = $request->company_logo;
        $company->company_slug = $this->generateSlug($request->name, 'companies', 'company_slug');
        $company->company_unique_views = (int)$request->fake_views;
        $company->company_views = (int)0;
        $company->created_at =  Carbon::now('Asia/colombo');
        $company->modified_at =  Carbon::now('Asia/colombo');
        $company->company_banner = $request -> banner;
        $company->company_latitude = $request->latitude;
        $company->company_longitude = $request->longitude;
        $company->meta_tags = $request->meta_tags;

        $company->save();

        /*if($request->customer_portal){

            $generated_password = $this->generateRandomString();

            DB::table('users')
                ->insert([
                   'user_name'              => $company->company_slug,
                   'user_password'          => $generated_password,
                   'company_id'             => $company->company_id,
                   'modified_at'            => Carbon::now('Asia/colombo'),
                   'created_at'            => Carbon::now('Asia/colombo')
                ]);

            $custom_message = "Hello Malitha, \n"
            .$custom_message = "New company has registered with our app with following details \n"
            .$custom_message = "Company name : ".$company->company_name." \n"
            .$custom_message = "Company description : ".$company->company_description." \n"
            .$custom_message = "Company email : ".$company->company_email  ." \n"
            .$custom_message = "Company tel no : ".$company->company_tel1." \n"
            .$custom_message = "Company land no : ".$company->company_tel2  ." \n"
            .$custom_message = "Company address : ".$company->company_address ." \n"
            .$custom_message = "Company facebook : ".$company->company_fb." \n"
            .$custom_message = "Company twitter : ".$company->company_twitter ." \n"
            .$custom_message = "Company youtube : ".$company->company_youtube ." \n"
            .$custom_message = "Company linkedIn : ".$company->company_linkedin  ." \n"
            .$custom_message = "Company instagram : ".$company->company_instagram  ." \n"
            .$custom_message = "Go to admin panel for more details \n"
            .$custom_message = "------------------------------------------------------------ \n"
            .$custom_message = "username : ". $company->company_slug." \n"
            .$custom_message = "password : ". $generated_password ." \n"
            .$custom_message = "------------------------------------------------------------ \n"
            .$custom_message = Carbon::now('Asia/colombo');

            $this->sendEmail($custom_message);
        }*/

        if($company){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully saved details',
                'company'     => $company,
            ], Response::HTTP_OK );
        }
    }

    private function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
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
     * Get All function with filters
     * -----------------------------------------------------------------------------------------------------------------
     */
    function getAllCompanies(Request $request){

        // request params
        $params = $request;

        $companies = DB::table('companies');

        //filter by order
        if($params ->order_by && $params ->order && $params->order_by){
            $companies->orderBy($params ->order_by, $params ->order);
        }

        if($params ->cat){
            $companies -> where('category_id', $params->cat);
        }

        if(isset($params->type) && $params ->type == 1){
            $companies -> where('companies.status', 1);
        }

        //Limit results
        if($params ->limit){
            $companies->limit($params ->limit);
        }

        //filter by offset
        if($params ->offset){
            $companies->offset($params ->offset);
        }

        if($params ->name){
            $companies -> where('company_name', $params->name);
        }

        if($params ->slug){
            $companies -> where('company_slug', $params->slug);
        }

        return $this->setHeaders( [
            'companies'     => $companies->get(),
        ], Response::HTTP_OK );
    }

    /**
     * Get single company
     * ------------------------------------------------------------------------------------------------------
     */
    public function getSingle(Request $request){

        $companies = Company::where('company_slug', $request->id)->get();
        $images = [];
        $videos = [];
        if (isset($companies[0])) {
            $images = DB::table('company_promos')
                ->where('company_id', $companies[0]->company_id)
                ->where('type', 1)->get();
            $videos = DB::table('company_promos')->select('image_name')
                ->where('company_id', $companies[0]->company_id)
                ->where('type', 2)->get();
            $collection = collect($videos);
            $videos = $collection->pluck('image_name')->all();
        }
        return $this->setHeaders( [
            'companies'  => $companies,
            'images'     => $images,
            'videos'     => implode(',', $videos)
        ], Response::HTTP_OK );
    }

    /**
     * Update company record
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function updateCompany(Request $request){
        
        $company = Company::where('company_id', $request->id)
            ->update([
                'company_name'           => $request->name ,
                'company_description'    => $request->desc,
                'company_tel1'           => $request->tel1,
                'company_tel2'           => $request->tel2,
                'company_address'        => $request->address,
                'company_email'          => $request->email,
                'company_website'        => $request->website,
                'company_fb'             => $request->fb,
                'company_twitter'        => $request->twitter,
                'company_linkedin'       => $request->linkedin,
                'company_youtube'        => $request->youtube,
                'company_instagram'      => $request->instagram,
                'category_id'            => (int)$request->category_id,
                'company_logo'           => $request->company_logo,
                'company_views'          => $request->company_views,
                'company_unique_views'    => $request->fake_views,
                'created_at'             => Carbon::now('Asia/colombo'),
                'modified_at'            => Carbon::now('Asia/colombo'),
                'company_banner'         => $request -> banner,
                'company_latitude'      => $request -> latitude,
                'company_longitude'     => $request -> longitude,
                'meta_tags'             => $request -> meta_tags,
                'status'                 => $request -> status
            ]);

        if($company){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully updated details',
                'company'     => $company,
            ], Response::HTTP_OK );
        }
    }

    /**
     * Delete company record
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function deleteCompany(Request $request){
        Company::destroy($request->id);

        return $this->setHeaders( ['status'                => 'success'], Response::HTTP_OK );
    }

    /**
     * Upload company logo
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function uploadCompanyLogo(Request $request){

        $logo_image_path = 'images/companies/';

        $file = $request -> file('company_logo');

        $logo_image_name = $file->getClientOriginalName();


        if ( ! $this->checkCompanyLogoExists( $request->data['company_id'], $logo_image_path ) ) {
            $this->createLogoImageDir( $request->data['company_id'], $logo_image_path );
        }

        $logo_image_path      = $logo_image_path . $request->data['company_id'];

        if ( $request->hasFile( 'company_logo' ) ) {

            $fileObject = $request->file('company_logo');
            // move the file from tmp to the destination path
            $fileObject->move($logo_image_path, $logo_image_name);
        }
    }

    /**
     * Upload new company logo
     * -------------------------------------------------------------------------------------------------------------
     */
    public function uploadCompanyBanner(Request $request){

        $image_path = 'images/banners/';

        $file = $request -> file('banner');

        $image_name = $file->getClientOriginalName();

        $this->createBannerImageDir( $request->data['company_id'], $image_path );

        $logo_image_path      = $image_path . $request->data['company_id'];

        if ( $request->hasFile( 'banner' ) ) {

            $fileObject = $request->file('banner');
            // move the file from tmp to the destination path
            $fileObject->move($logo_image_path, $image_name);
        }
    }
    
    /**
     * Create company banner DIR
     * -----------------------------------------------------------------------------------------------------------------
     */
    private function createBannerImageDir($banner_image_path, $banner_id){
        return File::makeDirectory( $banner_image_path . $banner_id, 0777, TRUE, TRUE );
    }

    /**
     * Check if company logo already exits
     * -----------------------------------------------------------------------------------------------------------------
     */
    function checkCompanyLogoExists($company_id, $logo_image_path) {
        return file_exists( $logo_image_path . $company_id );
    }

    /**
     * Check logo directory already exits
     * -----------------------------------------------------------------------------------------------------------------
     */
    function createLogoImageDir( $company_id, $logo_image_path ) {
        return File::makeDirectory( $logo_image_path . $company_id, 0777, TRUE, TRUE );
    }

    /**
     * Add views when user viewing a company page
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function addViews(Request $request){
        $company = Company::where('company_id', $request -> id)->first();

        $company->company_views = $company->company_views + 1;

        $company->save();

        return $this->setHeaders( [
            'promotions'     => $company
        ], Response::HTTP_OK );
    }

    public function sendEmail($custom_message, $name) {

        Mail::raw($custom_message, function ($message) use ($name){
            $message->to([env('MAIL_TO_NOTIFY'), 'chathurasachithra@gmail.com']);
            $message->from(env('MAIL_FROM'), env('MAIL_SENDER_NAME'));
            $message->subject("New Company Registration - " . $name);
        });
        return true;
    }

    /**
     * Upload images
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function uploadCompanyPromoImage(Request $request){

        $promo_image_path = 'images/company-promo/';
        $file = $request->file('image');
        $promo_image_name = Carbon::now()->timestamp . '-' . $file->getClientOriginalName();
        if ( $request->hasFile( 'image' ) ) {
            $fileObject = $request->file('image');
            // move the file from tmp to the destination path
            $fileObject->move($promo_image_path, $promo_image_name);
            DB::table('company_promos')->insert([
                'company_id' => $request->data['company_id'],
                'title' => $request->data['title'],
                'description' => $request->data['description'],
                'image_name' => $promo_image_name,
                'type' => 1
            ]);
        }
        return $this->setHeaders( ['status'=> 'success'], Response::HTTP_OK );
    }

    /**
     * Save video URLs
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function saveCompanyVideoUrls(Request $request){

        $companyId = $request->id;
        $videos = explode(',', $request->videos);
        DB::table('company_promos')->where('company_id', $companyId)->where('type', 2)->delete();
        foreach ($videos as $video) {
            DB::table('company_promos')->insert([
                'company_id' => $companyId,
                'image_name' => trim($video),
                'type' => 2

            ]);
        }
        return $this->setHeaders( ['status'=> 'success'], Response::HTTP_OK );
    }

    /**
     * Save video URLs
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function deletePromoImage(Request $request){

        $imageId = $request->id;
        DB::table('company_promos')->where('id', $imageId)->where('type', 1)->delete();
        return $this->setHeaders( ['status'=> 'success'], Response::HTTP_OK );
    }
    
}
