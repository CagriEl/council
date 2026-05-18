<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'url', 
        'order', 
        'location', 
        'parent_id', 
        'page_id', 
        'is_active'
    ];

    // Sayfa İlişkisi
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    // Alt menüleri getirir
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    // Üst menüyü getirir
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }
}