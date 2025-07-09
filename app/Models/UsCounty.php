<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsCounty extends Model
{
    use HasFactory;
    protected $table = 'uscounties';
    protected $fillable = ['county','county_ascii','county_full','county_fips','state_id','state_name','lat','lng','population'];
}
