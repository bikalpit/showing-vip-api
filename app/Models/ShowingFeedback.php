<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowingFeedback extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'showing_feedback';

    protected $fillable = ['booking_id','feedback'];
}