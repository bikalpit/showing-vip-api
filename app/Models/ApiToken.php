<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
class ApiToken extends Model implements Authenticatable
{
		use AuthenticableTrait;
    /*
     * The table associated with the model.
     */
    protected $table = 'api_token';

    protected $fillable = ['user_id','token','user_type'];
}