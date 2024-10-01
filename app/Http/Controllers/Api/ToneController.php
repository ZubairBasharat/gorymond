<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ToneResource;
use App\Models\Tone;
use App\Http\Controllers\Controller;


class ToneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ToneResource::collection(Tone::get());
    }
}