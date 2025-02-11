<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Product;
use Crypt;

class ProductsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('product_access'), 403);

        $products = Product::all();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('product_create'), 403);

        return view('admin.products.create');
    }

    public function store(StoreProductRequest $request)
    {
        abort_unless(\Gate::allows('product_create'), 403);

        try {
           Product::create($request->all());
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')->with('error', 'Failed to create the product.');
        }

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product)
    {
        abort_unless(\Gate::allows('product_edit'), 403);

        try {
            return view('admin.products.edit', compact('product'));
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')->with('error', 'Failed to load the product for editing.');
        }
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        abort_unless(\Gate::allows('product_edit'), 403);

        try {
            $product->update($request->all());
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')->with('error', 'Failed to update the product.');
        }

        return redirect()->route('admin.products.index');
    }

    public function show(Product $product)
    {
        abort_unless(\Gate::allows('product_show'), 403);

        return view('admin.products.show', compact('product'));
    }

    public function customShow($product_id)
    {
        abort_unless(\Gate::allows('product_show'), 403);

        $decrypted_id = Crypt::decrypt($product_id);
        $product = Product::find($decrypted_id);
        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        abort_unless(\Gate::allows('product_delete'), 403);

        try {
            $product->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete the product.');
        }

        return back();
    }

    public function massDestroy(MassDestroyProductRequest $request)
    {
        try {
            Product::whereIn('id', request('ids'))->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete the selected products.'], 500);
        }

        return response(null, 204);
    }
}
