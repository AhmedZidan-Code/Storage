<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function city()
    {
        return $this->belongsTo(Area::class, 'city_id');
    }
    public function governorate()
    {
        return $this->belongsTo(Area::class, 'governorate_id');
    }
    public function representative()
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function representatives()
    {
        return $this->belongsToMany(Representative::class, 'representative_clients');
    }
}
