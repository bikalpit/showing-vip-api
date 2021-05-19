<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveySubCategories extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'survey_sub_categories';

    protected $fillable = ['uuid','category_id','name'];
}