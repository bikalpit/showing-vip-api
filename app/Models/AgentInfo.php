<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentInfo extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'agent_info';

    protected $fillable = ['hmdo_lastupdated','hmdo_mls_originator','hmdo_agent_name','hmdo_agent_title','hmdo_agent_photo_url','hmdo_agent_email','hmdo_office_main_phone','hmdo_office_direct_phone','hmdo_agent_mobile_phone','hmdo_agent_skills','hmdo_office_id','hmdo_office_name','hmdo_office_photo','hmdo_office_street','hmdo_office_city','hmdo_office_zipcode','hmdo_office_state','hmdo_office_phone','hmdo_office_website','hmdo_agent_website'];

    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
}