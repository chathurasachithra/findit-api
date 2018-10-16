<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    public $timestamps = false;
    public $primaryKey = 'promotion_id';

    /*
     *  mass assignable attributes
     */
    protected $fillable = [
        'promotion_id',
        'promotion_name',
        'promotion_description',
        'promotion_image',
        'promotion_views',
        'promotion_is_featured',
        'category_id',
        'company_id',
        'created_at',
        'modified_at',
        'expire_date',
        'offer_end_date',
        'status',
        'promotion_fake_views'
    ];
}