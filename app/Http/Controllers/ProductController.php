<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Auth\Events\Validated;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::get();
        return Inertia::render('Product/Index', [
            'data' => $products
        ]);
    }

    public function store(Request $request) 
    {
        // dd($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'title' => 'required',
            'category_ref_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = new Product();
        $product->name = $request->input('name');
        $product->title = $request->input('title');
        $product->description = $request->input('description');
        $product->category_ref_id = $request->input('category_ref_id');
        $product->brand_ref_id = $request->input('brand_ref_id');
        $product->price = $request->input('price');
        $product->discount_price = $request->input('discount_price');
        $product->benefits_content = $request->input('benefits_content');
        $product->ingredients_content = $request->input('ingredients_content');
        $product->howtouse_content = $request->input('howtouse_content');
        $product->product_size_id = $request->input('product_size_id');
        $product->is_active = $request->input('is_active');

        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $filename = "XaYPfty10". $product->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/product_images', $filename);
            $product->product_image = $filename;
        }

        // dd($filename, $path, $product->product_image);

        $product->save();

        return redirect()->back()->with('success', 'Product created successfully');
    }

    public function edit(Request $request, $product) 
    {
        $data = Product::find($product);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request, $product) 
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'title' => 'required',
            'category_ref_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = Product::find($product);
        $product->name = $request->input('name');
        $product->title = $request->input('title');
        $product->description = $request->input('description');
        $product->category_ref_id = $request->input('category_ref_id');
        $product->brand_ref_id = $request->input('brand_ref_id');
        $product->price = $request->input('price');
        $product->discount_price = $request->input('discount_price');
        $product->benefits_content = $request->input('benefits_content');
        $product->ingredients_content = $request->input('ingredients_content');
        $product->howtouse_content = $request->input('howtouse_content');
        $product->product_size_id = $request->input('product_size_id');
        $product->is_active = $request->input('is_active');

        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $filename = "XaYPfty10". $product->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/product_images', $filename);
            $product->product_image = $filename;
        }

        $product->save();

        return redirect()->back()->with('success', 'Product created successfully');
    }

    public function destroy(Request $request, $product) 
    {
        $data = Product::find($product);

        if($data) {
            $data->delete();
        }

        return redirect()->back()->with('success', 'Product deleted successfully');
    }
}
