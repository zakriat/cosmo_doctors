<footer class="footer minimal-footer">
    @php
        $settings = App\Models\Setting::whereIn('name', [
            'helpline_number',
            'inquriy_email',
        ])->pluck('val', 'name');

        $helplineNumber = $settings['helpline_number'] ?? null;
        $inquriyEmail = $settings['inquriy_email'] ?? null;
    @endphp

    <div class="footer-content">
        <div class="container-fluid">
            <div class="row align-items-center py-2">
                
                <!-- Logo Section -->
                <div class="col-md-3 text-center text-md-start">
                    <a href="{{ route('frontend.index') }}" class="footer-logo">
                        <img src="{{ asset('storage/310/HoAjJNqw7fyP6Qp1mTayDn1m8L8s6HAxYFmoE5Wq.jpg') }}" height="30" alt="{{ app_name() }}">
                    </a>
                </div>
                
                <!-- Contact Info Section -->
                <div class="col-md-6 text-center">
                    <div class="contact-info d-flex align-items-center justify-content-center gap-4">
                        @if($helplineNumber)
                        <div class="contact-item d-flex align-items-center gap-1">
                            <i class="ph ph-phone text-primary" style="font-size: 14px;"></i>
                            <a href="tel:{{ $helplineNumber }}" class="text-decoration-none small">{{ $helplineNumber }}</a>
                        </div>
                        @endif
                        @if($inquriyEmail)
                        <div class="contact-item d-flex align-items-center gap-1">
                            <i class="ph ph-envelope text-primary" style="font-size: 14px;"></i>
                            <a href="mailto:{{ $inquriyEmail }}" class="text-decoration-none small">{{ $inquriyEmail }}</a>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Copyright Section -->
                <div class="col-md-3 text-center text-md-end">
                    <div class="copyright">
                        <small class="text-muted">© {{ now()->year }} <a href="{{ route('frontend.index') }}" class="text-primary text-decoration-none">{{ app_name() }}</a></small>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</footer>