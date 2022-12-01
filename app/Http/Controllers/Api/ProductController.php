<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(5);

        return new ProductResource(true, 'List Data Produk', $products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png',
            'name' => 'required|unique:products',
            'description' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file("image");
        $image->storeAs('public/products', $image->hashName());

        $product = Product::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'description' => $request->description,
            'qty' => $request->qty,
            'price' => $request->price,
            'rating' => $request->rating,
        ]);

        if ($product) {
            return new ProductResource(true, "Data produk berhasil disimpan 🤙", $product);
        }

        return new ProductResource(false, "Data produk gagal disimpan 😥", null);
    }

    public function show($id)
    {
        $product = Product::whereId($id)->first();

        if ($product) {
            return new ProductResource(true, 'Detail data produk 🤙', $product);
        }

        return new ProductResource(false, 'Detail data produk tidak ditemukan 😥', null);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            Storage::disk('local')->delete('public/products/' . basename($product->image));
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            $product->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'description' => $request->description,
                'qty' => $request->qty,
                'price' => $request->price,
                'rating' => $request->rating,
            ]);
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'qty' => $request->qty,
            'price' => $request->price,
            'rating' => $request->rating,
        ]);

        if ($product) {
            return new ProductResource(true, 'Data produk berhasil diupdate 🤙', $product);
        }

        return new ProductResource(false, 'Data produk gagal diupdate 😥', null);
    }

    public function destroy(Product $product)
    {
        Storage::disk('local')->delete('public/products/' . basename($product->image));
        if ($product->delete()) {
            return new ProductResource(true, 'Data produk berhasil dihapus 🤙', null);
        }

        return new ProductResource(false, 'Data produk gagal dihapus 😥', null);
    }
}