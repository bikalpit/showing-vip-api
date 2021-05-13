<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyShowingAvilability extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_owners';

    protected $fillable = ['uuid','showing_setup_id','date','time'];

    use SoftDeletes;
    
    protected $hidden = [ 'password'];
    protected $dates = ['deleted_at'];
}