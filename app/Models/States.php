<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'states';

    protected $fillable = [
        'name', 'country_id'
    ];
}