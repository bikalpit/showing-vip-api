<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'cities';

    protected $fillable = [
        'name', 'state_id'
    ];
}