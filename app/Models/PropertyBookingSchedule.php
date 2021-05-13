<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyBookingSchedule extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_owners';

    protected $fillable = ['uuid','buyer_id','property_id','booking_date','booking_time','status','cancel_by','cancel_reason'];

    use SoftDeletes;
    
    protected $hidden = [ 'password'];
    protected $dates = ['deleted_at'];
}