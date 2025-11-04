<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\Project;


class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['users','milestones','publications','tags','attachments','comments','tasks'])
            ->orderBy('title')
            ->get();

        return response()->json($projects, 200);
    }

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
