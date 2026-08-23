<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Zone;
use App\Models\Incentive;
use App\Exports\ZoneExport;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Validator;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use Illuminate\Support\Facades\DB;

class NewController extends Controller
{
    public function index(Request $request)
    {
        $key = explode(' ', $request['search'] ?? null );
        $zones = Zone::withCount(['restaurants','deliverymen'])
        ->when(isset($key), function($query)use($key){
            $query->where( function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->paginate(config('default_pagination'));
        return view('admin-views.zone.index', compact('zones'));
    }

//   public function store(Request $request)
//   {
       
//     try {

//         // Insert the name using Query Builder
//         $zoneId = DB::table('test')->insertGetId([
//             'name' => $request->name[0],
//         ]);

//         return response()->json(['message' => 'Zone added successfully!'], 200);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }
public function store(Request $request)
{
    try {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:test|max:191',
            'display_name' => 'nullable|unique:test|max:255',
            'coordinates' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        // Convert coordinate string into a valid polygon
        $value = $request->coordinates;
        $polygon = [];
        foreach (explode('),(', trim($value, '()')) as $index => $single_array) {
            if ($index == 0) {
                $lastcord = explode(',', $single_array);
            }
            $coords = explode(',', $single_array);
            $polygon[] = "{$coords[0]} {$coords[1]}";
        }
        $polygon[] = "{$lastcord[0]} {$lastcord[1]}"; // Close the polygon

        // Prepare the POLYGON WKT string
        $polygonWKT = "POLYGON((" . implode(',', $polygon) . "))";

        // Generate unique zone ID
        $zoneId = DB::table('test')->count() + 1;

        // Insert data using Query Builder
        $zoneId = DB::table('test')->insertGetId([
            'name' => $request->name[0],
            'display_name' => $request->name[1],
            'coordinates' => DB::raw("ST_GeomFromText('$polygonWKT')"),
            'status' => 1,
            'restaurant_wise_topic' => 'zone_' . $zoneId . '_restaurant',
            'customer_wise_topic' => 'zone_' . $zoneId . '_customer',
            'deliveryman_wise_topic' => 'zone_' . $zoneId . '_delivery_man',
            'per_km_shipping_charge' => $request->per_km_delivery_charge ?? 0,
            'minimum_shipping_charge' => $request->minimum_delivery_charge ?? 0,
            'maximum_shipping_charge' => $request->maximum_shipping_charge ?? null,
            'max_cod_order_amount' => $request->max_cod_order_amount ?? null,
            'increased_delivery_fee' => $request->increased_delivery_fee ?? 0,
            'increased_delivery_fee_status' => $request->increased_delivery_fee_status ?? 0,
            'increase_delivery_charge_message' => $request->increase_delivery_charge_message ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update translations
        Helpers::add_or_update_translations($request, 'name', 'name', 'Test', $zoneId, $request->name);
        Helpers::add_or_update_translations($request, 'display_name', 'display_name', 'Test', $zoneId, $request->display_name);

        // Retrieve updated zones
        $new_data = 1;
        $zones = DB::table('test')
            ->leftJoin('restaurants', 'test.id', '=', 'restaurants.zone_id')
            ->leftJoin('deliverymen', 'test.id', '=', 'deliverymen.zone_id')
            ->select('test.*', 
                DB::raw('COUNT(DISTINCT restaurants.id) as restaurants_count'),
                DB::raw('COUNT(DISTINCT deliverymen.id) as deliverymen_count'))
            ->groupBy('test.id')
            ->latest('test.created_at')
            ->paginate(config('default_pagination'));

        return response()->json([
            'view' => view('admin-views.zone.partials._table', compact('zones', 'new_data'))->render(),
            'total' => $zones->total()
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    
}
