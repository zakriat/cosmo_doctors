<a href="{{ route('blog-details', ['id' => $blog->id]) }}" class="card-link">
    <div class="blog-card rounded">
        <div class="blog-img d-flex justify-content-center align-items-center" style="height: 250px; overflow: hidden;">
            <img src="{{  $blog->getFirstMediaUrl('blog_attachment', 'thumb') ?: asset('img/default.webp') }}" alt="image-blog" class="w-100 h-100 object-contain">
        </div>
        <div class="card-body blog-content p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                {{-- @if ($blog->tags)
                @foreach (json_decode($blog->tags, true) as $tag)
                    <a class="bg-secondary rounded-pill text-uppercase badge">{{ trim($tag)}}</a>
                @endforeach
            @endif --}}
                <span class="font-size-12 fw-medium">{{ $blog->created_at->format('d M, Y') }}</span>
            </div>
            <h6 class="line-count-2">
                {{ $blog->title }}
            </h6>
            <div class="mt-5">
                <label class="text-medium d-block">Words by</label>
                <a class="m-0 font-size-14 text-uppercase text-primary text-decoration-underline fw-semibold blog-author">
                    {{ optional($blog->author)->first_name . ' ' . optional($blog->author)->last_name ?? 'Unknown' }}
                </a>
            </div>
        </div>
    </div>
</a>
