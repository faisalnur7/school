<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'division_id','bn_name','lat','lon','url','status'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function policeStations()
    {
        return $this->hasMany(PoliceStation::class);
    }
}
