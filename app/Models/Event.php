<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',            // Nama event
        'start_date',      // Tanggal mulai event
        'end_date',        // Tanggal berakhir event
        'voting_type',     // Tipe voting
        'description',     // Deskripsi event
        'regional_id',     // ID provinsi terkait (didapat dari API)
        'regency_id',      // ID kabupaten/kota terkait (didapat dari API)
    ];

    /**
     * Get the candidates for the event.
     */
    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
