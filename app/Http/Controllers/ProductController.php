<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            ->with(['categories', 'images'])
            ->get();

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
        $product->load(['categories', 'images']);

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
            ->with(['categories', 'images'])->first();

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
        $product->load(['categories', 'images']);

        return response()->json($product);
    }

    /**
     * Upload Image for specified Product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, $id)
    {
        // Validate Request
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'file' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',
            'enable' => 'required|boolean',
        ]);

        if ($validation->fails()) return response()->json($validation->errors());

        // Get single Category
        $product = Product::find($id);
        if (!$product) return response()->json(["Message" => "Data not Found"]);

        DB::beginTransaction();
        try {

            // Randomize filename and Upload file
            $file = $request->file('file');
            $path = public_path('product_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move($path, $filename);

            // Insert new Image
            $image = Image::create([
                'name' => $request->name,
                'file' => $filename,
                'enable' => $request->enable,
            ]);

            // Insert many-to-many Table
            DB::table('product_image')->insert([
                'image_id' => $image->id,
                'product_id' => $product->id,
            ]);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            return response()->json(["Message" => "Insert Error - " . $e->getMessage()]);
        }

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
