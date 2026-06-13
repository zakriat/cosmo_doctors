<?php

namespace Modules\Frontend\Http\Controllers;

use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Blog\Models\Blog;

class BlogController extends Controller
{
    public function blogsList()
    {
        $blogs = Blog::with('author')->latest()->get();

        return view('frontend::blogs', compact('blogs'));
    }

    public function index_data(Request $request)
    {
        $search = $request->input('search');
        $blog_list = Blog::with('author')->where('status', 1);

        if ($search) {
            $blogs = $blog_list->where('title', 'like', '%' . $search . '%');
        }

        $blogs = $blog_list->orderBy('updated_at', 'desc');

        return DataTables::of($blogs)
            ->addColumn('card', function ($blog) {
                return view('frontend::components.card.blog_card', compact('blog'))->render();
            })
            ->rawColumns(['card'])
            ->make(true);
    }

    public function blogDetails($id)
    {
        $blog = Blog::with('author')->findOrFail($id);

        // Increment unique view per session
        $sessionKey = 'blog_viewed_' . $blog->id;
        if (!session()->has($sessionKey)) {
            $blog->increment('total_views');
            session()->put($sessionKey, true);
        }

        $previous_blog = Blog::where('id', '<', $id)->latest()->first();

        $next_blog = Blog::where('id', '>', $id)->oldest()->first();

        $related_blogs = Blog::where('id', '!=', $id)->latest()->take(6)->get();

        return view('frontend::blog_detail', compact('blog', 'previous_blog', 'next_blog', 'related_blogs'));
    }
}
