<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ScheduleController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:schedules.view')
            ->only(['index', 'show']);

        $this->middleware('permission:schedules.create')
            ->only(['create', 'store']);

        $this->middleware('permission:schedules.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:schedules.delete')
            ->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}