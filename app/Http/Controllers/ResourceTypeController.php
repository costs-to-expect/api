<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResourceTypeController extends Controller
{
    /*
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['results' => [['id'=>1], ['id'=>2], ['id'=>3]]], 200);
    }

    /*
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $resource_type_id)
    {
        return response()->json(['result' => ['id'=>$resource_type_id]], 200);
    }
}
