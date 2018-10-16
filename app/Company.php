<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;
    public $primaryKey = 'company_id';

    /*
     *  mass assignable attributes
     */
    protected $fillable = [
        'company_id',
        'company_name',
        'company_description',
        'company_tel1',
        'company_tel2',
        'company_address',
        'company_email',
        'company_website',
        'company_fb',
        'company_twitter',
        'company_linkedin',
        'company_youtube',
        'category_id',
        'company_logo',
        'created_at',
        'modified_at',
        'company_instagram',
        'company_slug',
        'company_views',
        'company_other_views',
        'company_banner',
        'company_latitude',
        'company_longitude'
    ];
}