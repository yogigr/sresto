<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Table;
use Auth;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Table::class);

        $tables = Table::all();
        return response()->json([
            'tables' => $tables
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Table::class);

        $request->validate([
            'table_name' => 'required|string|max:25|unique:tables,name',
        ]);

        $table = Table::create([
            'name' => $request->table_name,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'message' => __('Table Created'),
            'table' => $table
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function show(Table $table)
    {
        $this->authorize('view', $table);

        return response()->json([
            'table' => $table
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Table $table)
    {
        $this->authorize('update', $table);

        $request->validate([
            'table_name' => 'required|string|max:25|unique:tables,name,' . $table->id,
        ]);

        $table->name = $request->table_name;
        $table->save();
        
        return response()->json([
            'message' => __('Table Updated'),
            'table' => $table
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function destroy(Table $table)
    {
        $this->authorize('delete', $table);
        
        $table->delete();
        return response()->json([
            'message' => __('Table Deleted')
        ], 200);
    }
}
