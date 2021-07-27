<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PropertyVerification extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_verification';

    protected $fillable = ['property_id','agent_id','user_id','token','send_time'];

}