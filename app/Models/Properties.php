<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Properties extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_owners';

    protected $fillable = ['uuid','mls_id','agent_id','property_verified','property_title','property_type','property_size','property_status','property_year_built','lat_area','elementary','middle','high','district','phone','office','hoa','taxes','parking','sources','disclaimer'];

    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}