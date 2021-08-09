<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyZillow extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_zillow';

    protected $fillable = ['uuid','property_id','z_listed','z_zpid','z_sale_amount','z_sale_lowrange','z_sale_highrange','z_sale_lastupdated','z_rental_amount','z_rental_lowrange','z_rental_highrange','z_rental_lastupdated','z_prop_url'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /*public function agentProfile(){
        return $this->hasOne('App\Models\Users', 'uuid', 'agent_id');
    }*/
}