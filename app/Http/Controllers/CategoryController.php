<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Category;
use Carbon\Carbon;

class CategoryController extends ApiController
{
    /**
     * Send 200 status code for all option requests
     * ------------------------------------------------------------------------------------------------------
     */
    public function OptionResponse(){
        return $this->setHeaders( [

        ], Response::HTTP_OK );
    }

    /**
     * Add new category record
     * ------------------------------------------------------------------------------------------------------
     */
    public function store(Request $request)
    {
        $icon = null;

        // get base 64 image of icon
        if($request->icon){
            $step_1 = str_replace('<img src="',"", $request -> icon);
            $icon = str_replace('" />', '', $step_1);
        }


        $category = new Category();

        $category->category_name            = $request->category_name;
        $category->parent_category_id       = (int)$request->parent_category_id;
        $category->icon                     = $icon;
        $category->created_at               = Carbon::now('Asia/colombo');
        $category->modified_at              = Carbon::now('Asia/colombo');
        $category->category_slug            = $this->generateSlug($request->category_name, 'categories', 'category_slug');

        $category->save();

        if($category){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully saved details',
                'category'     => $category,
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
     * Get categories with filters
     * ------------------------------------------------------------------------------------------------------
     */
    public function getAllCategories(Request $request)
    {

        // request params
        $params = $request;

        $categories = DB::table('categories');

        //filter by order
        if($params ->order_by && $params ->order && $params->order_by){
            $categories->orderBy($params ->order_by, $params ->order);
        }

        //Limit results
        if($params ->limit){
            $categories->limit($params ->limit);
        }
        
        //filter by parent category
        if($params->parent_category_id){
            $categories->where('categories.parent_category_id', $params ->parent_category_id);
        }


        if($params->category_name){
            $categories->where('category_name', $params ->category_name);
        }

        if($params->slug){
            $categories->where('category_slug', $params->slug);
        }

        //filter by offset
        if($params ->offset){
            $categories->offset($params ->offset);
        }

        $categories->join('parent_categories', 'parent_categories.parent_category_id', '=', 'categories.parent_category_id');

        if($params->parent_slug){
            $categories->where('parent_categories.parent_category_slug', $params ->parent_slug);
        }

        $categories->select('categories.*', 'parent_categories.icon as promo_icon', 'parent_categories.parent_category_id', 'parent_categories.parent_category_name', 'parent_categories.parent_category_slug');

        return $this->setHeaders( [
            'categories'     => $categories->get(),
        ], Response::HTTP_OK );
        
    }

    /**
     * Get single category
     * ------------------------------------------------------------------------------------------------------
     */
    public function getSingle(Request $request){
        $category = Category::where('category_slug', $request -> id);
        return $this->setHeaders( [
            'categories'     => $category ->get(),
        ], Response::HTTP_OK );
    }


    /**
     * Delete a category
     * ------------------------------------------------------------------------------------------------------
     */
    public function deleteCategory(Request $request){
        Category::destroy($request->id);

        return $this->setHeaders( ['status'                => 'success'], Response::HTTP_OK );
    }

    /**
     * Update category
     * ------------------------------------------------------------------------------------------------------
     */
    public function updateCategory(Request $request){
         $icon = null;

        // get base 64 image of icon
        if($request->icon){
            $step_1 = str_replace('<img src="',"", $request -> icon);
            $icon = str_replace('" />', '', $step_1);
        }

        $category = Category::where('category_id', $request->id)
            ->update([
                'category_name'             => $request->category_name ,
                'parent_category_id'        =>(int)$request->parent_category_id,
                'icon'                      => $icon,
                'created_at'                => Carbon::now('Asia/colombo'),
                'modified_at'               => Carbon::now('Asia/colombo'),
                'modified_at'               => $this->generateSlug($request->category_name, 'categories', 'category_slug')
            ]);

        if($category){
            return $this->setHeaders( [
                'status'   => 'success',
                'msg'      => 'Successfully updated details',
                'company'     => $category,
            ], Response::HTTP_OK );
        }
    }

    
}
