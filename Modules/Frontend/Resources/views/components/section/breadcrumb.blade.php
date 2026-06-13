<div class="bg-primary-subtle py-5">
    <div class="container">
        <div class="d-flex justify-content-center align-items-center">
            <nav class="breadcrumb-container" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    @foreach(generateBreadcrumb() as $item)

                    

                        @if($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">{{ $item['name'] }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>
</div>