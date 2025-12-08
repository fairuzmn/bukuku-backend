<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Utils\ResponseUtils;
use App\Http\Requests\Table\StoreTableRequest;
use App\Http\Requests\Table\UpdateTableRequest;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $table = Table::all();
        return ResponseUtils::baseResponse(200, 'Tables retrieved', [
            'tables' => $table,
        ]);
    }

    public function store(StoreTableRequest $request)
    {
        $data = $request->validated();

        $table = Table::create($data);
        return ResponseUtils::baseResponse(200, 'Table created', $table);
    }

    public function show(Table $table)
    {
        return ResponseUtils::baseResponse(200, 'Table details', $table);
    }

    public function update(UpdateTableRequest $request, Table $table)
    {
        $table->update($request->validated());
        return ResponseUtils::baseResponse(200, 'Table updated', $table);
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return ResponseUtils::baseResponse(200, 'Table deleted');
    }
}
