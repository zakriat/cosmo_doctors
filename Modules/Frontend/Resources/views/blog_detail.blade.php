@extends('frontend::layouts.master')

@section('content')

@section('title', __('frontend.blog_details'))

@include('frontend::components.section.breadcrumb')

<div class="list-page section-spacing px-0">
    <div class="page-title" id="page_title">
        <div class="container">
            <div class="blog-detail-card">
                <a href="{{ route('blogs') }}" class="font-size-14 fw-semibold"><i class="ph ph-caret-left me-2"></i> Back</a>
                <img src="{{ $blog->getFirstMediaUrl('blog_attachment', 'thumb') ?? asset('img/frontend/default-blog.jpg') }}" alt="blog-image" class="w-100 rounded object-cover blog-images mt-5">
                <div class="d-flex flex-wrap justify-content-between align-items-center column-gap-5 row-gap-3 my-4 py-4">
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <span class="badge bg-secondary font-size-14 rounded-pill text-uppercase">{{ $blog->title }}</span>
                        <span class="font-size-14 fw-medium">{{ $blog->created_at->format('d M, Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
                        <label class="text-medium d-block">Words by</label>
                        <a class="m-0 font-size-14 text-uppercase text-primary text-decoration-underline fw-semibold blog-author">
                            {{ optional($blog->author)->first_name . ' ' . optional($blog->author)->last_name ?? 'Unknown' }}
                        </a>
                    </div>
                </div>
                <h3>{{ $blog->title }}</h3>

                <div class="blog-detail-content-2">
                    {!! $blog->description !!}
                </div>

              <!-- Blog Pagination: Previous and Next Posts -->
              <div class="blog-pagination">
                <div class="d-flex flex-wrap justify-content-between align-items-center column-gap-4 row-gap-3">
                    <!-- Previous Post -->
                    @if($previous_blog)
                    <div class="d-flex flex-column blog-pagination-btn">
                        <a href="{{ route('blog-details', $previous_blog->id) }}"
                           class="link d-flex align-items-center gap-2">
                            <i class="ph ph-arrow-left align-middle"></i>
                            <h5 class="m-0 heading-color">Previous Post</h5>
                        </a>
                        <div class="mt-2 pt-1 d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <img src="{{ $previous_blog->getFirstMediaUrl('blog_attachment', 'thumb') }}" class="img-fluid blog-pagination-image rounded" alt="previous blog image">
                            </div>
                            <a href="{{ route('blog-details', $previous_blog->id) }}" class="fst-italic link">
                                <span>{{ $previous_blog->title }}</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Next Post -->
                    @if($next_blog)
                    <div class="d-flex flex-column align-items-end blog-pagination-btn active">
                        <a href="{{ route('blog-details', $next_blog->id) }}"
                           class="link d-flex align-items-center gap-2">
                            <h5 class="m-0 heading-color">Next Post</h5>
                            <i class="ph ph-arrow-right align-middle"></i>
                        </a>
                        <div class="mt-2 pt-1 d-flex align-items-center justify-content-end gap-3">
                            <a href="{{ route('blog-details', $next_blog->id) }}" class="fst-italic link">
                                <span>{{ $next_blog->title }}</span>
                            </a>
                            <div class="flex-shrink-0">
                                <img src="{{ $next_blog->getFirstMediaUrl('blog_attachment', 'thumb') }}" class="img-fluid blog-pagination-image rounded" alt="next blog image">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>


                <!-- Related Blogs -->
                @if($related_blogs->isNotEmpty())
                <div class="related-blog">
                    <h4>Related Blogs</h4>
                    <div class="row gy-4 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3">
                        @foreach ($related_blogs->take(3) as $related_blog)
                            <div class="col">
                                @include('frontend::components.card.blog_card', ['blog' => $related_blog])
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
