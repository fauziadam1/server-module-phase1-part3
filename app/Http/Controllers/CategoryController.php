<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function all()
    {
        $category = Category::all();

        return response()->json([
            "status" => "success",
            "message" => "Get all categories successful",
            'data' => [
                'category' => $category->map(function ($category) {
                    return [
                        "id" => $category->id,
                        "name" => $category->name,
                        "icon" => $category->icon,
                        "type" => $category->type,
                        "created_at" => $category->created_at,
                        "updated_at" => $category->updated_at
                    ];
                })
            ]
        ], 200);
    }
}
