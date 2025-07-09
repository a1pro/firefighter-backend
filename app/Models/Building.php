<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Floor;

class Building extends Model
{
    use HasFactory;
    protected $table = "buildings";
    
    protected $fillable = [
        'user_id',
        'building_name',
        'building_address',
        'total_floor',
         'zipcode',
         'lat',
         'lon',
        'status',
    ];

    // Relationship with floors
    public function floors()
    {
        return $this->hasMany(Floor::class);
    }

    // Relationship with basements
    public function basements()
    {
        return $this->hasMany(Basement::class);  // Fixed name to match plural basements
    }
}
?>
