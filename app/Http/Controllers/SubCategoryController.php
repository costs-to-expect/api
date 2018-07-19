<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubCategoryController extends Controller
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
    public function show(Request $request, int $category_id, int $sub_category_id)
    {
        return response()->json(['result' => ['id'=>$category_id, 'id2'=>$sub_category_id]], 200);
    }
}
