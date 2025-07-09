<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BuildingLayout;
class FloorImage extends Model
{
    use HasFactory;
    protected $table = 'floor_image';
    protected $fillable = ['building_layout_id', 'floor_number', 'image_path'];

    public function buildingLayout()
    {
        return $this->belongsTo(BuildingLayout::class);
    }
}
