@if(auth()->check() && auth()->user()->user_type === 'user' && !auth()->user()->isProfileComplete())
<div class="profile-incomplete-alert" id="profile-incomplete-alert">
    <div class="container-fluid">
        <div class="row align-items-center py-3">
            <div class="col-md-8 col-sm-12">
                <div class="d-flex align-items-center">
                    <i class="ph ph-info-circle me-3" style="font-size: 1.5rem; color: #0066cc;"></i>
                    <div>
                        <h6 class="mb-1 fw-semibold" style="color: #0066cc;">Complete Your Profile</h6>
                        <p class="mb-0 small" style="color: #004499;">
                            Help us serve you better by completing your profile information. 
                            Missing: {{ implode(', ', auth()->user()->getMissingProfileFields()) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 text-md-end text-center mt-2 mt-md-0">
                <a href="{{ route('edit-profile') }}" class="btn btn-primary btn-sm me-2">
                    <i class="ph ph-pencil-simple me-1"></i>
                    Complete Profile
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="dismissAlert()">
                    <i class="ph ph-x"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.profile-incomplete-alert {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-bottom: 3px solid #2196f3;
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.15);
    position: relative;
    z-index: 1000;
}

.profile-incomplete-alert .container-fluid {
    max-width: 1200px;
    margin: 0 auto;
}

.profile-incomplete-alert h6 {
    margin-bottom: 4px;
}

.profile-incomplete-alert .btn-primary {
    background-color: #2196f3;
    border-color: #2196f3;
    font-weight: 500;
}

.profile-incomplete-alert .btn-primary:hover {
    background-color: #1976d2;
    border-color: #1976d2;
}

.profile-incomplete-alert .btn-outline-secondary {
    border-color: #90a4ae;
    color: #546e7a;
}

.profile-incomplete-alert .btn-outline-secondary:hover {
    background-color: #546e7a;
    border-color: #546e7a;
}

@media (max-width: 768px) {
    .profile-incomplete-alert .col-sm-12 {
        text-align: center !important;
    }
    
    .profile-incomplete-alert .d-flex {
        justify-content: center;
        text-align: center;
    }
}
</style>

<script>
function dismissAlert() {
    const alert = document.getElementById('profile-incomplete-alert');
    if (alert) {
        alert.style.display = 'none';
        // Store dismissal in session storage so it stays hidden during this session
        sessionStorage.setItem('profile_alert_dismissed', 'true');
    }
}

// Check if alert was previously dismissed in this session
document.addEventListener('DOMContentLoaded', function() {
    if (sessionStorage.getItem('profile_alert_dismissed') === 'true') {
        const alert = document.getElementById('profile-incomplete-alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }
});
</script>
@endif