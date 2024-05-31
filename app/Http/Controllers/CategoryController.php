<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function getAll(Request $request) {
        $categories = Category::all();
        return response()->json(['status' => true, 'data' => $categories]);

    }

    public function createNew(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->save();
            return response()->json(['status' =>true, 'msg'=> 'category created successfully'], 200);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 500);
        }
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validator->passes()) {
            $category = Category::findOrFail($id);
            $category->update([
                'name' => $request->name,
            ]);
            return response()->json($category);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 500);
        }
    }

    public function remove(Request $request, $id) {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
