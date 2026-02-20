<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Tambahkan baris ini:
    protected $fillable = ['nama_tugas'];
}
