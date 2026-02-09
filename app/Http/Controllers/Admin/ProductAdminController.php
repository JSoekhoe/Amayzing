<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;


class ProductAdminController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'pickup_stock' => 'required|integer|min:0',
            'delivery_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:7168', // <--- nieuw
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Maak image + verklein (max 2000px) + comprimeer
            $img = Image::read($file)
                ->scaleDown(width: 2000, height: 2000);

            // Altijd als jpg wegschrijven (scheelt veel MB's)
            $encoded = $img->toJpeg(quality: 80);

            $path = 'products/' . uniqid('prod_') . '.jpg';
            Storage::disk('public')->put($path, (string) $encoded);

            $validated['image'] = $path;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product toegevoegd.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'pickup_stock' => 'required|integer|min:0',
            'delivery_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:7168', // <--- nieuw
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Maak image + verklein (max 2000px) + comprimeer
            $img = Image::read($file)
                ->scaleDown(width: 800, height: 800);

            // Altijd als jpg wegschrijven (scheelt veel MB's)
            $encoded = $img->toJpeg(quality: 85);

            $path = 'products/' . uniqid('prod_') . '.jpg';
            Storage::disk('public')->put($path, (string) $encoded);

            $validated['image'] = $path;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product bijgewerkt.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product verwijderd.');
    }
}
