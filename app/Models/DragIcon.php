<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StandardIcon;
class DragIcon extends Model
{
    use HasFactory;
    protected $table = 'drag_icon';
    protected $fillable = ['user_id','building_id','floor_id','basement_id','icon_id','image_upload','message','latitude','longitude','rotation'];


    public function icon() {
        return $this->belongsTo(StandardIcon::class, 'icon_id');
    }

    
}
