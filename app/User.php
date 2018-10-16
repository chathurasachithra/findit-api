<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;
    public $primaryKey = 'user_id';

    /*
     *  mass assignable attributes
     */
    protected $fillable = [
       'user_id',
       'user_name',
       'user_password',
       'created_at',
       'modified_at',
    ];
}