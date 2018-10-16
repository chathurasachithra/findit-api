<?php

/**
 * Option requests
 */
Route::group( [ 'middleware' => [ 'headers' ], 'prefix' => 'api/v1' ], function () {
    // user module -------------------------------------------
    Route::options( 'user',                 'UserController@OptionResponse' );
    Route::options( 'user/logout',          'UserController@OptionResponse' );
    
    // promotions module -------------------------------------
    Route::options( 'promotion',            'PromotionController@OptionResponse' );
    Route::options( 'promotion/image',      'PromotionController@OptionResponse' );
    Route::options( 'promotion/partner',      'PromotionController@OptionResponse' );

    // company module ----------------------------------------
    Route::options( 'company',              'CompanyController@OptionResponse' );
    Route::options( 'company/logo',         'CompanyController@OptionResponse' );
    Route::options( 'company/banner',         'CompanyController@OptionResponse' );
    Route::options( 'company/auth',         'CompanyController@OptionResponse' );

    Route::options( 'company-front',        'CompanyController@OptionResponse' );

    Route::options( 'company-promo',        'CompanyController@OptionResponse' );

    Route::options( 'home-banners',        'HomeController@OptionResponse' );

    // sub category module -----------------------------------
    Route::options( 'category',             'CategoryController@OptionResponse' );
    
    // parent category module --------------------------------
    Route::options( 'parent_category',      'ParentCategoryController@OptionResponse' );

    // customer module ---------------------------------------
    Route::options( 'customer',                'ParentCategoryController@OptionResponse' );
    Route::options( 'customer/interests',      'ParentCategoryController@OptionResponse' );

} );

/**
 * All requests (GET, POST, PUT, DELETE )
 */
Route::group( [ 'middleware' => [ 'api' ], 'prefix' => 'api/v1' ], function () {

    /* ------------------------ User module end points begin ---------------------------------------------*/
    Route::post     ( 'user',                   'UserController@store' );
    Route::post     ( 'user/auth',              'UserController@login' );
    Route::post     ( 'user/logout',            'UserController@logOut' );
    /* ------------------------ User module end points end -----------------------------------------------*/


    /* ------------------------ Company module end points begin ------------------------------------------*/
    Route::post     ( 'company',                'CompanyController@store' );
    Route::post     ( 'company/logo',           'CompanyController@uploadCompanyLogo' );
    Route::post     ( 'company/banner',         'CompanyController@uploadCompanyBanner' );
    Route::post     ( 'company/auth',           'CompanyController@AuthCompany' );
    Route::get      ( 'company/views',          'CompanyController@addViews' );
    Route::get      ( 'company',                'CompanyController@getAllCompanies' );
    Route::get      ( 'company/{id}',           'CompanyController@getSingle' );
    Route::delete   ( 'company/{id}',           'CompanyController@deleteCompany' );
    Route::put      ( 'company/{id}',           'CompanyController@updateCompany' );

    Route::post     ( 'company-front',           'CompanyController@saveCompany' );
    Route::get      ( 'company-front',           'CompanyController@getAllCompanies' );
    Route::get      ( 'company-front/{id}',      'CompanyController@getSingle' );
    Route::delete   ( 'company-front/{id}',      'CompanyController@deleteCompany' );
    Route::put      ( 'company-front/{id}',      'CompanyController@updateCompany' );

    Route::post     ( 'company-promo',           'CompanyController@uploadCompanyPromoImage' );
    Route::get      ( 'company-promo/{id}',      'CompanyController@getSingle' );
    Route::delete   ( 'company-promo/{id}',      'CompanyController@deletePromoImage' );
    Route::put      ( 'company-promo/{id}',      'CompanyController@saveCompanyVideoUrls' );
    /* ------------------------ Company module end points end --------------------------------------------*/


    /* ------------------------ Category module end points begin -----------------------------------------*/
    Route::post     ( 'category',               'CategoryController@store' );
    Route::get      ( 'category',               'CategoryController@getAllCategories' );
    Route::get      ( 'category/{id}',          'CategoryController@getSingle' );
    Route::delete   ( 'category/{id}',          'CategoryController@deleteCategory' );
    Route::put      ( 'category/{id}',          'CategoryController@updateCategory' );
    /* ------------------------ Category module end points end  ------------------------------------------*/


    /* ------------------------ Parent category module end points begin  ---------------------------------*/
    Route::post     ( 'parent_category',        'ParentCategoryController@store' );
    Route::get      ( 'parent_category',        'ParentCategoryController@getAllParentCategories' );
    Route::get      ( 'parent_category/{id}',   'ParentCategoryController@getSingleParentCategory' );
    Route::delete   ( 'parent_category/{id}',   'ParentCategoryController@deleteParentCategory' );
    Route::put      ( 'parent_category/{id}',   'ParentCategoryController@updateParentCategory' );
    /* ------------------------ Parent category module end points end  -----------------------------------*/

    /* ------------------------ Promotion module end points begin  ---------------------------------------*/
    Route::post     ( 'promotion',              'PromotionController@store' );
    Route::post     ( 'promotion/image',        'PromotionController@uploadPromotionImage' );
    Route::post     ( 'promotion/partner',      'PromotionController@addPartnerCompanies' );
    Route::get      ( 'promotion/partner',      'PromotionController@getPartnerCompanies' );
    Route::get      ( 'promotion/views',        'PromotionController@addViews' ); // this is add function (post)
    Route::get      ( 'promotion',              'PromotionController@getAllWithFilters' );
    Route::get      ( 'promotion/{p_id}',       'PromotionController@getSinglePromotion' );
    Route::put      ( 'promotion/{id}',         'PromotionController@updatePromotion' );
    Route::delete   ( 'promotion/{id}',         'PromotionController@deletePromotion' );
    /* ------------------------ Promotion module end points end  -----------------------------------------*/
    
    /*------------------------- Customer module end points begin -----------------------------------------*/
    Route::post     ( 'customer',               'CustomerController@store' );
    Route::post     ( 'customer/interests',     'CustomerController@addInterests' );
    Route::get      ( 'customer',               'CustomerController@addInterests' );
    /*------------------------- Customer module end points end -------------------------------------------*/

    /*------------------------- Home banner module end points begin -----------------------------------------*/
    Route::post     ( 'home-banners',           'HomeController@uploadHomeBannerImage' );
    Route::get      ( 'home-banners',      'HomeController@getBanners' );
    Route::delete   ( 'home-banners/{id}',      'HomeController@deleteBannerImage' );
    /*------------------------- Home banner module end points end -----------------------------------------*/

    // review module -------------------------------------------
    Route::get( 'review/add',   'ReviewController@store' );
    Route::get( 'review',       'ReviewController@getReviewsByPromoId' );

    // Admin only back door
    Route::post( 'resetdb',     'PromotionController@ResetDb' );
    
} );
