<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Halaman katalog bucket.
     */
    public function index(Request $request)
    {
        $query = Bucket::query()->whereNull('deleted_at');

        // Optional pencarian sederhana
        if ($search = $request->get('q')) {
            $query->where('nama_bucket', 'like', "%$search%")
                  ->orWhere('deskripsi', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%")
                  ;
        }

        $buckets = $query->orderBy('nama_bucket')->paginate(12);

        return view('customer.home', compact('buckets', 'search'));
    }

    /**
     * Detail bucket.
     */
    public function show(int $id)
    {
        $bucket = Bucket::findOrFail($id);
        return view('customer.detail', compact('bucket'));
    }

    public function shop()
    {
        $buckets = Bucket::all();
        return view('customer.shop', compact('buckets'));
    }

    
}