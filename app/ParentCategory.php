<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParentCategory extends Model
{
    public $timestamps = false;
    public $primaryKey = 'parent_category_id';

    /*
     *  mass assignable attributes
     */
    protected $fillable = [
        'parent_category_id',
        'parent_category_name',
        'icon',
        'created_at',
        'modified_at',
        'parent_category_slug'
    ];
}