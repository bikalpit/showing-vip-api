<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Properties extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'properties';

    protected $fillable = ['uuid','mls_id','data'];

    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}