<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditorAssignArea extends Model
{
    use HasFactory;
    protected $table = 'editor_assign_area';
    protected $fillable=['user_id','county','country','city','state','pincode'];
    
    
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');  // 'city_id' is the foreign key in area_alloted table
    }
}
