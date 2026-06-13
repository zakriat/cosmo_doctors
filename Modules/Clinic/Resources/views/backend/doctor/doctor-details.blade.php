{{-- ===== Doctor Details Offcanvas ===== --}}
@php
    // Ensure $data is always defined as an array to avoid undefined variable errors
    $data = $data ?? [];
@endphp
<div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="doctor-details-form-offcanvas"
    aria-labelledby="doctorDetailsLabel">
    <div class="offcanvas-header border-bottom">
        <h6 class="m-0 h5" id="doctorDetailsLabel">{{ __('clinic.doctor_details') }}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <form id="doctor-details-form" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <input type="hidden" id="doctor_id" name="doctor_id" value="{{ $data['id'] ?? '' }}">

            <div>
                <!-- About Doctor -->
                <h5 class="mb-3">{{ __('clinic.about_doctor') }}</h5>

                @if (empty($data) ||
                        (empty($data['id']) &&
                            empty($data['full_name']) &&
                            empty($data['email']) &&
                            empty($data['mobile']) &&
                            empty($data['profile_image']) &&
                            empty($data['about']) &&
                            empty($data['address']) &&
                            empty($data['gender']) &&
                            empty($data['commissions']) &&
                            empty($data['total_appointment']) &&
                            empty($data['specialization']) &&
                            empty($data['total_sessions']) &&
                            empty($data['experience'])))
                    <div class="card">
                        <div class="card-body p-5">
                            <h6 class="text-muted text-center mb-0">{{ __('clinic.no_data_available') }}</h6>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body p-5">
                            <div class="d-flex gap-4 align-items-center flex-wrap">
                                <img id="imagePreview"
                                    src="{{ !empty($data['profile_image']) ? $data['profile_image'] : asset('img/avatar/avatar.webp') }}"
                                    alt="{{ __('clinic.profile_image') }}"
                                    class="img-fluid avatar avatar-80 avatar-rounded">
                                <div>
                                    <h5 class="mb-3">Dr. {{ $data['full_name'] ?? '-' }}</h5>
                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                        <div class="d-inline-flex align-items-center gap-2 me-sm-2">
                                            <i class="ph ph-envelope mb-0 h5 align-middle"></i>
                                            <a href="mailto:{{ $data['email'] ?? '' }}"
                                                class="text-decoration-underline">{{ $data['email'] ?? '-' }}</a>
                                        </div>
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <i class="ph ph-phone mb-0 h5 align-middle"></i>
                                            <a href="tel:{{ $data['mobile'] ?? '' }}"
                                                class="text-decoration-underline">{{ $data['mobile'] ?? '-' }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mt-4">
                                <!-- About -->
                                @if (!empty($data['about']))
                                    <div class="my-5">
                                        <span class="mb-1">{{ __('clinic.about') }} :</span>
                                        <p class="mb-0 text-dark fw-semibold">{{ $data['about'] }}</p>
                                    </div>
                                @endif
    
                                <!-- Address -->
                                @if (!empty($data['address']))
                                    <div class="mt-3">
                                        <p class="mb-1">{{ __('clinic.lbl_address') }} :</p>
                                        <h6 class="mb-0">{{ $data['address'] }}</h6>
                                    </div>
                                @endif
    
                                <!-- Gender -->
                                @if (!empty($data['gender']))
                                    <div class="mt-3">
                                        <p class="mb-1">{{ __('clinic.lbl_gender') }} :</p>
                                        <h6 class="mb-0">{{ ucfirst($data['gender']) }}</h6>
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <p class="mb-1">{{ __('clinic.lbl_gender') }} :</p>
                                        <h6 class="mb-0 text-muted">{{ __('clinic.data_not_found') }}</h6>
                                    </div>
                                @endif

                                <!-- Commissions -->
                                @if (!empty($data['commissions']))
                                    <div class="d-flex flex-column gap-2 mt-3">
                                        <p class="mb-1">{{ __('clinic.commission') }} :</p>
                                        @foreach ($data['commissions'] as $commission)
                                            <h6 class="mb-0">{{ $commission['value'] ?? '-' }}
                                                ({{ $commission['title'] ?? '-' }})</h6>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex flex-column gap-2 mt-3">
                                        <p class="mb-1">{{ __('clinic.commission') }} :</p>
                                        <h6 class="mb-0 text-muted">{{ __('clinic.data_not_found') }}</h6>
                                    </div>
                                @endif
                            </div>


                            <!-- Totals / Meta -->
                            <div class="d-flex justify-content-between gap-2 flex-wrap mt-3">
                                <div>
                                    <p class="mb-1">{{ __('clinic.total_appointment') }}</p>
                                    <h6 class="mb-0">
                                        @if (isset($data['appointment']))
                                            {{ $data['appointment'] }}
                                        @else
                                            <span class="text-muted">{{ __('clinic.data_not_found') }}</span>
                                        @endif
                                    </h6>
                                </div>
                                <div>
                                    <p class="mb-1">{{ __('clinic.expertize_in') }}</p>
                                    <h6 class="mb-0">
                                        @if (!empty($data['specialization']))
                                            {{ $data['specialization'] }}
                                        @else
                                            <span class="text-muted">{{ __('clinic.data_not_found') }}</span>
                                        @endif
                                    </h6>
                                </div>
                                <div>
                                    <p class="mb-1">{{ __('clinic.available_session_count') }}</p>
                                    <h6 class="mb-0">
                                        @if (isset($data['total_sessions']))
                                            {{ $data['total_sessions'] }}
                                        @else
                                            <span class="text-muted">{{ __('clinic.data_not_found') }}</span>
                                        @endif
                                    </h6>
                                </div>
                                <div>
                                    <p class="mb-1">{{ __('clinic.experience') }}</p>
                                    <h6 class="mb-0">
                                        @if (isset($data['experience']))
                                            {{ $data['experience'] }}
                                        @else
                                            <span class="text-muted">{{ __('clinic.data_not_found') }}</span>
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Services -->
                <div class="d-flex justify-content-between align-items-center mb-2 gap-3 mt-4">
                    <h5 class="mb-0">{{ __('clinic.services') }}</h5>
                    {{-- <a href="{{ route('backend.services.index') }}" class="text-secondary" role="button">View all</a> --}}
                </div>
                <div class="card">
                    <div class="card-body p-5">
                        <ul class="list-inline m-0 p-0" id="doctor-services-list">
                            @if (!empty($data['services']) && count($data['services']) > 0)
                                @foreach ($data['services'] as $index => $service)
                                    <li class="mb-3 doctor-service-item {{ $index > 4 ? 'd-none' : '' }}">
                                        <div class="px-3 py-4 bg-body rounded-3">
                                            <div
                                                class="d-flex align-items-sm-center align-items-start justify-content-between flex-sm-row flex-column gap-3">
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-2">{{ $service->servicename ?? '-' }}</h5>
                                                    <div class="d-flex gap-3 flex-wrap">
                                                        <p class="mb-0">
                                                            {{ __('clinic.total_appointments_done') }} :
                                                            <span class="font-title">
                                                                @php
                                                                    $serviceCount = 0;
                                                                    if (
                                                                        isset($data['total_appointment']) &&
                                                                        is_array($data['total_appointment'])
                                                                    ) {
                                                                        foreach (
                                                                            $data['total_appointment']
                                                                            as $appointment
                                                                        ) {
                                                                            if (
                                                                                isset($appointment['service_id']) &&
                                                                                $appointment['service_id'] ==
                                                                                    $service->service_id
                                                                            ) {
                                                                                $serviceCount = $appointment['count'];
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp
                                                                {{ $serviceCount }}
                                                            </span>
                                                        </p>
                                                        <p class="mb-0">
                                                            {{ __('clinic.clinic') }}
                                                            <span class="font-title">
                                                                {{ $service->clinic_name ?? '-' }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <h3 class="mb-0 text-primary">
                                                    @if (isset($service->charges))
                                                        {{ Currency::format($service->charges) }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </h3>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <li>
                                    <div class="px-3 py-4 bg-body rounded-3">
                                        <div
                                            class="d-flex align-items-sm-center align-items-start justify-content-between flex-sm-row flex-column gap-3">
                                            <div class="flex-grow-1">
                                                <h5 class="mb-2 text-muted">{{ __('clinic.no_data_available') }}</h5>
                                                <div class="d-flex gap-3 flex-wrap">
                                                    <p class="mb-0">
                                                        {{ __('clinic.total_appointments_done') }} :
                                                        <span class="font-title text-muted">N/A</span>
                                                    </p>
                                                    <p class="mb-0">
                                                        {{ __('clinic.clinic') }}
                                                        <span class="font-title text-muted">-</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <h3 class="mb-0 text-muted">-</h3>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                        @if (!empty($data['services']) && count($data['services']) > 5)
                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-link px-0 position-relative"
                                    id="show-more-services-btn" tabindex="0">
                                    {{-- Show More --}}
                                    {{ __('clinic.show_more') }}
                                </button>
                                <button type="button" class="btn btn-link px-0 position-relative d-none"
                                    id="show-less-services-btn" tabindex="0">
                                    {{ __('clinic.show_less') }}
                                </button>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Reviews -->
                <div class="d-flex justify-content-between align-items-center mb-2 gap-3 mt-4">
                    <h5 class="mb-0">{{ __('clinic.reviews') }}</h5>
                </div>
                <div>
                    @if (!empty($data['ratings']) && count($data['ratings']) > 0)
                        @foreach ($data['ratings'] as $review)
                            <div class="card mb-3">
                                <div class="card-body p-3">
                                    <div
                                        class="d-flex align-items-sm-center align-items-start justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge rounded-4 bg-body heading-color border">
                                                <div class="d-flex gap-1 align-items-center">
                                                    <span class="text-warning">&#9733;</span>
                                                    <span class="mb-0 text-primary lh-sm fs-12 fw-bold">
                                                        @if (isset($review->rating))
                                                            {{ $review->rating }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </span>
                                            <h5 class="mb-0">{{ $review->title ?? 'Review' }}</h5>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="fs-12 fw-semibold">
                                                @if (isset($review->created_at))
                                                    {{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <h6 class="mb-0 lh-1">
                                            By
                                            @if (isset($review->user) && isset($review->user->first_name))
                                                {{ $review->user->first_name }} {{ $review->user->last_name }}
                                            @else
                                                Anonymous
                                            @endif
                                        </h6>
                                        <p class="mt-2 mb-0 fs-12">
                                            {{ $review->comment ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card mb-3">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-center align-items-center py-5">
                                    <h5 class="mb-0 text-muted text-center">{{ __('clinic.no_data_available') }}</h5>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var servicesList = document.getElementById('doctor-services-list');
        var showMoreBtn = document.getElementById('show-more-services-btn');
        var showLessBtn = document.getElementById('show-less-services-btn');
        var itemsPerPage = 5;

        function getVisibleCount() {
            var items = document.querySelectorAll('#doctor-services-list .doctor-service-item:not(.d-none)');
            return items.length;
        }

        function getTotalCount() {
            var items = document.querySelectorAll('#doctor-services-list .doctor-service-item');
            return items.length;
        }

        function showMoreHandler(e) {
            e.preventDefault();
            var items = document.querySelectorAll('#doctor-services-list .doctor-service-item.d-none');
            var count = 0;
            items.forEach(function(item) {
                if (count < itemsPerPage) {
                    item.classList.remove('d-none');
                    count++;
                }
            });

            // Update button visibility after showing more items
            var remaining = document.querySelectorAll('#doctor-services-list .doctor-service-item.d-none');
            var visibleCount = getVisibleCount();
            var totalCount = getTotalCount();

            // Hide "Show More" button if no more items to show
            if (remaining.length === 0 && showMoreBtn) {
                showMoreBtn.classList.add('d-none');
            }

            // Show "Show Less" button if more than itemsPerPage are visible OR all items are visible
            console.log('After show more - Visible:', visibleCount, 'Total:', totalCount, 'ItemsPerPage:',
                itemsPerPage);
            if (showLessBtn && (visibleCount > itemsPerPage || visibleCount === totalCount)) {
                showLessBtn.classList.remove('d-none');
                console.log('Show Less button shown after show more');
            } else {
                console.log('Show Less button NOT shown - condition not met');
            }
        }

        function showLessHandler(e) {
            e.preventDefault();
            var items = document.querySelectorAll('#doctor-services-list .doctor-service-item:not(.d-none)');
            var total = items.length;

            // Only hide if more than itemsPerPage are visible
            if (total > itemsPerPage) {
                // Hide items beyond the first itemsPerPage
                for (var i = itemsPerPage; i < total; i++) {
                    items[i].classList.add('d-none');
                }
            }

            // Update button visibility
            if (showMoreBtn) {
                showMoreBtn.classList.remove('d-none');
            }
            if (showLessBtn) {
                showLessBtn.classList.add('d-none');
            }
        }

        // Remove any previous event listeners to avoid duplicate triggers
        if (showMoreBtn) {
            showMoreBtn.removeEventListener('click', showMoreHandler);
            showMoreBtn.addEventListener('click', showMoreHandler);
        }
        if (showLessBtn) {
            showLessBtn.removeEventListener('click', showLessHandler);
            showLessBtn.addEventListener('click', showLessHandler);
        }

        // Also handle if the button is re-rendered (e.g., via AJAX), use event delegation as fallback
        document.body.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'show-more-services-btn') {
                showMoreHandler(e);
            }
            if (e.target && e.target.id === 'show-less-services-btn') {
                showLessHandler(e);
            }
        });

        // On load, ensure correct button visibility
        function updateButtonsOnLoad() {
            if (!showMoreBtn || !showLessBtn) return;
            var total = getTotalCount();
            var visible = getVisibleCount();

            console.log('Initial state - Total:', total, 'Visible:', visible, 'ItemsPerPage:', itemsPerPage);

            if (total > itemsPerPage) {
                // Show "Show More" button if there are hidden items
                if (visible < total) {
                    showMoreBtn.classList.remove('d-none');
                    console.log('Show More button shown');
                } else {
                    showMoreBtn.classList.add('d-none');
                    console.log('Show More button hidden');
                }

                // Show "Show Less" button if more than itemsPerPage are visible OR all items are visible
                if (visible > itemsPerPage || visible === total) {
                    showLessBtn.classList.remove('d-none');
                    console.log('Show Less button shown');
                } else {
                    showLessBtn.classList.add('d-none');
                    console.log('Show Less button hidden');
                }
            } else {
                // Hide both buttons if total items <= itemsPerPage
                showMoreBtn.classList.add('d-none');
                showLessBtn.classList.add('d-none');
                console.log('Both buttons hidden - not enough items');
            }
        }
        updateButtonsOnLoad();
    });
</script>
