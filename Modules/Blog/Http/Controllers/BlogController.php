<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Blog\Models\Blog;
use Mews\Purifier\Facades\Purifier;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Blog\Http\Requests\BlogRequest;
use Yajra\DataTables\DataTables;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        // Page Title
        $this->module_title = 'messages.blogs';
        // module name
        $this->module_name = 'blog';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $module_name = 'blog'; 
        // $auth_user = authSession(); 
        $pageTitle = trans('messages.blogs');
        $assets = ['datatable'];
        return view('blog::blog.index', compact('pageTitle','module_name', 'assets', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $isMultiVendor = Setting::where('name', 'is_multi_vendor')->value('val'); 

        if ($isMultiVendor == 1) {
            $vendors = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin','vendor']); 
            })->select('id', 'first_name', 'last_name')->get();
        } else {
            $vendors = User::whereHas('roles', function($query) {
                 $query->where('name', 'admin'); 
            })->select('id', 'first_name', 'last_name')->get();
        }
        
        $id = $request->id;
        $blogdata = Blog::find($id);
    
        $pageTitle = trans('messages.update_form_title', ['form' => trans('messages.blog')]);
    
        if ($blogdata == null) {
            $pageTitle = trans('messages.add_button_form', ['form' => trans('messages.blog')]);
            $blogdata = new Blog;
        }
    
        return view('blog::blog.create', compact('pageTitle', 'blogdata', 'vendors'));
    }

    /**
     * Fetch data for DataTables.
     */
    public function index_data(DataTables $datatable, Request $request)
    {
        try {
            $module_name = 'blog';
            // Initialize the query for fetching blog data
            $query = Blog::query();

            // Apply filters if necessary
            if ($request->has('filter') && isset($request->filter['column_status'])) {
                $query->where('status', $request->filter['column_status']);
            }
            if (auth()->user()->hasAnyRole(['admin'])) {
                $query->withTrashed();
            }


            return $datatable->eloquent($query)
                ->addColumn('check', function ($query) {
                    $isDeleted = $query->deleted_at ? 1 : 0;
                    return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-'.$query->id.'" name="datatable_ids[]" value="'.$query->id.'" data-type="blog" data-deleted="'.$isDeleted.'" onclick="dataTableRowCheck('.$query->id.',this)">';
                })
                ->editColumn('title', function ($query) {
                    return auth()->user()->can('blog edit') 
                        ? '<a class="btn-link btn-link-hover" href='.route('backend.blog.create', ['id' => $query->id]).'>'.$query->title.'</a>' 
                        : $query->title;
                })
                ->filterColumn('title', function ($query, $keyword) {
                    $query->where('title', 'like', '%'.$keyword.'%');
                })
                ->editColumn('author_id', function ($query) {
                    $authorName = $query->author ? $query->author->first_name . ' ' . $query->author->last_name : '';
                    return $authorName;
                })
                ->filterColumn('author_id', function ($query, $keyword) {
                    $query->whereHas('author', function ($q) use ($keyword) {
                        $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$keyword%"]);
                    });
                })
                ->orderColumn('author_id', function ($query, $order) {
                    $query->select('blogs.*')
                        ->join('users as providers', 'providers.id', '=', 'blogs.author_id')
                        ->orderByRaw("CONCAT(providers.first_name, ' ', providers.last_name) $order");
                })

                ->editColumn('status', function ($row) {
                    $checked = $row->status ? 'checked="checked"' : '';
                    $disabled = $row->deleted_at ? 'disabled' : '';
                    $title = $row->deleted_at ? 'title="'.__('messages.item_is_deleted').'"' : '';

                    return '
                        <div class="form-check form-switch ">
                            <input type="checkbox" data-url="'.route('backend.blog.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'"  name="status" value="'.$row->id.'" '.$checked.' '.$disabled.' '.$title.'>
                        </div>
                    ';
                })
                
                ->addColumn('action', function ($blog) use ($module_name) {
                    return view('blog::blog.action', compact('blog', 'module_name'))->render();
                })
                ->addIndexColumn()
                ->rawColumns(['title', 'action', 'status', 'check'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(BlogRequest $request)
    {
        // if(demoUserPermission()){
        //     return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        // }
    
        $data = $request->all();
        $data = $request->except('blog_attachment');
        if ($request->has('description')) {
            // Preserve user formatting while sanitizing dangerous HTML
            $data['description'] = Purifier::clean($request->description);
        }
        $data['tags'] = isset($request->tags) ? json_encode($request->tags) : null;
        $data['author_id'] = !empty($request->author_id) ? $request->author_id : auth()->user()->id;
        $data['is_featured'] = 0;
        if ($request->has('is_featured')) {
            $data['is_featured'] = 1;
        }
        
        $blog = Blog::updateOrCreate(['id' => $data['id']], $data);
        
    
        // Handle media files
        if ($request->is('api/*')) {
            if ($request->has('attachment_count')) {
                $files = [];
                for ($i = 0; $i < $request->attachment_count; $i++) {
                    $attachmentKey = "blog_attachment_" . $i;
                    if (!empty($request->$attachmentKey)) {
                        $files[] = $request->$attachmentKey;
                    }
                }
    
                // Clear existing media (optional)
                $blog->clearMediaCollection('blog_attachment');
    
                // Add new media files
                foreach ($files as $file) {
                    $blog->addMedia($file)->toMediaCollection('blog_attachment');
                }
            }
        } else {
            if ($request->hasFile('blog_attachment')) {
                // Clear existing media (optional)
                $blog->clearMediaCollection('blog_attachment');
    
                // Loop through and add each uploaded file
                foreach ($request->file('blog_attachment') as $file) {
                    $blog->addMedia($file)->toMediaCollection('blog_attachment');
                }
            }
        }
    
        $message = trans('messages.update_form', ['form' => trans('messages.blogs')]);
        if ($blog->wasRecentlyCreated) {
            $message = trans('messages.save_form', ['form' => trans('messages.blogs')]);
        }
    
        return redirect()->route('backend.blog.index')->withSuccess($message);
    }

    public function destroy($id)
    {
        // Attempt to find the blog post by its ID
        $blog = Blog::find($id);       
        if ($blog) {
            $blog->delete();
            $msg = __('messages.msg_deleted', ['name' => __('messages.blog')]);
        } else {
            $msg = __('messages.msg_fail_to_delete', ['name' => __('messages.blog')]);
        }
    
        if (request()->is('api/*')) {
            return comman_message_response($msg);
        }
        
        return comman_custom_response(['message' => $msg, 'status' => $blog ? true : false]);
    }
    
    public function action(Request $request){
        $id = $request->id;
        $blog  = Blog::withTrashed()->where('id',$id)->first();
        $msg = __('messages.not_found_entry',['name' => __('messages.blog')] );
        if($request->type == 'restore') {
            $blog->restore();
            $msg = __('messages.msg_restored',['name' => __('messages.blog')] );
        }
     
        if($request->type === 'forcedelete'){
            $blog->forceDelete();
            $msg = __('messages.msg_forcedelete',['name' => __('messages.blog')] );
        }
        if(request()->is('api/*')){
            return comman_message_response($msg);
		}
        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }

    public function forceDelete($id)
    {
        $data = Blog::withTrashed()->findOrFail($id);
        $data->forceDelete();
        return response()->json(['message' => 'Tax entry permanently deleted']);
    }
    /**
     * Remove the specified resource from storage.
     */
    
    public function restore($id)
    {
        $blog = Blog::withTrashed()->find($id);
        $blog->restore();
        return response()->json(['status' => true, 'message' => trans('messages.msg_restored', ['name' => trans('messages.blog')])]);
    }
    public function removeMedia($id, $media_id)
    {
        $blog = Blog::findOrFail($id);
        $media = $blog->getMedia('blog_attachment')->find($media_id);
        
        if ($media) {
            $media->delete();
            return redirect()->back()->withSuccess(trans('messages.media_removed'));
        }

        return redirect()->back()->withErrors(trans('messages.error_removing_media'));
    }

    public function update_status(Request $request, Blog $id)
    {
        // Prevent enabling status for soft-deleted blogs
        if ($id->trashed()) {
            return response()->json(['status' => false, 'message' => __('messages.item_is_deleted')], 422);
        }
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('service_providers.status_update')]);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $services = Blog::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_service_update');
                break;

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }

                // Soft delete and auto-disable handled by model event
                Blog::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_service_delete');
                break;
            
            case 'restore':
                $blogs = Blog::withTrashed()->whereIn('id', $ids);
                $blogs->restore();
                $message = __('messages.bulk_service_restore');
                break;

            case 'permanently-delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Blog::withTrashed()->whereIn('id', $ids)->forceDelete();
                $message = __('messages.bulk_service_permanently_deleted');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }


}
