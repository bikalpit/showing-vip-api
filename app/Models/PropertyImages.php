<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyImages extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'property_images';

    protected $fillable = ['property_id','image_name'];

    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}