<?php

namespace Modules\RequestService\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\RequestService\Models\RequestService;
use Modules\RequestService\Transformers\RequestServiceResource;
class RequestServicesController extends Controller
{
    public function getRequestList(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $request_list = RequestService::query();
        $is_status = $request->input('is_status');
        if ($is_status === 'accept') {
            $request_list->where('is_status', 'accept');
        } elseif ($is_status === 'reject') {
            $request_list->where('is_status', 'reject');
        } elseif ($is_status === 'pending') {
            $request_list->where('is_status', 'pending');
        }
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $request_list->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%");
            });
        }
        $request_list = $request_list->where('status', 1);
        
        $request_service = $request_list->orderBy('updated_at', 'desc');
        $request_service = $request_service->paginate($perPage);

        $responseData = RequestServiceResource::collection($request_service);

        return response()->json([
            'status' => true,
            'data' => $responseData,
            'message' => __('messages.request_service_list'),
        ], 200);
    }
}
