<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get All enabled Product
        $products = Product::where('enable', true)
            ->whereHas('categories', function ($q) {
                $q->where('enable', true);
            })
            ->with('categories')->get();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate Request
        $validation = Validator::make($request->all(), [
            'category_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'enable' => 'required|boolean',
        ]);

        if ($validation->fails()) return response()->json($validation->errors());

        DB::beginTransaction();
        try {
            // Insert new Product
            $product = Product::create($request->all());

            // Insert many-to-many Table
            DB::table('category_product')->insert([
                'category_id' => $request->category_id,
                'product_id' => $product->id,
            ]);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            return response()->json(["Message" => "Insert Error - " . $e->getMessage()]);
        }

        // Get related Category
        $product->load('categories');

        return response()->json($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get single Product
        $product = Product::where('id', $id)
            ->whereHas('categories', function ($q) {
                $q->where('enable', true);
            })
            ->with('categories')->first();

        if (!$product) return response()->json(["Message" => "Data not Found"]);

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate Request
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'enable' => 'required|boolean',
        ]);

        if ($validation->fails()) return response()->json($validation->errors());

        // Get single Product
        $product = Product::find($id);
        if (!$product) return response()->json(["Message" => "Data not Found"]);

        DB::beginTransaction();
        try {
            // Update Product
            $product->name = $request->name;
            $product->description = $request->description;
            $product->enable = $request->enable;
            $product->save();

            // Update many-to-many Table
            DB::table('category_product')
                ->where('product_id', $product->id)
                ->update([
                    'category_id' => $request->category_id,
                ]);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            return response()->json(["Message" => "Update Error - " . $e->getMessage()]);
        }

        // Get related Category
        $product->load('categories');

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Get single Product
        $product = Product::find($id);
        if (!$product) return response()->json(["Message" => "Data not Found"]);

        // Delete Category
        $product->delete();

        return response()->json(["Message" => "Hapus Sukses"]);
    }
}
