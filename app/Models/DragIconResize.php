<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DragIconResize extends Model
{
    use HasFactory;
    protected $table = 'drage_icon_resize';

protected $fillable = [
    'icon_id','icon_lat','building_id','basement_id','floor_id','icon_long','point1_lat','pont1_long','point2_lat','pont2_long','point3_lat','pont3_long','point4_lat','pont4_long'];



     public function icon()
    {
        return $this->belongsTo(StandardIcon::class, 'icon_id');
    }

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function basement()
    {
        return $this->belongsTo(Basement::class, 'basement_id');
    }
}
