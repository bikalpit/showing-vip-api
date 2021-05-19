<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyCategories extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'survey_categories';

    protected $fillable = ['uuid','name'];

    public function subCategory(){
        return $this->hasMany('App\Models\SurveySubCategories', 'category_id', 'uuid');
    }
}