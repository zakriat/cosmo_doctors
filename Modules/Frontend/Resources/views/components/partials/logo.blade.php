<div class="logo-default">
    <a class="navbar-brand text-primary" href="{{ route('frontend.index') }}"> 
        <div class="logo-main">
            <div class="logo-mini d-none">
                <img src="{{ asset(setting('mini_logo')) }}" height="30" alt="{{ app_name() }}">
            </div>
            <div class="logo-normal">
                <img src="{{ asset(setting('logo')) }}" height="30" alt="{{ app_name() }}">
            </div>
            <div class="logo-dark">
                <img src="{{ asset(setting('dark_logo')) }}" height="30" alt="{{ app_name() }}">
            </div>
        </div>
    </a>
</div>