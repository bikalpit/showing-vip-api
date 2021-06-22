<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyBookingSchedule extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_booking_schedule';

    protected $fillable = ['uuid','buyer_id','property_id','agent_id','booking_date','booking_time','status','cancel_by','cancel_reason'];

    use SoftDeletes;
    
    protected $dates = ['deleted_at'];

    public function Property()
    {
    		return $this->hasOne('App\Models\Properties', 'uuid', 'property_id');
    }

    public function Buyer()
    {
    		return $this->hasOne('App\Models\Users', 'uuid', 'buyer_id');
    }

    public function Agent()
    {
    		return $this->hasOne('App\Models\Users', 'uuid', 'agent_id');
    }
}