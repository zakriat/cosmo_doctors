<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ language_direction() }}" class="theme-fs-md"
    data-bs-theme-color={{ getCustomizationSetting('theme_color') }}>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ asset(setting('logo')) }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset(setting('favicon')) }}">
    <meta name="keyword" content="{{ setting('meta_keyword') }}">
    <meta name="description" content="{{ setting('meta_description') }}">
    <meta name="setting_options" content="{{ setting('customization_json') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        (function() {
            const savedTheme = localStorage.getItem('data-bs-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>


    <!-- Shortcut Icon -->
    <link rel="shortcut icon" href="{{ asset(setting('favicon')) }}">
    <link rel="icon" type="image/ico" href="{{ asset(setting('favicon')) }}" />

    <!-- CSRF Token -->
    <meta name="app_name" content="{{ app_name() }}">

    <meta name="data_table_limit" content="{{ setting('data_table_limit') }}">


    <meta name="auth_user_roles" content="{{ auth()->user()->roles->pluck('name') }}">

    <meta name="baseUrl" content="{{ url('/') }}" />


    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ mix('css/icon.min.css') }}">
    @if ($isNoUISlider ?? '')
        <!-- NoUI Slider css -->
        <link rel="stylesheet" href="{{ asset('vendor/noUiSilder/style/nouislider.min.css') }}">
    @endif

    @stack('before-styles')
    <link rel="stylesheet" href="{{ mix('css/libs.min.css') }}">
    <link rel="stylesheet" href="{{ mix('css/backend.css') }}">
    <link rel="stylesheet" href="{{ mix('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('custom-css/dashboard.css') }}">
    
 

    @if (language_direction() == 'rtl')
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('css/customizer.css') }}">


    <style>
        :root {
            <?php
            $rootColors = setting('root_colors'); // Assuming the setting() function retrieves the JSON string

            // Check if the JSON string is not empty and can be decoded
            if (!empty($rootColors) && is_string($rootColors)) {
                $colors = json_decode($rootColors, true);

                // Check if decoding was successful and the colors array is not empty
                if (json_last_error() === JSON_ERROR_NONE && is_array($colors) && count($colors) > 0) {
                    foreach ($colors as $key => $value) {
                        echo $key . ': ' . $value . '; ';
                    }
                } else {
                    echo 'Invalid JSON or empty colors array.';
                }
            }
            ?>
        }
    </style>

    <!-- Scripts -->
    @php
        $currentLang = App::currentLocale();
        $langFolderPath = base_path("lang/$currentLang");
        $filePaths = \File::files($langFolderPath);
    @endphp

    @foreach ($filePaths as $filePath)
        @php
            $fileName = pathinfo($filePath, PATHINFO_FILENAME);

            $arr = require $filePath;
            $dbLang = Modules\Language\Models\Language::getAllLang()
                ->where('language', app()->getLocale())
                ->where('file', $fileName)
                ->pluck('value', 'key')
                ->toArray();
            if (count($dbLang) > 0) {
                $arr = array_merge($arr, $dbLang);
            }
        @endphp
        <script>
            window.localMessagesUpdate = {
                ...window.localMessagesUpdate,
                "{{ $fileName }}": @json($arr)
            }
        </script>
    @endforeach
    @php
        $role = !empty(auth()->user()) ? auth()->user()->getRoleNames() : null;
        $id = !empty(auth()->user()) ? auth()->user()->id : null;
        $permissions = auth()->user()->getAllPermissions()->pluck('name')->toArray();
    @endphp
    <script>
        window.auth_role = @json($role);
        window.id = @json($id);
        window.auth_permissions = @json($permissions)
    </script>
    @if (setting('is_one_signal_notification') == 1)
        <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
        <script>
            window.OneSignal = window.OneSignal || [];
            OneSignal.push(function() {
                OneSignal.init({
                    appId: "{{ setting('onesignal_app_id') }}",
                    safari_web_id: "web.onesignal.auto.3cbb98e8-d926-4cfe-89ae-1bc86ff7cf70",
                    notifyButton: {
                        enable: true,
                    },
                    subdomainName: "apps-iqonic",
                });
                window.OneSignal.getUserId(function(userId) {
                    if (userId !== '{{ auth()->user()->web_player_id ?? '' }}' &&
                        '{{ auth()->user()->web_player_id ?? 'undefined' }}' !== 'undefined') {
                        fetch("{{ route('backend.update-player-id') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    player_id: userId
                                })
                            })
                            .then((res) => res.json())
                            .then((data) => console.log(data))
                    }
                })
            });
        </script>
    @endif
    <!-- Google Font -->

    <link rel="stylesheet" href="{{ asset('iconly/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('phosphor-icons/style.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&family=Jaldi:wght@400;700&display=swap"
        rel="stylesheet">

    @stack('after-styles')

    <x-google-analytics />

    <style>
        {!! setting('custom_css_block') !!}
    </style>
