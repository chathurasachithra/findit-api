<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $timestamps = false;
    public $primaryKey = 'review_id';

    /*
     *  mass assignable attributes
     */
    protected $fillable = [
        'review_id',
        'reviewer_name',
        'reviewer_email',
        'review_description',
        'promotion_id',
        'created_at',
        'modified_at',
    ];
}