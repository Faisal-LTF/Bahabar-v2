<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo',
        'description',
        'region',
        'event_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
