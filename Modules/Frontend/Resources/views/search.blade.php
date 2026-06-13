@extends('frontend::layouts.master')

@section('content')
    <div class="list-page section-spacing px-0">
        <div class="page-title" id="page_title">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 d-lg-block d-none"></div>
                    <div class="col-lg-8 search-box text-center">
                        <form>
                            <div class="input-group custom-search-group position-relative mb-3">
                                <span class="input-group-text"><i class="ph ph-magnifying-glass"></i></span>
                                <input type="text" id="search-query" class="form-control search-input" placeholder="{{ __('messages.search') }}...">
                                <span class="clear-icon d-none" id="remove-search">
                                    <i class="ph ph-x-circle"></i>
                                </span>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-2 d-lg-block d-none"></div>
                </div>

                <!-- Search Results -->
                <div class="my-4">

                    <div class="row gy-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4" id="results"></div>

                </div>
                <div class="row gy-4 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 d-none" id="shimmer-loader">
                    @for ($i = 0; $i < 8; $i++)
                        <div class="serach-card p-3 text-center rounded placeholder-glow search-shimmer-card"
                            style="width: 300px;">
                            <div class="position-relative">
                                <div class="d-block">
                                    <div class="placeholder rounded object-cover" style="width: 100%; height: 150px;"></div>
                                </div>
                                <h6 class="line-count-1 mt-3 mb-2 pb-1 placeholder rounded"
                                    style="height: 20px; width: 70%; margin: 10px auto;"></h6>
                                <p class="mb-0 text-capitalize line-count-2 placeholder rounded"
                                    style="height: 14px; width: 90%; margin: 5px auto;"></p>
                                <p class="mb-0 text-capitalize line-count-2 placeholder rounded"
                                    style="height: 14px; width: 80%; margin: 5px auto;"></p>
                            </div>
                        </div>
                    @endfor
                </div>
                <!-- No Results -->
                <div id="no_result" class="mt-4 d-none">
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <img src="{{ asset('img/frontend/search-not-found.png') }}" alt="image" class="img-fluid">
                        <div>
                            <h5 class="mb-3">Sorry, we couldn't find your search!</h5>
                            <p class="m-0">Try something new</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let debounceTimer;
        const removeSearchButton = document.getElementById('remove-search');
        const resultsContainer = document.getElementById('results');
        const noResultContainer = document.getElementById('no_result');
        const shimmerLoader = document.getElementById('shimmer-loader');

        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get('query');

        const searchInput = document.querySelector('.search-input');
        searchInput.value = query || '';

        if (query) {
            toggleRemoveButton();
            search(query);
        }

        function toggleRemoveButton() {
            if (searchInput.value.trim() !== '') {
                removeSearchButton.classList.remove('d-none');
            } else {
                removeSearchButton.classList.add('d-none');
            }
        }

        searchInput.addEventListener('input', function() {
            toggleRemoveButton();
            search(this.value.trim());
        });

        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                search(this.value.trim());
            }
        });

        removeSearchButton.addEventListener('click', function() {
            searchInput.value = '';
            toggleRemoveButton();
            searchInput.dispatchEvent(new Event('input'));
            window.history.pushState({}, '', `${window.location.origin}${window.location.pathname}`);
        });

        function search(query) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (query.length === 0) {
                    resultsContainer.innerHTML = ''; 
                    noResultContainer.classList.remove('d-none'); 
                    shimmerLoader.classList.add('d-none');
                } else {
                    performSearch(query);
                }
            }, 300);
        }

        function performSearch(query) {
            const searchRoute = "{{ route('getSearchData') }}";
            const searchApiUrl = `${searchRoute}?search=${encodeURIComponent(query)}`;
            shimmerLoader.classList.remove('d-none'); 
            $.ajax({
                url: searchApiUrl,
                method: 'GET',
                success: function(response) {
                    shimmerLoader.classList.add('d-none'); 
                    if (response.status) {
                        if (!response.html) {
                            resultsContainer.innerHTML = ''; 
                            noResultContainer.classList.remove('d-none'); 
                        } else {
                            resultsContainer.innerHTML = response.html; 
                            noResultContainer.classList.add('d-none'); 
                        }
                    }
                },
                error: function(xhr) {
                    console.error(xhr); 
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const headerSearch = document.querySelector('header #search-query');
        const pageSearch = document.querySelector('.search-input');
        const removeSearchButton = document.getElementById('remove-search');
        const resultsContainer = document.getElementById('results');
        const noResultContainer = document.getElementById('no_result');

        function syncSearchFields(value) {
            if (headerSearch) headerSearch.value = value;
            if (pageSearch) pageSearch.value = value;
            
            if (removeSearchButton) {
                if (value.trim() !== '') {
                    removeSearchButton.classList.remove('d-none');
                } else {
                    removeSearchButton.classList.add('d-none');
                }
            }

            if (value.trim() === '') {
                resultsContainer.innerHTML = '';  
                noResultContainer.classList.remove('d-none');  
            } else {
                noResultContainer.classList.add('d-none');  
            }
        }

        if (headerSearch) {
            headerSearch.addEventListener('input', function(e) {
                syncSearchFields(e.target.value);
                if (typeof search === 'function') {
                    search(e.target.value.trim());
                }
            });
        }

        if (pageSearch) {
            pageSearch.addEventListener('input', function(e) {
                syncSearchFields(e.target.value);
            });
        }

        if (removeSearchButton) {
            removeSearchButton.addEventListener('click', function() {
                syncSearchFields('');
                if (typeof search === 'function') {
                    search('');
                }
                window.history.pushState({}, '', `${window.location.origin}${window.location.pathname}`);
            });
        }

        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get('query');
        if (query) {
            syncSearchFields(query);
        }
    });

</script>
@endpush
