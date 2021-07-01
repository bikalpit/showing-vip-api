<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'messages';

    protected $fillable = ['sender_id','receiver_id','message'];

    public function sender(){
        return $this->hasOne('App\Models\Users', 'uuid', 'sender_id');
    }
}