</head>

<body class="{{ !empty(getCustomizationSetting('card_style')) ? getCustomizationSetting('card_style') : '' }}">
    <!-- Loader Start -->
    <div id="loading">
        <x-partials._body_loader />
    </div>
    <!-- Loader End -->
    <!-- Sidebar -->

    @include('backend.includes.sidebar')

    <!-- /Sidebar -->
    <div class="main-content wrapper vh-100">

        <div class="position-relative
{{ !isset($isBanner) ? 'iq-banner' : '' }} default">
            <!-- Header -->
            @include('backend.includes.header')
            <!-- /Header -->
            @if (!isset($isBanner))
                <!-- Header Banner Start-->

                @include('components.partials.sub-header')

                <!-- Header Banner End-->
            @endif
        </div>

        <div class="conatiner-fluid content-inner pb-0" id="page_layout">
            <!-- Main content block -->
            @yield('content')
            <!-- / Main content block -->
            @if (isset($export_import) && $export_import)
                <div data-render="import-export">
                    <export-modal export-doctor-id="{{ $export_doctor_id ?? '' }}" export-url="{{ $export_url }}"
                        :module-column-prop="{{ json_encode($export_columns) }}"
                        module-name="{{ $module_name }}"></export-modal>
                    <import-modal import-url="{{ $import_url ?? '' }}" module-name="{{ $module_name ?? '' }}"
                        :module-column-prop="{{ json_encode($import_columns ?? []) }}">
                    </import-modal>
                </div>
            @endif
        </div>

        <!-- Footer block -->
        @include('backend.includes.footer')
        <!-- / Footer block -->

    </div>

    <!-- Modal -->
    <div class="modal fade" data-iq-modal="renderer" id="renderModal" tabindex="-1" aria-labelledby="renderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" data-iq-modal="content">
                <div class="modal-header">
                    <h5 class="modal-title" data-iq-modal="title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" data-iq-modal="body">
                    <p>Modal body text goes here.</p>
                </div>
            </div>
        </div>
    </div>

    <div data-render="global-booking">
        @include('appointment::backend.appointment.global-appoitment')
    </div>

    @php
        $multiVendorEnabled = multiVendor();
        $bodyChartEnabled = bodychart();
        $soapEnabled = soap();
        $telemedSettingEnabled = telemedSetting();
        $encounter = encounter();
        $setting = App\Models\Setting::where('name', 'date_formate')->first();
        $dateformate = $setting ? $setting->val : 'Y-m-d';
        $setting = App\Models\Setting::where('name', 'time_formate')->first();
        $timeformate = $setting ? $setting->val : 'h:i A';
        $baseUrl = env('APP_URL');
    @endphp


    @stack('before-scripts')
    @if ($isNoUISlider ?? '')
        <!-- NoUI Slider Script -->
        <script src="{{ asset('vendor/noUiSilder/script/nouislider.min.js') }}"></script>
    @endif
    <script src="{{ mix('js/backend.js') }}"></script>
    <script src="{{ mix('js/booking-form.min.js') }}"></script>
    {{-- <script src="{{ mix('js/iqonic-script/setting.min.js') }}"></script> --}}
    <script src="{{ asset('js/iqonic-script/utility.js') }}"></script>
    {{-- <script src="{{ asset('js/setting-init.js') }}"></script> --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/vue.js') }}"></script>
    <script src="{{ asset('laravel-js/modal-view.js') }}" defer></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const currencyFormat = (amount) => {
            
            const DEFAULT_CURRENCY = JSON.parse(@json(json_encode(Currency::getDefaultCurrency(true))))
            const noOfDecimal = DEFAULT_CURRENCY.no_of_decimal
            const decimalSeparator = DEFAULT_CURRENCY.decimal_separator
            const thousandSeparator = DEFAULT_CURRENCY.thousand_separator
            const currencyPosition = DEFAULT_CURRENCY.currency_position
            const currencySymbol = DEFAULT_CURRENCY.currency_symbol
            return formatCurrency(amount, noOfDecimal, decimalSeparator, thousandSeparator, currencyPosition,
                currencySymbol)
        }
        window.currencyFormat = currencyFormat
        window.defaultCurrencySymbol = @json(Currency::defaultSymbol())
    </script>
    <script>
        window.bodyChartEnabled = @json($bodyChartEnabled);
        window.soapEnabled = @json($soapEnabled);
        window.telemedSettingEnabled = @json($telemedSettingEnabled);
        window.encounter = @json($encounter);
        window.base_url = @json($baseUrl);
    </script>

    <script>
        window.auth_profile_image = @json(default_user_avatar());
    </script>

    <script>
        window.multiVendorEnabled = @json($multiVendorEnabled);
        window.dateformate = @json($dateformate);
        window.timeformate = @json($timeformate);
    </script>
    <script src="{{ mix('js/import-export.min.js') }}"></script>
    @if (isset($assets) && (in_array('textarea', $assets) || in_array('editor', $assets)))
        <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
        {{-- <script src="{{ asset('vendor/tinymce/js/tinymce/jquery.tinymce.min.js') }}"></script> --}}
    @endif

    @stack('after-scripts')
    <!-- / Scripts -->
    <script>
        $('.notification_list').on('click', function() {
            notificationList();
        });

        $(document).on('click', '.notification_data', function(event) {
            event.stopPropagation();
        })

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

        function setNotification(count) {
            if (Number(count) >= 100) {
                $('.notify_count').text('99+');
            }
        }

        function getNotificationCounts() {
            var url = "{{ route('notification.counts') }}";

            $.ajax({
                type: 'get',
                url: url,
                success: function(res) {
                    if (res.counts > 0) {
                        $('.notify_count').addClass('notification_tag').text(res.counts);
                        setNotification(res.counts);
                        $('.notification_list span.dots').addClass('d-none')
                        $('.notify_count').removeClass('d-none')
                    } else {
                        $('.notify_count').addClass('d-none')
                        $('.notification_list span.dots').removeClass('d-none')
                    }

                    if (res.counts <= 0 && res.unread_total_count > 0) {
                        $('.notification_list span.dots').removeClass('d-none')
                    } else {
                        $('.notification_list span.dots').addClass('d-none')
                    }
                }
            });
        }

        getNotificationCounts();
    </script>

    <script>
        {!! setting('custom_js_block') !!}
        @if (\Session::get('error'))
            Swal.fire({
                title: 'Error',
                text: '{{ session()->get('error') }}',
                icon: "error",
                showClass: {
                    popup: 'animate__animated animate__zoomIn'
                },
                hideClass: {
                    popup: 'animate__animated animate__zoomOut'
                }
            })
        @endif

        // dark and light mode code
        const theme_mode = localStorage.getItem('data-bs-theme')
        const element = document.querySelector('html');
        if (theme_mode === null) {
            element.setAttribute('data-bs-theme', 'light')
        } else {
            document.documentElement.setAttribute('data-bs-theme', theme_mode)
        }

        // Force full page reload on language change for all admin pages
        $(document).on('click', 'a[href*="language/"]', function (e) {
            e.preventDefault();
            window.location.href = $(this).attr('href');
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const snackbar = document.getElementById('snackbar');
            if (snackbar) {
                setTimeout(() => {
                    snackbar.style.display = 'none';
                }, 5000); // Hide after 5 seconds
            }
        });

        function dismissSnackbar(event) {
            event.preventDefault();
            const snackbar = document.getElementById('snackbar');
            if (snackbar) {
                snackbar.style.display = 'none';
            }
        }

    </script>
    <style>
        .snackbar {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1050;
            background-color: #333;
            color: white;
            padding: 15px 30px;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .snackbar .dismiss-link {
            cursor: pointer;
        }
    </style>


</body>

</html>
