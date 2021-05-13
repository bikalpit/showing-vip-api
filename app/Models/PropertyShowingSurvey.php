<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyShowingSurvey extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_owners';

    protected $fillable = ['uuid','showing_setup_id','question_id'];

    use SoftDeletes;
    
    protected $hidden = [ 'password'];
    protected $dates = ['deleted_at'];
}