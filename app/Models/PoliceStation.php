<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliceStation extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'district_id','bn_name','url','status'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function postOffices()
    {
        return $this->hasMany(PostOffice::class);
    }
}
