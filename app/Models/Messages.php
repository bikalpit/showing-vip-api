<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Messages extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'messages';

    protected $fillable = ['sender_id','receiver_id','message'];

    use SoftDeletes;
    
    public function sender(){
        return $this->hasOne('App\Models\Users', 'uuid', 'sender_id');
    }
}