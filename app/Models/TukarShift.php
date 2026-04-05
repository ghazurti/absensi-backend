<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TukarShift extends Model
{
    protected $fillable = [
        'user_pengaju_id',
        'user_penerima_id',
        'shift_pengaju_id',
        'shift_penerima_id',
        'alasan',
        'status',
        'catatan_admin',
    ];

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'user_pengaju_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'user_penerima_id');
    }

    public function shiftPengaju()
    {
        return $this->belongsTo(Shift::class, 'shift_pengaju_id');
    }

    public function shiftPenerima()
    {
        return $this->belongsTo(Shift::class, 'shift_penerima_id');
    }
}
