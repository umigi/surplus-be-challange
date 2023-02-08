<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get All enabled Categories
        $categories = Category::where('enable', true)->get();

        return response()->json($categories);
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
            'name' => 'required',
            'enable' => 'required|boolean',
        ]);

        if ($validation->fails()) return response()->json($validation->errors());

        // Insert new Category
        $category = Category::create($request->all());

        return response()->json($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get single Category
        $category = Category::find($id);
        if (!$category) return response()->json(["Message" => "Data not Found"]);

        return response()->json($category);
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
            'enable' => 'required|boolean',
        ]);

        if ($validation->fails()) return response()->json($validation->errors());

        // Get single Category
        $category = Category::find($id);
        if (!$category) return response()->json(["Message" => "Data not Found"]);

        // Update Category
        $category->name = $request->name;
        $category->enable = $request->enable;
        $category->save();

        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Get single Category
        $category = Category::find($id);
        if (!$category) return response()->json(["Message" => "Data not Found"]);

        // Delete Category
        $category->delete();

        return response()->json(["Message" => "Hapus Sukses"]);
    }
}
