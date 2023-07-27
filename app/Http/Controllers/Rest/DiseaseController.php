<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiseaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return response()->json(Disease::query()->orderBy('order')->get());
        // $diseases = Disease::query()->orderBy('order')->get(); // 22.9.12無効化
        $diseases = Disease::list();
        return response()->json($diseases);
    }
}
