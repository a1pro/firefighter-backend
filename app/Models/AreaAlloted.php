<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaAlloted extends Model
{
    use HasFactory;
    protected $table = 'area_alloted';
    protected $fillable=['user_id','area','country','city','state','county','pincode'];
    
    
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');  // 'city_id' is the foreign key in area_alloted table
    }
}
