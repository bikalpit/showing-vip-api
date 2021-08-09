<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyValuecheck extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_valuecheck';

    protected $fillable = ['uuid','property_id','vs_listed','vs_streetnumber','vs_streetdirection','vs_streetname','vs_streettype','vs_unitnumber','vs_city','vs_state','vs_zipcode','vs_county','vs_countyname','vs_country','vs_apn','vs_assessyr','vs_assesmkt','vs_landmktval','vs_taxyr','vs_taxdue','vs_esttaxes','vs_ownername','vs_ownername2','vs_formallegal','vs_saledate','vs_docdate','vs_saleamt','vs_pricesqft','vs_longitude','vs_latitude','vs_proptype','vs_stories','vs_housestyle','vs_squarefeet','vs_bsmtsf','vs_finbsmtsf','vs_bsmttype','vs_bedrooms','vs_bathrooms','vs_garagetype','vs_garagesqft','vs_carspaces','vs_fireplaces','vs_heat','vs_cool','vs_extwall','vs_roofcover','vs_roofstyle','vs_yearblt','vs_lotsizec','vs_lotsize','vs_acre','vs_pool','vs_spa','vs_foundation','vs_golf','vs_lotwidth','vs_lotlength','vs_waterfront','vs_extwallcover','vs_intwall','vs_decksqft','vs_deckdesc','vs_patiosqft','vs_patiodesc','vs_waterservice','vs_sewerservice','vs_electricservice'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /*public function agentProfile(){
        return $this->hasOne('App\Models\Users', 'uuid', 'agent_id');
    }*/
}