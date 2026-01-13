<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
      use HasFactory;

    /**
     * BU SATIR EKSİK OLDUĞU İÇİN HATA ALIYORSUNUZ.
     * $guarded = []; demek "Hiçbir sütunu koruma, hepsine veri yazılmasına izin ver" demektir.
     */
    protected $guarded = []; 
}
