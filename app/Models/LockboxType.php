<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LockboxType extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'lockbox_type';

    protected $fillable = ['uuid','name'];
}