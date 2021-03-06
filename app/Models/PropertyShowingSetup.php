<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Users;

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

    public function Validator(){
        return $this->hasMany('App\Models\Users', 'uuid', 'validator');
    }

    public function Presence(){
        return $this->hasMany('App\Models\Users', 'uuid', 'presence');
    }

    public function getValidatorAttribute($value){
        $check_json = json_decode($value);
        if ($value == null || $value == '') {
            return null;
        }elseif ($check_json === null) {
            return null;
        }else{
            return Users::whereIn('uuid', json_decode($value))->get();    
        }
    }
    
    public function getPresenceAttribute($value){
        $check_json = json_decode($value);
        if ($value == null || $value == '') {
            return null;
        }elseif ($check_json === null) {
            return null;
        }else{
            return Users::whereIn('uuid', json_decode($value))->get();    
        }
    }
}