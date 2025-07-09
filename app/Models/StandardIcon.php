<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardIcon extends Model
{
    use HasFactory;
    protected $table = 'standard_icon';
    protected $fillable = ['name','path','category_id'];
    
    
    public function category()
    {
        return $this->belongsTo(StandardIconCategory::class, 'category_id');  // 'category_id' is the correct column name
    }
}
