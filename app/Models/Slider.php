<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    /**
     * BU SATIR EKSİK OLDUĞU İÇİN HATA ALIYORSUNUZ.
     * $guarded = []; demek "Hiçbir sütunu koruma, hepsine veri yazılmasına izin ver" demektir.
     */
    protected $guarded = []; 
}