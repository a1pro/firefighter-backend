<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;
    protected $table= 'floors';
    protected $fillable = [
        'building_id',
        'floor_name',
        'floor_number',
        'floor_image',
        'message',
        'status',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}

?>
