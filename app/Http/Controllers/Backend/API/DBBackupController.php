<?php

namespace App\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class DBBackupController extends Controller
{
    public function index(Request $request)
    {
        
        $data = Storage::allFiles(env('APP_NAME', 'KiviCare Laravel'));       
        return response()->json(['data' => $data, 'status' => true]);        
    }

    public function downloadBkFiles(Request $request)
    {     
        if(isset($request->file_name) && !empty($request->file_name))
        { 
            $file=  storage_path('app/').$request->file_name;
            return Response::download($file);
        }else{
            return response()->json(['status' => false, 'message' => 'Something went wrong..!']); 
        }
    }
}
