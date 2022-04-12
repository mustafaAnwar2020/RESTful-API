<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ResponseJson;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\productResource;

class productsController extends Controller
{
    use ResponseJson;
    public function index()
    {
        $products = Product::all();
        return $this->sendResponse(productResource::collection($products),'All products!');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input,[
            'name'=>'required|string|max:255',
            'description'=>'required|string|max:1000',
            'price'=>'required|numeric'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation error' , $validator->errors());
        }

        $product = Product::create($input);

        return $this->sendResponse(new productResource($product),'Product created successfully!');
    }


    public function show(Product $product)
    {
        return $this->sendResponse(new productResource($product),'your product is here');
    }


    public function update(Request $request, Product $product)
    {

        $input = $request->all();
        $validator = Validator::make($input,[
            'name'=>'required|string|max:255',
            'description'=>'required|string|max:1000',
            'price'=>'required|numeric'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation error' , $validator->errors());
        }

        $product->update($input);

        return $this->sendResponse(new productResource($product),'Product updated successfully!');
    }

    public function search($name){
        $resultProduct = Product::where('name','like','%'.$name.'%')->get();
        return $this->sendResponse(productResource::collection($resultProduct),'search result is here!');
    }
    public function destroy(Product $product)
    {
        $product->delete($product->id);
        $data = ['message'=>'Product deleted successfully'];
        return response()->json($data,200);
    }
}
