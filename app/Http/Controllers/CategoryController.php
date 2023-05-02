<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per_page parameter must be an integer between 1 and 100.');
        }

        $categories = Category::filter(request(['search']))
          ->sortable()
          ->paginate($row)
          ->appends(request()->query());

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:categories,name',
            'slug' => 'required|unique:categories,slug|alpha_dash',
        ];

        $validatedData = $request->validate($rules);

        Category::create($validatedData);

        return Redirect::route('categories.index')->with('success', 'Category has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
      abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', [
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $rules = [
            'name' => 'required|unique:categories,name,'.$category->id,
            'slug' => 'required|alpha_dash|unique:categories,slug,'.$category->id,
        ];

        $validatedData = $request->validate($rules);

        Category::where('slug', $category->slug)->update($validatedData);

        return Redirect::route('categories.index')->with('success', 'Category has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        Category::destroy($category->id);

        return Redirect::route('categories.index')->with('success', 'Category has been deleted!');
    }
}
