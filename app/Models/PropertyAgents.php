<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAgents extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_owners';

    protected $fillable = ['property_id','user_id','agent_id'];
}