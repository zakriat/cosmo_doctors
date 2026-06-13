<!-- Appointment Details Modal -->
<div class="modal fade" id="appointmentDetailsModal" tabindex="-1" aria-labelledby="appointmentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentDetailsModalLabel">
                    <i class="ph ph-calendar-check me-2"></i>Appointment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="appointmentDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading appointment details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.detail-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
}

.detail-card h6 {
    color: #333;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.detail-row {
    display: flex;
    padding: 8px 0;
    border-bottom: 1px solid #f5f5f5;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #666;
    min-width: 180px;
}

.detail-value {
    color: #333;
    flex: 1;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.audio-player {
    width: 100%;
    margin: 10px 0;
}

.document-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    margin-bottom: 10px;
    background: #f9f9f9;
}

.document-icon {
    font-size: 24px;
    margin-right: 12px;
    color: #666;
}

.document-info {
    flex: 1;
}

.document-name {
    font-weight: 600;
    color: #333;
}

.document-size {
    font-size: 12px;
    color: #999;
}

.medical-history-text {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    border-left: 4px solid #007bff;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.no-data {
    text-align: center;
    padding: 20px;
    color: #999;
    font-style: italic;
}
</style>
