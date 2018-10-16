<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\ParentCategory;
use Carbon\Carbon;

class ParentCategoryController extends ApiController
{

    /**
     * Send 200 status code to all option requests
     * ----------------------------------------------------------------------------------------------------
     */
    public function OptionResponse(){
        return $this->setHeaders( [

        ], Response::HTTP_OK );
    }

    /**
     * Add new parent category record
     * ----------------------------------------------------------------------------------------------------
     */
    public function store(Request $request)
    {
        $icon = null;

        // get base 64 image of icon
        if($request->icon){
            $step_1 = str_replace('<img src="',"", $request -> icon);
            $icon = str_replace('" />', '', $step_1);
        }
        
        $parent_category = new ParentCategory();

        $parent_category->parent_category_name  = $request->name;
        $parent_category->icon                  = $icon;
        $parent_category->created_at            =  Carbon::now('Asia/colombo');
        $parent_category->modified_at           =  Carbon::now('Asia/colombo');
        $parent_category->parent_category_slug           =  $this->generateSlug($request->name, 'parent_categories', 'parent_category_slug');

        $parent_category->save();

        if($parent_category){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully saved details',
                'parent_category'     => $parent_category,
            ], Response::HTTP_OK );
        }
    }

    /**
     * Get all parent categories with filters
     * ----------------------------------------------------------------------------------------------------
     */
    public function getAllParentCategories(Request $request)
    {
        // request params
        $params = $request;

        $parent_categories = DB::table('parent_categories');

        //filter by order
        if($params ->order_by && $params ->order && $params->order_by){
            $parent_categories = $parent_categories->orderBy($params ->order_by, $params ->order);
        }

        //Limit results
        if($params ->limit){
            $parent_categories = $parent_categories->limit($params ->limit);
        }

        if($params ->name){
            $parent_categories = $parent_categories -> where('parent_category_name', $params ->name);
        }

        if($params->slug){
            $parent_categories = $parent_categories -> where('parent_category_slug', $params->slug);
        }

        //filter by offset
        if($params ->offset){
            $parent_categories = $parent_categories->offset($params ->offset);
        }

        $parent_categories = $parent_categories->where('parent_category_id', '!=', 4);

        $mostPromotions = DB::table('promotions')->selectRaw('COUNT(promotions.category_id) as count, promotions.category_id')
            ->join('categories', 'categories.category_id', '=', 'promotions.category_id')
            ->groupBy('promotions.category_id')
            ->whereIn('parent_category_id', [1])
            ->orderBy('count', 'DESC')->take(12)->lists('promotions.category_id');

        $randomCategories = DB::table('categories')->whereIn('category_id', $mostPromotions)
            ->select('categories.*')->orderBy('category_name', 'ASC')->take(12)->get();
        $banners = DB::table('home_banners')->get();
        return $this->setHeaders( [
            'parent_categories'     => $parent_categories->get(),
            'random_categories'     => $randomCategories,
            'banners'               => $banners,
        ], Response::HTTP_OK );
    }

    /**
     * Delete parent category record
     * ----------------------------------------------------------------------------------------------------
     */
    public function deleteParentCategory(Request $request){
        ParentCategory::destroy($request->id);

        return $this->setHeaders( ['status'                => 'success'], Response::HTTP_OK );
    }

    /**
     * Update parent category record
     * ----------------------------------------------------------------------------------------------------
     */
    public function updateParentCategory(Request $request){
        $icon = null;

        // get base 64 image of icon
        if($request->icon){
            $step_1 = str_replace('<img src="',"", $request -> icon);
            $icon = str_replace('" />', '', $step_1);
        }

        $parent_category = ParentCategory::where('parent_category_id', $request->id)
            ->update([
                'parent_category_name'              => $request->parent_category_name ,
                'icon'                              => $icon,
                'created_at'                        => Carbon::now('Asia/colombo'),
                'modified_at'                       => Carbon::now('Asia/colombo'),
                'parent_category_slug'              => $this->generateSlug($request->parent_category_name, 'parent_categories', 'parent_category_slug')
            ]);

        if($parent_category){
            return $this->setHeaders( [
                'status'                => 'success',
                'msg'                   => 'Successfully updated details',
                'parent_category'       => $parent_category,
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
     * Get single parent category by id
     *---------------------------------------------------------------------------------------------------- 
     */
    public function getSingleParentCategory(Request $request){
        $parent_category = ParentCategory::where('parent_category_slug', $request -> id);
        return $this->setHeaders( [
            'parent_category'     => $parent_category ->get(),
        ], Response::HTTP_OK );
    }
}
