<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bucket;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort');

        // Mulai query dari model Bucket
        $buckets = Bucket::query();

        // Sorting berdasarkan pilihan user
        if ($sort === 'low') {
            $buckets->orderBy('harga', 'asc');
        } elseif ($sort === 'high') {
            $buckets->orderBy('harga', 'desc');
        } else {
            $buckets->latest(); // default: terbaru
        }

        return view('customer.shop', [
            'buckets' => $buckets->get(),
            'sort' => $sort,
        ]);
    }
}
