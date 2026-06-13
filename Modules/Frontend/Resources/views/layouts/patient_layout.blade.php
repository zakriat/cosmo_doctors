<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light"
    dir="{{ session()->has('dir') ? session()->get('dir') : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="base-url" content="{{ env('APP_URL') }}">
    <link rel="icon" type="image/png" href="{{ asset(setting('logo')) }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset(setting('favicon')) }}">

    <title> @yield('title') </title>

    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="author" content="{{ $author ?? '' }}">
    <meta name="data_table_limit" content="{{ setting('data_table_limit') }}">
    <meta name="baseUrl" content="{{ url('/') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&family=Kalam:wght@300;400;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('iconly/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('phosphor-icons/regular/style.css') }}">
    <link rel="stylesheet" href="{{ asset('phosphor-icons/fill/style.css') }}">
    <link rel="shortcut icon" href="{{ asset(setting('favicon')) }}">
    <link rel="icon" type="image/ico" href="{{ asset(setting('favicon')) }}" />
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/frontend/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/medical-transcription.css') }}">

    @include('frontend::components.partials.head.plugins')

    @stack('after-styles')

    <style>
        /* Patient-specific styles for minimal layout */
        .patient-dashboard-container {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .patient-content {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .patient-content {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
        }
        
        /* Remove any sidebar-related styles */
        .sidebar {
            display: none !important;
        }
        
        /* Ensure full width content */
        .patient-main-content {
            width: 100%;
            max-width: none;
        }
        
        /* Minimal footer styles - Ultra compact */
        .minimal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            margin-top: 1rem;
        }

        .minimal-footer .footer-content {
            padding: 0.75rem 0;
        }

        .minimal-footer .footer-logo img {
            max-height: 30px;
            width: auto;
        }

        .minimal-footer .contact-info {
            font-size: 0.8rem;
        }

        .minimal-footer .contact-item a {
            color: #6c757d;
            transition: color 0.2s ease;
            font-size: 0.8rem;
        }

        .minimal-footer .contact-item a:hover {
            color: var(--bs-primary);
        }

        .minimal-footer .copyright {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .minimal-footer .copyright a {
            font-weight: 500;
        }

        /* Mobile responsive - Stack vertically but keep compact */
        @media (max-width: 768px) {
            .minimal-footer .footer-content {
                padding: 0.5rem 0;
            }
            
            .minimal-footer .contact-info {
                font-size: 0.75rem;
                flex-direction: column;
                gap: 0.25rem !important;
            }
            
            .minimal-footer .contact-item {
                justify-content: center !important;
            }
            
            .minimal-footer .copyright {
                font-size: 0.7rem;
                margin-top: 0.5rem;
            }
            
            .minimal-footer .footer-logo img {
                max-height: 26px;
            }
        }

        ── Brand colours + dark text for patient pages ──


   
        
.card-header .row.align-items-center,
.card-header .row.align-items-center * {
    color: #212529 !important;
}

body, p, span, label, td, th, h1, h2, h3, h4, h5, h6, li, small {
    color: #212529 !important;
}
.text-muted, .text-secondary {
    color: #212529  !important;
}

/* Stats card labels */
.stats-card p {
    color: #212529  !important;
}

/* Card headers — row becomes col (stacked) on all sizes */
/* .card-header .row {
    flex-direction: column;
    gap: 0.5rem;
}
.card-header .row .col-md-6 {
    width: 100%;
}     */
        
:root {
    --bs-primary:     #E63732;
    --bs-primary-rgb: 230, 55, 50;
}

body {
    color: #212529 !important;
}

/* All general text dark */
p, span, label, td, th, li, small, .text-muted {
    color: #212529 !important;
}

/* .btn.bs-primary — the blood test button class */
/* .btn.bs-primary {
    background-color: #E63732 !important;
    border-color:     #E63732 !important;
    color:            #fff    !important;
} */

/* .btn.bs-primary:hover {
    background-color: #b82c28 !important;
    border-color:     #b82c28 !important;
} */

/* Standard .btn-primary too */
/* .btn-primary {
    background-color: #E63732 !important;
    border-color:     #E63732 !important;
    color:            #fff    !important;
} */

/* .btn-primary:hover {
    background-color: #b82c28 !important;
    border-color:     #b82c28 !important;
} */

/* Links
a { color: #E63732 !important; }
a:hover { color: #b82c28 !important; } */

:root {
    --bs-body-color: #212529 !important;
    --bs-body-color-rgb: 33, 37, 41 !important;
    --bs-secondary-color: #212529 !important;
    --bs-secondary-color-rgb: 33, 37, 41 !important;
}


    </style>
</head>

<body class="patient-dashboard-container">
    @include('frontend::components.patient_header')
    
    {{-- Temporarily disabled profile alert --}}
    {{-- @include('frontend::components.profile-incomplete-alert') --}}

    <main class="patient-content">
        @yield('content')
    </main>

    @php
        // Define which patient pages should show the minimal footer
        $pagesWithFooter = [
            'patient.dashboard',                    // /patient-dashboard
            'edit-profile',                        // /edit-profile  
            'appointment-list',                    // /appointment-list
            'appointment-details',                 // /appointment-details/{id}
            'encounter-list'                       // /encounter-list (triage)
        ];
        
        // Get current route name
        $currentRoute = Route::currentRouteName();
        
        // Check if current page should show footer
        $showFooter = in_array($currentRoute, $pagesWithFooter);
        
        // Also check by URL path as fallback for dynamic routes
        if (!$showFooter) {
            $currentPath = request()->path();
            $pathBasedCheck = [
                'patient-dashboard' => true,
                'edit-profile' => true,
                'appointment-list' => true,
                'appointment-details' => true,
                'encounter-list' => true
            ];
            
            foreach ($pathBasedCheck as $pathPattern => $shouldShow) {
                if (str_contains($currentPath, $pathPattern)) {
                    $showFooter = $shouldShow;
                    break;
                }
            }
        }
    @endphp

    @if($showFooter)
        @include('frontend::layouts.patient_footer')
    @endif

    @include('frontend::components.partials.scripts.plugins')

    <div id="back-to-top" style="display: none;" class="animate__animated animate__fadeIn">
        <a class="p-0 btn btn-primary btn-md position-fixed top" id="top" href="#top">
            <i class="ph ph-caret-up align-middle"></i>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ mix('modules/frontend/script.js') }}"></script>
    <script src="{{ mix('js/backend-custom.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    <script>
        const currencyFormat = (amount) => {
            const DEFAULT_CURRENCY = JSON.parse(@json(json_encode(Currency::getDefaultCurrency(true))))
            const noOfDecimal = DEFAULT_CURRENCY.no_of_decimal
            const decimalSeparator = DEFAULT_CURRENCY.decimal_separator
            const thousandSeparator = DEFAULT_CURRENCY.thousand_separator
            const currencyPosition = DEFAULT_CURRENCY.currency_position
            const currencySymbol = DEFAULT_CURRENCY.currency_symbol
            return formatCurrency(amount, noOfDecimal, decimalSeparator, thousandSeparator, currencyPosition, currencySymbol)
        }
        window.currencyFormat = currencyFormat
        window.defaultCurrencySymbol = @json(Currency::defaultSymbol())
    </script>
    
    @stack('after-scripts')
</body>

</html>