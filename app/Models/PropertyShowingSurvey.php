<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyShowingSurvey extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_showing_survey';

    protected $fillable = ['uuid','showing_setup_id','survey'];

    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    public function showing()
    {
        return $this->hasOne('App\Models\PropertyShowingSetup','uuid','showing_setup_id');
    }
}