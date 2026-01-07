<?php

namespace App\Http\Controllers;

use App\Services\UnitService;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * @var UnitService
     */
    protected UnitService $unitService;

    /**
     * UnitController constructor.
     *
     * @param UnitService $unitService
     */
    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('units.index');
    }
}
