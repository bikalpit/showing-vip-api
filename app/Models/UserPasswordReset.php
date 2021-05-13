<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPasswordReset extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'user_password_reset';

    protected $fillable = ['email', 'token'];
}