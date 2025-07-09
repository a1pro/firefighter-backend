<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditDragIcons extends Model
{
    use HasFactory;

    protected $fillable = ['building_id','otp','otp_verify','otp_date','otp_verify_date','edited_by'];
}
