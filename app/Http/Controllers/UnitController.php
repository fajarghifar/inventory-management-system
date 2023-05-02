<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $units = Unit::filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query());

        return view('units.index', [
            'units' => $units,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:units,name',
            'slug' => 'required|unique:units,slug|alpha_dash',
        ];

        $validatedData = $request->validate($rules);

        Unit::create($validatedData);

        return Redirect::route('units.index')->with('success', 'Unit has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
      abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('units.edit', [
            'unit' => $unit
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $rules = [
            'name' => 'required|unique:units,name,'.$unit->id,
            'slug' => 'required|alpha_dash|unique:units,slug,'.$unit->id,
        ];

        $validatedData = $request->validate($rules);

        Unit::where('slug', $unit->slug)->update($validatedData);

        return Redirect::route('units.index')->with('success', 'Unit has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        Unit::destroy($unit->id);

        return Redirect::route('units.index')->with('success', 'Unit has been deleted!');
    }
}
