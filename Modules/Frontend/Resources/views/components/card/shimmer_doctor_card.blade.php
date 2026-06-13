<div class="doctor-card rounded text-center placeholder-glow doctor-section-card" style="width: 300px;">
    <div class="doctor-inner-card d-block">
        <div class=" placeholder-glow">
            <div class="placeholder rounded-circle w-50" style="height: 120px;"></div>
        </div>
        <div class="doctor-content mb-3 pb-1">
            <div class="d-flex align-items-center justify-content-center gap-2 mt-4 mb-1 doctor-name">
                <div class="placeholder rounded" style="height: 20px; width: 60%;"></div>
                <div class="placeholder rounded-circle" style="height: 18px; width: 18px;"></div>
            </div>
            <div class="placeholder rounded" style="height: 16px; width: 60%;"></div>
        </div>

        <div class="d-flex align-items-center justify-content-center gap-2 mb-4 pb-1">
            <div class="d-flex align-items-center">
                @for ($i = 1; $i <= 5; $i++)
                    <div class="placeholder rounded" style="height: 16px; width: 16px; background-color: #e0e0e0;">
                    </div>
                @endfor
            </div>
            <div class="placeholder rounded" style="height: 16px; width: 30%;"></div>
        </div>

        <div class="placeholder rounded" style="height: 16px; width: 50%;"></div>
    </div>
</div>
