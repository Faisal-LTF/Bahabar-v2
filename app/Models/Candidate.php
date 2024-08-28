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
        'event_id',
        'regional_id',
        'regency_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
