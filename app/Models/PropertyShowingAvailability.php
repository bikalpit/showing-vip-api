<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyShowingAvailability extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_showing_availability';

    protected $fillable = ['uuid','showing_setup_id','availability'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}