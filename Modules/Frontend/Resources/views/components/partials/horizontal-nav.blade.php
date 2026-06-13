<!-- Horizontal Menu Start -->
<nav id="navbar_main" class="offcanvas mobile-offcanvas nav navbar navbar-expand-xl hover-nav horizontal-nav py-xl-0">
    <div class="container-fluid p-lg-0">
        <div class="offcanvas-header">
            <div class="navbar-brand p-0">
                <!--Logo -->
                @include('frontend::components.partials.logo')

            </div>
            <button type="button" class="btn-close p-0" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <ul class="navbar-nav iq-nav-menu  list-unstyled" id="header-menu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.index') ? 'active' : '' }}" href="{{ route('frontend.index') }}">
                    <span class="item-name">{{ __('frontend.home') }}</span>
                </a>
            </li>
            @auth
                @if(!empty($sectionData['appointments']) && auth()->user()->hasRole('user'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('appointment-list') ? 'active' : '' }}"
                            href="{{ route('appointment-list') }}">
                            <span class="item-name">{{ __('frontend.my_appointments') }}</span>
                        </a>
                    </li>
                @endif
            @endauth

            @if(!empty($sectionData['categories']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('categories') ? 'active' : '' }}"
                        href="{{ route('categories') }}">
                        <span class="item-name">{{ __('frontend.categories') }}</span>
                    </a>
                </li>
            @endif

            @if(!empty($sectionData['services']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">
                        <span class="item-name">{{ __('frontend.services') }}</span>
                    </a>
                </li>
            @endif

            @if(!empty($sectionData['clinics']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clinics') ? 'active' : '' }}" href="{{ route('clinics') }}">
                        <span class="item-name">{{ __('frontend.clinics') }}</span>
                    </a>
                </li>
            @endif

            @if(!empty($sectionData['doctors']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('doctors') ? 'active' : '' }}" href="{{ route('doctors') }}">
                        <span class="item-name">{{ __('frontend.doctors') }}</span>
                    </a>
                </li>
            @endif
            @auth
                @if(!empty($sectionData['incidence']) && auth()->user()->hasRole('user'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('incidence.index') ? 'active' : '' }}" href="{{ route('incidence.index') }}">
                            <span class="item-name">{{ __('frontend.incidence') }}</span>
                        </a>
                    </li>
                @endif
            @endauth

            {{-- <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('contact-us') ? 'active' : '' }}" href="{{ route('contact-us') }}">
                    <span class="item-name">Contact</span>
                </a>
            </li> --}}

        </ul>
    </div>
    <!-- container-fluid.// -->
</nav>
<!-- Horizontal Menu End -->
