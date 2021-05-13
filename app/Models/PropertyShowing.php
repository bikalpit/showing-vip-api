<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyShowing extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_owners';

    protected $fillable = ['uuid','property_id','notification_email','notification_text','showing_type','showing_validator','showing_presence','showing_instructions','lockbox_type','lockbox_location','showing_start_date','showing_end_date','showing_timeframe','showing_overlap','cancel_at'];

    use SoftDeletes;
    
    protected $hidden = [ 'password'];
    protected $dates = ['deleted_at'];
}