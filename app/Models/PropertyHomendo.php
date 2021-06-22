<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyHomendo extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_homendo';

    protected $fillable = ['uuid','property_id','hmdo_listed','hmdo_lastupdated','hmdo_mls_id','hmdo_mls_originator','hmdo_mls_proptype','hmdo_mls_propname','hmdo_mls_status','hmdo_mls_price','hmdo_mls_url','hmdo_mls_thumbnail'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /*public function agentProfile(){
        return $this->hasOne('App\Models\Users', 'uuid', 'agent_id');
    }*/
}