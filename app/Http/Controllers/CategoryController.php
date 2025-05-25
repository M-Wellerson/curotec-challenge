<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CategoryStoreRequest;

class CategoryController extends Controller
{
    public function store(CategoryStoreRequest $request)
    {
        $request->user()->categories()->create($request->validated());
        return back();
    }
}
