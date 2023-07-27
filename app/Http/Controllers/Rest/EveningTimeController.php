<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\EveningTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EveningTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(EveningTime::query()->orderBy('order')->get());
    }
}
