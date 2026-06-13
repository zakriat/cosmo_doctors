<?php

namespace Modules\Product\Http\Controllers\Backend\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Unit;
use Modules\Product\Transformers\UnitResource;

class UnitsController extends Controller
{
    public function product_unit(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Get the number of items per page from the request (default: 10)
        $serviceProviderId = $request->input('service_provider_id');
        $unit = Unit::where('status', 1);
        // ->whereHas('service_providers', function ($query) use ($serviceProviderId) {
            //     $query->where('service_provider_id', $serviceProviderId);
        // });

        $unit = $unit->paginate($perPage);
        // $unit = $unit->paginate($perPage)->appends('service_provider_id', $serviceProviderId);
        $unitCollection = unitResource::collection($unit);

        if ($request->has('unit_id') && $request->unit_id != '') {
            $unit = $unit->where('id', $request->unit_id)->first();

            $unitCollection = new unitResource($unit);
        }

        return response()->json([
            'status' => true,
            'data' => $unitCollection,
            'message' => __('product.unit_list'),
        ], 200);
    }
}
