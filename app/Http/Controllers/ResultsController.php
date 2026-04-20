<?php

namespace App\Http\Controllers;

use App\Actions\GenerateResponseResults;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function index()
    {
        $action = new GenerateResponseResults();
        $results = $action->handle();
        return response()->json($results);
    }
}
