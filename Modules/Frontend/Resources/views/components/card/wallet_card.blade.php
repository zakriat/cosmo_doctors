<li>
    <div class="wallet-detail section-bg rounded p-4">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="rating-meta rounded-pill bg-primary-subtle badge">
                    {{ in_array($history->activity_type, ['paid_for_appointment', 'wallet_deduction']) ? __('frontend.debit') : __('frontend.credit') }}
                </span>
                <h6 class="m-0 font-size-18">

                    {{ __(key: 'frontend.' . strtolower($history->activity_type)) }} #{{ json_decode($history->activity_data, true)['appointment_id'] ?? '' }}
                </h6>
            </div>
            <?php
            $activityData = json_decode($history['activity_data'], true); // Decode the JSON to an associative array
            $creditDebitAmount = $activityData['credit_debit_amount'] ?? null; // Access the value safely
            ?>
            <span class="{{ in_array($history->activity_type, ['paid_for_appointment', 'wallet_deduction']) ? 'text-danger' : 'text-success' }} font-size-18 fw-semibold">
                {{ Currency::format($creditDebitAmount, 2) }}
            </span>
        </div>
        <span>{{ formatDate($history->datetime) }}</span>
    </div>
</li>
