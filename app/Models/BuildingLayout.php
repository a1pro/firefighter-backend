<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FloorImage;

class BuildingLayout extends Model
{
    use HasFactory;
    protected $table    = 'building_layout';
    protected $fillable = ['building_name','address','number_of_floors','floor_image'];

    public function floorImages()
    {
        return $this->hasMany(FloorImage::class);
    }

}
