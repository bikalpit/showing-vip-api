<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyOwners extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_owners';

    protected $fillable = ['property_id','user_id','type','verification_token','verify_status'];

    public function User(){
        return $this->hasOne('App\Models\Users', 'uuid', 'user_id');
    }
}