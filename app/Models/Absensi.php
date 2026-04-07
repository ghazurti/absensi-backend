<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory, Auditable;

    protected $table = 'absensis';

    protected $fillable = [
        'user_id',
        'shift_id',
        'tanggal',
        'check_in',
        'check_out',
        'foto_check_in',
        'foto_check_out',
        'latitude_in',
        'longitude_in',
        'latitude_out',
        'longitude_out',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function getIsPswAttribute()
    {
        if (!$this->check_out || !$this->shift) return false;
        $tanggalStr = $this->tanggal instanceof \Carbon\Carbon ? $this->tanggal->toDateString() : \Carbon\Carbon::parse($this->tanggal)->toDateString();
        $jamKeluar = \Carbon\Carbon::parse($tanggalStr . ' ' . $this->shift->jam_keluar);
        return $this->check_out->lt($jamKeluar);
    }

    public function getIsLupaAbsenAttribute()
    {
        $tanggalObj = $this->tanggal instanceof \Carbon\Carbon ? $this->tanggal : \Carbon\Carbon::parse($this->tanggal);
        return !$this->check_out && $tanggalObj->isBefore(\Carbon\Carbon::today()) && !in_array($this->status, ['izin', 'sakit', 'alpha']);
    }
}
