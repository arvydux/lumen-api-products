<?php

namespace App\Http\Controllers;

use App\Enum\VatRates;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return $this->successResponse('All products listing', $products, 200);
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'name'          => 'required',
                'description'   => 'required|string|max:140',
                'sku'           => 'required|alpha_num|max:13',
                'cost_price'    => 'required|numeric',
                'selling_price' => 'required|numeric',
                'vat'           => 'boolean',
                'vat_rate'      => [Rule::enum(VatRates::class), 'required_with:vat', 'exclude_unless:vat,1']
            ]);

            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->sku = $request->sku;
            $product->cost_price = $request->cost_price;
            $product->selling_price = $request->selling_price;
            $product->vat = $request->vat;
            $product->vat_rate = $request->vat_rate;

            $product->save();

            return $this->successResponse('Product has been added', $product, 201);
        } catch (ValidationException $th) {
            return $this->errorResponse('The given data was invalid.', $th->errors());
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            return $this->successResponse('Single product', $product, 200);
        } catch (ModelNotFoundException $th) {
            return $this->errorResponse('Product not found.', $th->errors());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'name'          => 'required',
                'description'   => 'required|string|max:140',
                'sku'           => 'required|alpha_num|max:13',
                'cost_price'    => 'required|numeric',
                'selling_price' => 'required|numeric',
                'vat'           => 'boolean',
                'vat_rate'      => [Rule::enum(VatRates::class), 'required_with:vat', 'exclude_unless:vat,1']
            ]);

            $product = Product::findOrFail($id);
            $product->name = $request->name;
            $product->description = $request->description;
            $product->sku = $request->sku;
            $product->cost_price = $request->cost_price;
            $product->selling_price = $request->selling_price;
            $product->vat = $request->vat;
            $product->vat_rate = $request->vat_rate;
            $product->save();

            return $this->successResponse('Product has been updated', $product, 200);
        } catch (ValidationException $th) {
            return $this->errorResponse('The given data was invalid.', $th->errors());
        } catch (ModelNotFoundException $th) {
            return $this->errorResponse('Product not found.', $th->errors());
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return $this->successResponse('Product has been removed.', [], '204');
        } catch (ModelNotFoundException $th) {
            return $this->errorResponse('Product not found.', $th->getMessage());
        }
    }
}
