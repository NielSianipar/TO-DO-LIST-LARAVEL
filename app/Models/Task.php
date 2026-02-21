<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['nama_tugas', 'user_id', 'is_selesai', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
