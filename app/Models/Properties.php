<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Properties extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'properties';

    protected $fillable = ['uuid','mls_id','data','verified'];

    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    public function Valuecheck(){
        return $this->hasOne('App\Models\PropertyValuecheck', 'property_id', 'uuid');
    }

    public function Zillow(){
        return $this->hasOne('App\Models\PropertyZillow', 'property_id', 'uuid');
    }

    public function Homendo(){
        return $this->hasOne('App\Models\PropertyHomendo', 'property_id', 'uuid');
    }
}