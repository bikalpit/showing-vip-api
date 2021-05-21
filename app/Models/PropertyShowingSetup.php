<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyShowingSetup extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_showing_setup';

    protected $fillable = ['uuid','property_id','notification_email','notification_text','type','validator','presence','instructions','lockbox_type','lockbox_location','start_date','end_date','timeframe','overlap','cancel_at'];

    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    public function showingAvailability(){
        return $this->hasOne('App\Models\PropertyShowingAvailability', 'showing_setup_id', 'uuid');
    }

    public function showingSurvey(){
        return $this->hasOne('App\Models\PropertyShowingSurvey', 'showing_setup_id', 'uuid');
    }

    public function Property(){
        return $this->hasOne('App\Models\Properties', 'uuid', 'property_id');
    }
}