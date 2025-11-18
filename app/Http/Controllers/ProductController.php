<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Menampilkan daftar semua produk
    public function index()
    {
        // Generate 20 produk random untuk ditampilkan
        $products = [];
        for ($i = 1; $i <= 20; $i++) {
            $products[] = [
                'id' => $i,
                'name' => 'Product ' . $i,
                'description' => 'Description for product ' . $i . '. This is a sample product description.',
                'price' => rand(10000, 500000)
            ];
        }
        
        return view('products.list', compact('products'));
    }

    // Menampilkan form untuk membuat produk baru
    public function create()
    {
        return view('products.form', ['product' => null, 'isEdit' => false]);
    }

    // Menyimpan produk baru ke database
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);

        // Di sini seharusnya menyimpan ke database
        // Untuk sekarang, redirect dengan pesan sukses
        return redirect()->route('products')
            ->with('success', 'Product created successfully!');
    }

    // Menampilkan detail satu produk
    public function show($id)
    {
        // Generate produk dummy berdasarkan ID
        $product = [
            'id' => $id,
            'name' => 'Product ' . $id,
            'description' => 'This is the detailed description for product ' . $id . '. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'price' => rand(10000, 500000)
        ];

        return view('products.show', compact('product'));
    }

    // Menampilkan form untuk edit produk
    public function edit($id)
    {
        // Generate produk dummy berdasarkan ID
        $product = [
            'id' => $id,
            'name' => 'Product ' . $id,
            'description' => 'Description for product ' . $id,
            'price' => rand(10000, 500000)
        ];

        return view('products.form', ['product' => $product, 'isEdit' => true]);
    }

    // Update produk yang sudah ada
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);

        // Di sini seharusnya update ke database
        // Untuk sekarang, redirect dengan pesan sukses
        return redirect()->route('products')
            ->with('success', 'Product updated successfully!');
    }
}