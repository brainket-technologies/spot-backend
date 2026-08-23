<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Campaign;
use App\CentralLogics\BannerLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    // Old Code this 
    public function get_banners(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json(['errors' => $errors], 403);
        }

        $longitude = $request->header('longitude')??0;
        $latitude = $request->header('latitude')??0;
        $zone_id = json_decode($request->header('zoneId'), true);

        $bannersCacheKey = 'banners_' . md5(json_encode($zone_id));
        $campaignsCacheKey = 'campaigns_' . md5(json_encode([$zone_id, $longitude, $latitude]));

        $banners = Cache::remember($bannersCacheKey, now()->addMinutes(20), function () use ($zone_id) {
            return BannerLogic::get_banners($zone_id);
        });

        $campaigns = Cache::remember($campaignsCacheKey, now()->addMinutes(20), function () use ($zone_id, $longitude, $latitude) {
            return Campaign::whereHas('restaurants', function ($query) use ($zone_id) {
                $query->whereIn('zone_id', $zone_id)->Active()->where('campaign_status', 'confirmed');
            })->with('restaurants', function ($query) use ($zone_id, $longitude, $latitude) {
                return $query->WithOpen($longitude, $latitude)
                    ->whereIn('zone_id', $zone_id)
                    ->where('campaign_status', 'confirmed')
                    ->where('status', 1);
            })
                ->running()
                ->active()
                ->get();
        });

        try {
            return response()->json([
                'campaigns' => Helpers::basic_campaign_data_formatting($campaigns, true),
                'banners' => $banners
            ], 200);
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->json([], 200);
        }
    }
    
    
    
    
    // appHeaderBannerUri 
    public function appHeaderBannerUri(Request $request)
    {
        try {
           return response()->json([
                'res' => 'success',
                'msg' => 'found',
                'data' => [
                    'image' => 'https://smithbrothersmedia.com.au/wp-content/uploads/2020/07/BeFitFood_Banner_4.gif',
                    // 'image' => 'https://cdn.pixabay.com/photo/2018/08/04/11/30/draw-3583548_1280.png',
                    // 'image' => '',
                    'status' => 'true'
                ]
            ], 200);
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->json([], 200);
        }
    }
    
    
    
    
    
    // this is new code 
    public function get_banners_new(Request $request)
    {
        // Check if zoneId is provided in query parameters or headers
        $zone_id = $request->query('zoneId') ?? $request->header('zoneId');
        
        if (!$zone_id) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => translate('messages.zone_id_required')]);
            return response()->json(['errors' => $errors], 403);
        }
        
        // Get longitude and latitude from query parameters or headers
        $longitude = $request->query('longitude') ?? $request->header('longitude') ?? 0;
        $latitude = $request->query('latitude') ?? $request->header('latitude') ?? 0;
        
        // Try to decode zoneId if it's a JSON string, otherwise use as is
        try {
            $zone_id = is_string($zone_id) && is_array(json_decode($zone_id, true)) 
                ? json_decode($zone_id, true) 
                : [$zone_id];
        } catch (\Exception $e) {
            $zone_id = [$zone_id];
        }
        
        $bannersCacheKey = 'banners_' . md5(json_encode($zone_id));
        $campaignsCacheKey = 'campaigns_' . md5(json_encode([$zone_id, $longitude, $latitude]));
        
        $banners = Cache::remember($bannersCacheKey, now()->addMinutes(20), function () use ($zone_id) {
            return BannerLogic::get_banners($zone_id);
        });
        
        $campaigns = Cache::remember($campaignsCacheKey, now()->addMinutes(20), function () use ($zone_id, $longitude, $latitude) {
            return Campaign::whereHas('restaurants', function ($query) use ($zone_id) {
                $query->whereIn('zone_id', $zone_id)
                      ->Active()
                      ->where('campaign_status', 'confirmed');
            })->with('restaurants', function ($query) use ($zone_id, $longitude, $latitude) {
                return $query->WithOpen($longitude, $latitude)
                    ->whereIn('zone_id', $zone_id)
                    ->where('campaign_status', 'confirmed')
                    ->where('status', 1);
            })
            ->running()
            ->active()
            ->get();
        });
        
        try {
            return response()->json([
                'campaigns' => Helpers::basic_campaign_data_formatting($campaigns, true),
                'banners' => $banners
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Banner API error: ' . $e->getMessage());
            return response()->json([], 200);
        }
}
    
    
    // end here 
}
