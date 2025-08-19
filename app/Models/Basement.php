<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Building;
class Basement extends Model
{
    use HasFactory;
    
    protected $table = 'basement';
    protected $fillable = ['building_id', 'basement_name', 'basement_number','basement_image','message', 'status'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}

