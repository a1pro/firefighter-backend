<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Building;
class Basement extends Model
{
    use HasFactory;
    
    protected $table = 'basement';
    protected $fillable = ['building_id', 'floor_name', 'floor_image','messge', 'status'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}

