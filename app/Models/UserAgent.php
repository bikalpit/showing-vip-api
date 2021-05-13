<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAgent extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'user_agent';

    protected $fillable = ['user_id','agent_id'];
}