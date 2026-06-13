<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Clinic\Models\ClinicsCategory;
use Yajra\DataTables\DataTables;
class CategoryController extends Controller
{
    public function categoriesList()
    {
        return view('frontend::categories');
    }

    public function index_data(Request $request)
    { 
        $search = $request->input('search');
        $category_list = ClinicsCategory::query();

        if ($search) {
            $categories = $category_list->where('name', 'like', '%' . $search . '%');
        }

        $category_list =$category_list->where('status',1);

        $categories = $category_list->orderBy('updated_at', 'desc');

        return DataTables::of($categories)
            ->addColumn('card', function ($category) {
                return view('frontend::components.card.category_card', compact('category'))->render();
            })
            ->rawColumns(['card'])  
            ->make(true);
        
    }

    
}
