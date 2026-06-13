<header class="header-center-home header-default header-sticky header-one">
    @php
        $headerSection = Modules\FrontendSetting\Models\FrontendSetting::where('key', 'heder-menu-setting')->first();
        $sectionData = $headerSection ? json_decode($headerSection->value, true) : null;
    @endphp

    <nav class="nav navbar navbar-expand-xl navbar-light iq-navbar header-hover-menu py-xl-0">
        <div class="container-fluid navbar-inner">
            <div class="d-flex align-items-center justify-content-between w-100 landing-header">
                <div class="d-flex gap-3 gap-xl-0 align-items-center">
                    <button type="button" data-bs-toggle="offcanvas" data-bs-target="#navbar_main"
                        aria-controls="navbar_main" class="d-xl-none btn btn-primary rounded-pill toggle-rounded-btn">
                        <i class="ph ph-arrow-right"></i>
                    </button>
                    <!--Logo -->
                    @include('frontend::components.partials.logo')
                </div>

                <!-- navigation -->
                @if ($sectionData['header_setting'] == 1)
                    @include('frontend::components.partials.horizontal-nav', [
                        'sectionData' => $sectionData,
                    ])
                @endif
                <div class="right-panel">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-btn">
                            <span class="navbar-toggler-icon"></span>
                        </span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <div
                            class="d-flex flex-md-row flex-column align-items-md-center align-items-end justify-content-end gap-md-3 gap-2">
                            @if ($sectionData['header_setting'] == 1 && $sectionData['enable_search'] == 1)
                                <ul class="navbar-nav align-items-center list-inline justify-content-end mt-md-0 mt-2">
                                    <li class="p-0 header-search">
                                        <div class="form-group input-group mb-0">
                                            <button type="submit" id="search-button"
                                                class="btn btn-link bg-body search-submit">
                                                <i class="ph ph-magnifying-glass"></i>
                                            </button>
                                            <input type="text" id="search-query"
                                                class="form-control border-0 bg-body" placeholder="{{ __('messages.search') }}...">
                                        </div>
                                    </li>
                                </ul>
                            @endif

                            <ul class="navbar-nav align-items-center mb-0 ps-0 justify-content-end">
                                @if ($sectionData['header_setting'] == 1)
                                    @if ($sectionData['enable_darknight_mode'] == 1)
                                        <li class="nav-item theme-scheme-switch">
                                            <a href="javascript:void(0)"
                                                class="nav-link d-flex align-items-center change-mode">
                                                <span class="light-mode">
                                                    <i class="ph ph-sun"></i>
                                                </span>
                                                <span class="dark-mode">
                                                    <i class="ph ph-moon"></i>
                                                </span>
                                            </a>
                                        </li>
                                    @endif

                                    <?php
                                    $notifications_count = 0;
                                    if (auth()->user()) {
                                        $notifications_count = optional(auth()->user())->unreadNotifications->count();
                                    }
                                    ?>
                                    @if (auth()->user() && auth()->user()->getRoleNames()->contains('user'))
                                        <li class="nav-item dropdown iq-dropdown header-notification dropdown-notification-wrapper">
                                            <a class="nav-link btn-action"
                                                data-bs-toggle="dropdown" href="#">
                                                <div class="iq-sub-card">
                                                    <div class="notification_list">
                                                        <span class="btn-inner">
                                                            <i class="ph ph-bell"></i>
                                                        </span>
                                                        @if ($notifications_count > 0)
                                                            <span class="notification-alert">{{ $notifications_count }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                            <ul class="p-0 sub-drop dropdown-menu dropdown-menu-end">
                                                <div class="m-0 shadow-none card notification_data"></div>
                                            </ul>
                                        </li>


                                        {{-- <li class="nav-item dropdown iq-dropdown header-notification dropdown-notification-wrapper">
                                            <a class="nav-link btn-action"
                                                data-bs-toggle="dropdown" href="#">
                                                <div class="iq-sub-card">
                                                    <div class="d-flex align-items-center notification_list">
                                                        <span class="btn-inner">
                                                            <i class="ph ph-bell"></i>
                                                        </span>
                                                        @if ($notifications_count > 0)
                                                            <span
                                                                class="notification-alert">{{ $notifications_count }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-notification-menu shadow" aria-labelledby="navbarDropdown" data-bs-popper="static">
                                                <div class="d-flex justify-content-between align-items-center gap-3 rounded mb-3 pb-1">
                                                    <h6 class="m-0">Notifications</h6>
                                                    <button type="button" class="btn btn-close p-0"></button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center gap-3">
                                                    <!-- <div>
                                                        <ul class="nav nav-pills gap-2 m-0" id="pills-tab" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="pills-notification-all-tab" data-bs-toggle="pill" data-bs-target="#pills-notification-all" type="button" role="tab" aria-controls="pills-notification-all" aria-selected="true">All</button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="pills-unread-tab" data-bs-toggle="pill" data-bs-target="#pills-unread" type="button" role="tab" aria-controls="pills-unread" aria-selected="false">Unread</button>
                                                            </li>
                                                        </ul>

                                                    </div> -->
                                                    <a class="notification-link text-end">Mark all as read</a>
                                                </div>
                                                <!-- <div class="tab-content my-3" id="pills-tabContent">
                                                    <div class="tab-pane fade show active" id="pills-notification-all" role="tabpanel" aria-labelledby="pills-notification-all-tab">All</div>
                                                    <div class="tab-pane fade" id="pills-unread" role="tabpanel" aria-labelledby="pills-unread-tab">Unread</div>
                                                </div> -->
                                                <ul class="d-flex flex-column list-inline m-0 p-0 notifcation-data">
                                                    <li class="notifcation-fields">
                                                            <span class="d-flex align-items-center justify-content-between gap-3">
                                                                <span class="d-flex gap-3">
                                                                    <span class="flex-shrik-0">
                                                                    <img src="{{ asset('img/frontend/notification.png') }}" class="img-fluid notification-droup-down-img rounded" alt="image">
                                                                    </span>
                                                                    <div>
                                                                        <div class="d-flex justify-content-between align-items-center gap-3 mb-1">
                                                                            <h6 class="m-0">Welcome to H &amp; W !</h6>
                                                                            <p class="m-0 notification-time">2 min ago</p>
                                                                        </div>
                                                                        <p class="mb-0 me-2 pe-2">Start your amazing journey to better kivicare</p>
                                                                    </div>
                                                                </span>
                                                            </span>
                                                    </li>
                                                    <li class="notifcation-fields">
                                                            <span class="d-flex align-items-center justify-content-between gap-3">
                                                                <span class="d-flex gap-3">
                                                                    <span class="flex-shrik-0">
                                                                    <img src="{{ asset('img/frontend/notification.png') }}" class="img-fluid notification-droup-down-img rounded" alt="image">
                                                                    </span>
                                                                    <div>
                                                                        <div class="d-flex justify-content-between align-items-center gap-3 mb-1">
                                                                            <h6 class="m-0">Account creation confirmation</h6>
                                                                            <p class="m-0 notification-time">6 hr ago</p>
                                                                        </div>
                                                                        <p class="mb-0 me-2 pe-2">Your account has been successfully created. Get ready to explore a
                                                                            healthier you!</p>
                                                                    </div>
                                                                </span>
                                                            </span>
                                                    </li>

                                                </ul>
                                            </div>
                                        </li> --}}
                                    @endif
                                    @if ($sectionData['enable_language'] == 1)
                                        <li class="nav-item dropdown dropdown-language-wrapper">
                                            <button class="btn bg-body text-body gap-3 px-md-3 px-2 dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <img src="{{ asset('flags/' . App::getLocale() . '.png') }}"
                                                    alt="flag" class="img-fluid me-2"
                                                    style="width: 20px; height: auto; min-width: 15px;"
                                                    onerror="this.onerror=null; this.src='{{ asset('flags/globe.png') }}';">
                                                {{ strtoupper(App::getLocale()) }}
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-language mt-0">
                                                @foreach (config('app.available_locales') as $locale => $title)
                                                    <a class="dropdown-item"
                                                        href="{{ route('language.switch', $locale) }}">
                                                        <span class="d-flex align-items-center gap-3">
                                                            <img src="{{ asset('flags/' . $locale . '.png') }}"
                                                                alt="flag"
                                                                class="img-fluid mr-2"style="width: 20px;height: auto;min-width: 15px;">
                                                            <span> {{ $title }}</span>
                                                            <span class="active-icon"><i
                                                                    class="ph-fill ph-check-fat align-middle"></i></span>
                                                        </span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </li>
                                    @endif
                                @endif
                                @if (auth()->check() && auth()->user()->hasRole('user'))
                                    <li class="nav-item flex-shrink-0 dropdown dropdown-user-wrapper">
                                        <a class="nav-link dropdown-user" href="#" id="navbarDropdown"
                                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <img src="{{ auth()->user()->profile_image }}"
                                                class="img-fluid user-image rounded-circle" alt="user image">
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end dropdown-user-menu shadow"
                                            aria-labelledby="navbarDropdown">
                                            <div
                                                class="p-3 d-flex justify-content-between align-items-center gap-3 rounded mb-4 pb-4 border-bottom">
                                                <div class="d-inline-flex align-items-center gap-3">
                                                    <div class="image flex-shrink-0">
                                                        <img src="{{ auth()->user()->profile_image }}"
                                                            class="img-fluid dropdown-user-menu-image" alt="">
                                                    </div>
                                                    <div class="content">
                                                        <h6 class="mb-1">
                                                            {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}
                                                        </h6>
                                                        <span
                                                            class="font-size-14 dropdown-user-menu-contnet">{{ auth()->user()->email }}</span>
                                                    </div>
                                                </div>
                                                <div class="link">
                                                    <a href="{{ route('edit-profile') }}" class="link-body-emphasis">
                                                        <i class="ph ph-pencil"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <ul class="d-flex flex-column gap-4 list-inline m-0 p-0">
                                                <!-- Book Appointment -->
                                                <li class="user-fields">
                                                    <a href="{{ route('services') }}" class="link-body-emphasis font-size-14">
                                                        <span class="d-flex align-items-center gap-3">
                                                            <span class="user-droup-down-img">
                                                                <i class="ph ph-calendar-plus"></i>
                                                            </span>
                                                            <span>
                                                                <h6 class="fw-semibold m-0">{{ __('frontend.book_appointment') }}</h6>
                                                                <small>{{ __('frontend.schedule_new_appointment') }}</small>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>
                                                

                                                <li class="user-fields">
                                                    <a href="{{ route('appointment-list') }}"
                                                        class="link-body-emphasis font-size-14">
                                                        <span class="d-flex align-items-center gap-3">
                                                            <span class="user-droup-down-img">
                                                                <i class="ph ph-calendar-check"></i>
                                                            </span>
                                                            <span>
                                                                <h6 class="fw-semibold m-0">{{ __('frontend.my_appointments') }}
                                                                </h6>
                                                                <small>{{ __('frontend.all_appointments') }}
                                                                </small>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="user-fields">
                                                    <a href="{{ route('encounter-list') }}"
                                                        class="link-body-emphasis font-size-14">
                                                        <span class="d-flex align-items-center gap-3">
                                                            <span class="user-droup-down-img">
                                                                <i class="ph ph-speedometer"></i>
                                                            </span>
                                                            <span>
                                                                <h6 class="fw-semibold m-0">{{ __('frontend.triage') }}
                                                                </h6>
                                                                <small>{{ __('frontend.close_active_triage') }}
                                                                </small>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="user-fields">
                                                    <a href="{{ route('account-setting') }}"
                                                        class="link-body-emphasis font-size-14">
                                                        <span class="d-flex align-items-center gap-3">
                                                            <span class="user-droup-down-img">
                                                                <i class="ph ph-lock"></i>
                                                            </span>
                                                            <span>
                                                                <h6 class="fw-semibold m-0">{{ __('frontend.setting') }}
                                                                </h6>
                                                                <small>{{ __('frontend.Change_password') }}
                                                                </small>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="user-fields">
                                                    <a data-bs-toggle="modal" data-bs-target="#logoout-account"
                                                        class="link-body-emphasis font-size-14 cursor-pointer">
                                                        <span class="d-flex align-items-center gap-3">
                                                            <span class="user-droup-down-img">
                                                                <i class="ph ph-sign-out"></i>
                                                            </span>
                                                            <span>
                                                                <h6 class="fw-semibold mb-0">{{ __('frontend.logout') }}
                                                                </h6>
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <a href="{{ route('login-page') }}" class="btn btn-secondary login-btn">
                                            {{ __('frontend.login') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>


<!-- logout modal -->
<div class="modal fade" id="logoout-account">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content section-bg">
            <div class="modal-body modal-body-inner">
                <div class="close-modal-btn" data-bs-dismiss="modal">
                    <i class="ph ph-x align-middle"></i>
                </div>
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <svg width="87" height="86" viewBox="0 0 87 86" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_295_21874)">
                            <path
                                d="M40.8125 72.5625C40.8125 73.2753 40.5294 73.9588 40.0253 74.4629C39.5213 74.9669 38.8378 75.25 38.125 75.25H16.625C15.9122 75.25 15.2287 74.9669 14.7247 74.4629C14.2206 73.9588 13.9375 73.2753 13.9375 72.5625V13.4375C13.9375 12.7247 14.2206 12.0412 14.7247 11.5372C15.2287 11.0331 15.9122 10.75 16.625 10.75H38.125C38.8378 10.75 39.5213 11.0331 40.0253 11.5372C40.5294 12.0412 40.8125 12.7247 40.8125 13.4375C40.8125 14.1503 40.5294 14.8338 40.0253 15.3378C39.5213 15.8419 38.8378 16.125 38.125 16.125H19.3125V69.875H38.125C38.8378 69.875 39.5213 70.1581 40.0253 70.6621C40.5294 71.1662 40.8125 71.8497 40.8125 72.5625ZM77.6514 41.0986L64.2139 27.6611C63.838 27.2848 63.359 27.0285 62.8374 26.9246C62.3158 26.8208 61.7751 26.874 61.2838 27.0776C60.7924 27.2812 60.3725 27.626 60.0773 28.0683C59.782 28.5106 59.6246 29.0307 59.625 29.5625V40.3125H38.125C37.4122 40.3125 36.7287 40.5956 36.2247 41.0997C35.7206 41.6037 35.4375 42.2872 35.4375 43C35.4375 43.7128 35.7206 44.3963 36.2247 44.9004C36.7287 45.4044 37.4122 45.6875 38.125 45.6875H59.625V56.4375C59.6246 56.9693 59.782 57.4894 60.0773 57.9317C60.3725 58.374 60.7924 58.7188 61.2838 58.9224C61.7751 59.126 62.3158 59.1792 62.8374 59.0754C63.359 58.9715 63.838 58.7152 64.2139 58.3389L77.6514 44.9014C77.9013 44.6518 78.0995 44.3554 78.2348 44.0292C78.37 43.7029 78.4396 43.3532 78.4396 43C78.4396 42.6468 78.37 42.2971 78.2348 41.9708C78.0995 41.6446 77.9013 41.3482 77.6514 41.0986Z"
                                fill="#5670CC" />
                        </g>
                        <defs>
                            <clipPath id="clip0_295_21874">
                                <rect width="86" height="86" fill="white" transform="translate(0.5)" />
                            </clipPath>
                        </defs>
                    </svg>
                    <div class="popup-content  text-center mt-5 pt-2">
                        <h6 class="font-size-18">Are you sure you want to log out?</h6>
                        <p class="mb-0 font-size-14">You can always log back in anytime to access your account and
                            continue where you left off.</p>
                        <div class="d-flex align-items-center flex-wrap justify-content-center gap-4 mt-4">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button id="logout-btn" class="btn btn-light">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span class="logout-text">Logout</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loader -->
<div class="loader simple-loader d-none">
    <div class="loader-body">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<!-- No Data Found -->

<!-- id="no-data-found" -->
<div class="modal fade" id="no-data-found">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content section-bg">
            <div class="modal-body modal-body-inner">
                <div class="close-modal-btn" data-bs-dismiss="modal">
                    <i class="ph ph-x align-middle"></i>
                </div>
                <div class="d-flex justify-content-center align-items-center flex-column">
                    <svg width="72" height="86" viewBox="0 0 72 86" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_43_26714)">
                            <path
                                d="M66.5898 83.7864H5.40964C4.5082 83.7864 3.64367 83.4283 3.00626 82.7909C2.36884 82.1535 2.01074 81.289 2.01074 80.3875V5.61179C2.01074 4.71034 2.36884 3.84582 3.00626 3.20841C3.64367 2.57099 4.5082 2.21289 5.40964 2.21289H46.1964L69.9887 26.0052V80.3875C69.9887 81.289 69.6306 82.1535 68.9932 82.7909C68.3558 83.4283 67.4912 83.7864 66.5898 83.7864Z"
                                fill="#EAEAFA" stroke="#5670CC" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M46.1963 2.21289V26.0052H69.9886" stroke="#5670CC" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M34.3004 63.3938C40.8705 63.3938 46.1966 58.0678 46.1966 51.4977C46.1966 44.9276 40.8705 39.6016 34.3004 39.6016C27.7304 39.6016 22.4043 44.9276 22.4043 51.4977C22.4043 58.0678 27.7304 63.3938 34.3004 63.3938Z"
                                stroke="#5670CC" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M42.7129 59.9102L49.5957 66.7929" stroke="#5670CC" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_43_26714">
                                <rect width="70.8235" height="86" fill="white"
                                    transform="translate(0.587891)" />
                            </clipPath>
                        </defs>
                    </svg>
                    <div class="popup-content  text-center mt-5 pt-2">
                        <h6 class="font-size-18">No data found</h6>
                        <p class="mb-0 font-size-14">We couldn't find any matching information</p>
                        <div class="d-flex align-items-center flex-wrap justify-content-center gap-4 mt-4">
                            <button class="btn btn-secondary">Back To Home</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal end -->

<script>
    window.onload = function() {
        const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get('query');
        document.getElementById('search-query').value = query;
        const envURL = document.querySelector('meta[name="base-url"]').getAttribute('content');
        const searchButton = document.getElementById('search-button');
        const searchInput = document.getElementById('search-query');

        function handleSearch() {
            const query = searchInput.value.trim();
            const searchRoute = "{{ route('search') }}";
            const searchApiUrl = `${searchRoute}?query=${encodeURIComponent(query)}`;
            if (query) {
                window.location.href = `${searchApiUrl}`;
            }
        }

        searchButton.addEventListener('click', function (e) {
            e.preventDefault();
            handleSearch();
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleSearch();
            }
        });

        $('.notification_list').on('click', function() {
            notificationList();
            notification_count()
        });
    };

    document.getElementById('logout-btn').addEventListener('click', function() {
        const button = this;
        const spinner = button.querySelector('.spinner-border');
        const text = button.querySelector('.logout-text');
        const loader = document.querySelector('.loader.simple-loader');

        // Show spinner in button
        spinner.classList.remove('d-none');
        text.textContent = 'Logging out...';
        button.disabled = true;

        // Show full page loader
        loader.classList.remove('d-none');

        // Make the logout request
        fetch('{{ route("user-logout") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '{{ route("frontend.index") }}';
            } else {
                throw new Error('Logout failed');
            }
        })
        .catch(error => {
            console.error('Logout error:', error);
            // Reset button state
            spinner.classList.add('d-none');
            text.textContent = 'Logout';
            button.disabled = false;
            // Hide loader
            loader.classList.add('d-none');
            // Show error message
            alert('Failed to logout. Please try again.');
        });
    });


    function notification_count() {

        var url = "{{ route('notification.counts') }}";
        $.ajax({
            type: 'get',
            url: url,
            success: function(res) {

                console.log(res);


            }
        });


    }

    function notificationList(type = '') {
        var url = "{{ route('notification.list') }}";

        $.ajax({
            type: 'get',
            url: url,
            data: {
                'type': type
            },
            success: function(res) {

                $('.notification_data').html(res.data);
                getNotificationCounts();
                if (res.type == "markas_read") {
                    notificationList();
                }
                $('.notify_count').removeClass('notification_tag').text('');
            }
        });
    }
</script>
