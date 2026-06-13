<div class="category-card text-center rounded">
    @if($category)
    <a href="{{ route('services', ['category_id' => $category->id]) }}" class="d-block">
        <div class="category-img d-inline-block">
            <img src="{{ $category->file_url }}" alt="category-image" class="img-fluid rounded-circle">
        </div>
        <h6 class="category-content text-capitalize fw-bold mt-4 mb-0"> {{ $category->name ?? '' }} </h6>
        <p class="mb-0 mt-2 text-capitalize line-count-2 text-muted font-size-14">{{ $category->description ?? '' }}</p>
    </a>
    @endif
</div>
