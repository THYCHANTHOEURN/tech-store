<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Data\CategoryDataResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    /**
     * Get all categories.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(Request $request)
    {
        try {
            $categories = Category::where('status', true)
                ->whereNull('parent_id')
                ->with(['children' => function($query) {
                    $query->where('status', true);
                }])
                ->get();
            $categories = CategoryDataResource::collection($categories);

            return response()->json([
                'success'   => true,
                'data'      => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}
