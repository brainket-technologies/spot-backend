<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get list of categories with restaurant product count
     */
    public function get_categories(Request $request)
    {
        try {
            $restaurant_id = $request['vendor']->restaurants[0]->id;
            $search = $request->query('search');

            $categories = Category::withCount(['childes'])
                ->where(['position' => 0, 'status' => 1])
                ->when($search, function ($q) use ($search) {
                    $keys = explode(' ', $search);
                    $q->where(function ($sub) use ($keys) {
                        foreach ($keys as $key) {
                            $sub->orWhere('name', 'like', "%{$key}%");
                        }
                    });
                })
                ->orderBy('priority', 'desc')
                ->get();

            foreach ($categories as $category) {
                $category['products_count'] = (int) Food::where('restaurant_id', $restaurant_id)
                    ->whereHas('category', function ($q) use ($category) {
                        $q->whereId($category->id)->orWhere('parent_id', $category->id);
                    })
                    ->count();
            }

            return response()->json(Helpers::category_data_formatting($categories, true), 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    ['code' => 'category_error', 'message' => $e->getMessage()]
                ]
            ], 500);
        }
    }

    /**
     * Get subcategories for a given parent category
     */
    public function get_childes($parent_id, Request $request)
    {
        try {
            $restaurant_id = $request['vendor']->restaurants[0]->id;

            $categories = Category::where(['parent_id' => $parent_id, 'status' => 1])
                ->orderBy('priority', 'desc')
                ->get();

            foreach ($categories as $category) {
                $category['products_count'] = (int) Food::where('restaurant_id', $restaurant_id)
                    ->where('category_id', $category->id)
                    ->count();
            }

            return response()->json(Helpers::category_data_formatting($categories, true), 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    ['code' => 'category_error', 'message' => $e->getMessage()]
                ]
            ], 500);
        }
    }

    /**
     * Get category-wise products for the vendor restaurant
     */
    public function get_category_wise_products(Request $request)
    {
        try {
            $restaurant_id = $request['vendor']->restaurants[0]->id;
            $limit = (int) ($request->query('limit', 10));
            $offset = (int) ($request->query('offset', 1));
            $category_id = $request->query('category_id');
            $is_sub_category = (int) ($request->query('sub_category', 0));

            $query = Food::where('restaurant_id', $restaurant_id);

            if ($category_id) {
                if ($is_sub_category == 1) {
                    $query->where('category_id', $category_id);
                } else {
                    $query->whereHas('category', function ($q) use ($category_id) {
                        $q->whereId($category_id)->orWhere('parent_id', $category_id);
                    });
                }
            }

            $paginator = $query->latest()->paginate($limit, ['*'], 'page', $offset);

            $data = [
                'total_size' => $paginator->total(),
                'limit' => (string) $limit,
                'offset' => (string) $offset,
                'products' => Helpers::product_data_formatting(
                    data: $paginator->items(),
                    multi_data: true,
                    trans: true,
                    local: app()->getLocale()
                ),
            ];

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    ['code' => 'category_products_error', 'message' => $e->getMessage()]
                ]
            ], 500);
        }
    }

    /**
     * Get addon categories list for vendor
     */
    public function get_addon_categories(Request $request)
    {
        try {
            $categories = Category::where(['position' => 0, 'status' => 1])
                ->with('translations')
                ->orderBy('name')
                ->get();

            $data = [];
            foreach ($categories as $category) {
                $data[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'translations' => $category->translations,
                ];
            }

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    /**
     * Get tax / VAT list
     */
    public function get_taxvat_list(Request $request)
    {
        return response()->json([], 200);
    }
}
