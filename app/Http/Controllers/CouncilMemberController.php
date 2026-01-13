<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CouncilMember;


class CouncilMemberController extends Controller
{
    public function index()
    {
        // Aktif üyeleri çek, varsa 'sort_order'a göre yoksa 'name'e göre sırala
        // Not: sort_order sütunu yoksa ->orderBy('name') kullanabilirsiniz.
        $members = CouncilMember::where('is_active', true)
            ->orderBy('sort_order', 'asc') 
            ->orderBy('name', 'asc')
            ->get();

        return view('pages.meclis', compact('members'));
    }
}