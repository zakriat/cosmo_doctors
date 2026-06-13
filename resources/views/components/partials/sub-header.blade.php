<div class="iq-navbar-header navs-bg-color pr-hide">
    <div class="container-fluid iq-container pb-0">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb gap-2 heading-font m-0">
                            @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin'))
                                <li class="breadcrumb-item"><a
                                        href="{{ route('backend.home') }}">{{ __('sidebar.home') }}</a></li>
                            @elseif(auth()->user()->hasRole('doctor'))
                                <li class="breadcrumb-item"><a
                                        href="{{ route('backend.doctor-dashboard') }}">{{ __('sidebar.home') }}</a>
                                </li>
                            @elseif(auth()->user()->hasRole('receptionist'))
                                <li class="breadcrumb-item"><a
                                        href="{{ route('backend.receptionist-dashboard') }}">{{ __('sidebar.home') }}</a>
                                </li>
                            @elseif(auth()->user()->hasRole('vendor'))
                                <li class="breadcrumb-item"><a
                                        href="{{ route('backend.vendor-dashboard') }}">{{ __('sidebar.home') }}</a>
                                </li>
                            @endif
                            <li><i class="ph ph-caret-double-right"></i></li>
                            <li class="breadcrumb-item active" aria-current="page" id="breadcrumbcustom">
                                {{ __($module_title ?? '') }}</li>
                        </ol>
                    </nav>
                    <div>
                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('vendor'))
                            <!-- <a href="javascript:void(0)" class="btn btn-secondary d-flex align-items-center gap-2"
                                create-title="{{ __('messages.create') }} {{ __('messages.appointment') }}"
                                id="appointment-button"><i class="ph ph-plus-circle align-middle"></i>
                                {{ __('messages.appointment') }}</a> -->
                                  <!-- In sub-header.blade.php -->
                                {{-- <a href="javascript:void(0)"
                                    class="btn btn-secondary d-flex align-items-center gap-2"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#global-appointment">
                                    <i class="ph ph-plus-circle align-middle"></i> {{ __('messages.appointment') }}
                                </a> --}}
                                <a href="javascript:void(0)"
                                    class="btn btn-secondary d-flex align-items-center gap-2"
                                    id="global-appointment-trigger"
                                    onclick="openGlobalAppointment()">
                                    <i class="ph ph-plus-circle align-middle"></i> {{ __('messages.appointment') }}
                                </a>
                                
                                <script>
                                function openGlobalAppointment() {
                                    console.log('Opening global appointment offcanvas');
                                    // Target the Blade template offcanvas
                                    var offcanvasElement = document.getElementById('global-appointment-offcanvas');
                                    if (offcanvasElement) {
                                        try {
                                            // Try to get existing instance or create new one
                                            var offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement, {
                                                backdrop: true,
                                                keyboard: true
                                            });
                                            offcanvas.show();
                                        } catch (error) {
                                            console.error('Bootstrap offcanvas error:', error);
                                            // Fallback: manual show
                                            $(offcanvasElement).addClass('show');
                                            $('body').addClass('offcanvas-open');
                                        }
                                    } else {
                                        console.error('Global appointment offcanvas element not found');
                                    }
                                }
                                </script>

                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
