<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplainCategory;
use Illuminate\Http\Request;

class ComplainCategoryApiController extends Controller
{
    /**
     * Fetch all Complain Categories
     */
    public function index()
    {
        $categories = ComplainCategory::select('id', 'title', 'discription')->get();

        return response()->json([
            'status' => true,
            'message' => 'Complain categories fetched successfully.',
            'data' => $categories
        ]);
    }
}
