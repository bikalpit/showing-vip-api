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

    protected $fillable = ['uuid','property_id','hmdo_listed','hmdo_lastupdated','hmdo_mls_agent_email','hmdo_mls_agentid','hmdo_mls_description','hmdo_mls_id','hmdo_mls_originator','hmdo_mls_proptype','hmdo_mls_propname','hmdo_mls_status','hmdo_mls_price','hmdo_mls_streetnumber','hmdo_mls_streetdirection','hmdo_mls_streetname','hmdo_mls_streettype','hmdo_mls_unitnumber','hmdo_mls_city','hmdo_mls_zipcode','hmdo_mls_state','hmdo_mls_latitude','hmdo_mls_longitude','hmdo_mls_yearbuilt','hmdo_mls_beds','hmdo_mls_baths','hmdo_mls_sqft','hmdo_mls_acres','hmdo_mls_carspaces','hmdo_mls_url','hmdo_mls_thumbnail','hmdo_mls_officeid'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /*public function agentProfile(){
        return $this->hasOne('App\Models\Users', 'uuid', 'agent_id');
    }*/
}