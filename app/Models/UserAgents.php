<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAgents extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'user_agents';

    protected $fillable = ['user_id','agent_id'];

    public function agentProfile(){
        return $this->hasOne('App\Models\Users', 'uuid', 'agent_id');
    }
}