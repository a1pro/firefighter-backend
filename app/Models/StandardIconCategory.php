<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardIconCategory extends Model
{
    use HasFactory;

    // Table name and fillable fields
    protected $table = 'standard_icon_category';
    protected $fillable = ['category_name','category_image'];  // Ensure this is correct

    // Relationship to StandardIcon
    public function icons()
    {
        return $this->hasMany(StandardIcon::class, 'category_id');
    }
}


?>