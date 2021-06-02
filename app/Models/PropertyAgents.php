<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAgents extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_agents';

    protected $fillable = ['property_id','user_id','agent_id'];

    public function property()
    {
        return $this->hasOne('App\Models\Properties','uuid','property_id');
    }
    
    public function owner()
    {
        return $this->hasOne('App\Models\Users','uuid','user_id');
    }
}