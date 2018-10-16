<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;
    public $primaryKey = 'category_id';

    /*
     *  mass assignable attributes
     */
    protected $fillable = [
        'category_id',
        'category_name',
        'created_at',
        'modified_at',
        'parent_category_id',
        'icon',
        'category_slug'
    ];
}