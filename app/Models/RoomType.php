<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RoomType extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function room(){
        return $this->belongsTo(Room::class, 'id', 'roomtype_id');
    }
}
