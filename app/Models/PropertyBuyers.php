<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyBuyers extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_buyers';

    protected $fillable = ['property_id','user_id','agent_id'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}