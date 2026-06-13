/**
 * Enhanced Medical Transcription with Gemini AI
 * Handles dual-view transcription, color coding, and user interactions
 */

class EnhancedMedicalTranscription {
    constructor() {
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.currentTranscriptionId = null;
        this.recordingStartTime = null;
        this.recordingTimer = null;
        this.isRecording = false;

        // Multiple audio files support
        this.audioQueue = []; // Array of {id, blob, url, name, transcription, status}
        this.nextAudioId = 1;

        this.init();
    }

    init() {
        this.bindEvents();
        this.setupCSRFToken();
    }

    setupCSRFToken() {
        // Ensure CSRF token is available for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    bindEvents() {
        // Recording controls
        $('#record-audio-btn').on('click', () => this.startRecording());
        $('#stop-recording-btn').on('click', () => this.stopRecording());
        $('#cancel-recording-btn').on('click', () => this.cancelRecording());

        // Multiple audio file upload
        $('#upload-audio-btn').on('click', () => $('#audio-file-input').click());
        $('#audio-file-input').on('change', (e) => this.handleFileUpload(e));

        // Transcription controls
        $('#transcribe-btn').on('click', () => this.addRecordingToQueue());
        $('#transcribe-all-btn').on('click', () => this.transcribeAllInQueue());
        $('#delete-recording-btn').on('click', () => this.deleteRecording());
        $('#clear-queue-btn').on('click', () => this.clearQueue());
        $('#add-all-to-notes-btn').on('click', () => this.addAllTranscriptionsToNotes());

        // Copy and use buttons
        $('#copy-original-btn').on('click', () => this.copyToMainTextarea('original'));
        $('#copy-medical-btn').on('click', () => this.copyToMainTextarea('medical'));
        $('#use-medical-btn').on('click', () => this.useVersion('medical'));
        $('#use-combined-btn').on('click', () => this.useCombinedVersion());

        // Collapse/expand
        $('#collapse-cards-btn').on('click', () => this.toggleCardsVisibility());

        // Edit detection
        $('#original-text, #medical-text').on('input', () => this.handleTextEdit());
    }

    async startRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    sampleRate: 16000
                }
            });

            this.mediaRecorder = new MediaRecorder(stream, {
                mimeType: 'audio/webm;codecs=opus'
            });

            this.audioChunks = [];
            this.isRecording = true;
            this.recordingStartTime = Date.now();

            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    this.audioChunks.push(event.data);
                }
            };

            this.mediaRecorder.onstop = () => {
                this.handleRecordingComplete();
            };

            this.mediaRecorder.start(1000); // Collect data every second
            this.startRecordingTimer();
            this.updateRecordingUI(true);

        } catch (error) {
            console.error('Error accessing microphone:', error);
            this.showError('Could not access microphone. Please check permissions and try again.');
        }
    }

    stopRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
            this.stopRecordingTimer();
            this.isRecording = false;
        }
    }

    cancelRecording() {
        this.stopRecording();
        this.audioChunks = [];
        this.updateRecordingUI(false);
        this.hideAudioPlayer();
        this.showInfo('Recording cancelled.');
    }

    handleRecordingComplete() {
        if (this.audioChunks.length === 0) {
            this.showError('No audio data recorded. Please try again.');
            return;
        }

        const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
        const audioUrl = URL.createObjectURL(audioBlob);
        const recordingName = `Recording ${this.nextAudioId}`;

        this.showAudioPlayer(audioUrl, audioBlob, recordingName);
        this.updateRecordingUI(false);
    }

    startRecordingTimer() {
        this.recordingTimer = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.recordingStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
            const seconds = (elapsed % 60).toString().padStart(2, '0');
            $('#recording-timer').text(`${minutes}:${seconds}`);
        }, 1000);
    }

    stopRecordingTimer() {
        if (this.recordingTimer) {
            clearInterval(this.recordingTimer);
            this.recordingTimer = null;
        }
    }

    updateRecordingUI(isRecording) {
        if (isRecording) {
            $('#record-audio-btn').addClass('d-none');
            $('#stop-recording-btn, #cancel-recording-btn, #recording-timer').removeClass('d-none');
        } else {
            $('#record-audio-btn').removeClass('d-none');
            $('#stop-recording-btn, #cancel-recording-btn, #recording-timer').addClass('d-none');
            $('#recording-timer').text('00:00');
        }
    }

    showAudioPlayer(audioUrl, audioBlob, name) {
        const audioPlayer = $('#audio-player')[0];
        audioPlayer.src = audioUrl;

        $('#audio-player-container').removeClass('d-none').addClass('fade-in');
        $('#audio-name-display').text(name);
        $('#transcribe-btn').removeClass('d-none')
            .data('audio-blob', audioBlob)
            .data('audio-name', name)
            .html('<i class="ph ph-plus-circle"></i> Add to Queue');
    }

    hideAudioPlayer() {
        $('#audio-player-container').addClass('d-none');
        $('#transcribe-btn').addClass('d-none').removeData('audio-blob');
    }

    deleteRecording() {
        this.hideAudioPlayer();
        this.hideTranscriptionCards();
        this.audioChunks = [];
        this.showInfo('Recording deleted.');
    }

    /**
     * Handle file upload from input
     */
    handleFileUpload(event) {
        const files = event.target.files;
        if (!files || files.length === 0) return;

        Array.from(files).forEach(file => {
            // Validate file type
            if (!file.type.startsWith('audio/')) {
                this.showError(`${file.name} is not an audio file.`);
                return;
            }

            // Validate file size (25MB limit)
            if (file.size > 25 * 1024 * 1024) {
                this.showError(`${file.name} exceeds 25MB limit.`);
                return;
            }

            const audioUrl = URL.createObjectURL(file);
            this.addToQueue(file, audioUrl, file.name);
        });

        // Clear input
        event.target.value = '';
        this.showSuccess(`${files.length} file(s) added to queue.`);
    }

    /**
     * Add current recording to queue
     */
    addRecordingToQueue() {
        const audioBlob = $('#transcribe-btn').data('audio-blob');
        const audioName = $('#transcribe-btn').data('audio-name');

        if (!audioBlob) {
            this.showError('No audio to add.');
            return;
        }

        const audioUrl = $('#audio-player')[0].src;
        this.addToQueue(audioBlob, audioUrl, audioName);

        this.hideAudioPlayer();
        this.audioChunks = [];
        this.showSuccess('Audio added to queue.');
    }

    /**
     * Add audio to queue
     */
    addToQueue(blob, url, name) {
        const audioItem = {
            id: this.nextAudioId++,
            blob: blob,
            url: url,
            name: name,
            transcription: null,
            status: 'pending' // pending, processing, completed, error
        };

        this.audioQueue.push(audioItem);
        this.renderQueue();
    }

    /**
     * Render audio queue UI
     */
    renderQueue() {
        const queueContainer = $('#audio-queue-container');

        if (this.audioQueue.length === 0) {
            queueContainer.addClass('d-none');
            return;
        }

        queueContainer.removeClass('d-none');

        const queueHtml = this.audioQueue.map(item => `
            <div class="audio-queue-item card mb-2" data-audio-id="${item.id}">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ph ph-file-audio fs-5 text-primary"></i>
                                <div>
                                    <div class="fw-semibold">${item.name}</div>
                                    <div class="small text-muted">
                                        ${this.getStatusBadge(item.status)}
                                        ${item.categories && item.categories.length > 0 ?
                `<span class="badge bg-info ms-2">${item.categories.length} medical terms</span>` : ''
            }
                                    </div>
                                </div>
                            </div>
                            ${item.status === 'completed' && item.transcription ? `
                                <div class="mt-2 p-2 bg-light rounded small">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong>Transcription:</strong>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary toggle-highlight-btn" 
                                                    data-audio-id="${item.id}" 
                                                    data-mode="highlighted" 
                                                    title="Toggle highlighting">
                                                <i class="ph ph-palette"></i>
                                            </button>
                                            <button class="btn btn-outline-primary copy-transcription-btn" 
                                                    data-audio-id="${item.id}" 
                                                    title="Copy to clipboard">
                                                <i class="ph ph-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="transcription-content" data-audio-id="${item.id}">
                                        ${item.highlightedTranscription || this.escapeHtml(item.transcription.substring(0, 200))}${item.transcription.length > 200 ? '...' : ''}
                                    </div>
                                    ${item.categories && item.categories.length > 0 ? `
                                        <div class="mt-2">
                                            <small class="text-muted">Medical terms found:</small>
                                            <div class="medical-terms-summary">
                                                ${this.renderMedicalTermsSummary(item.categories)}
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                            ` : ''}
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary play-audio-btn" data-audio-id="${item.id}">
                                <i class="ph ph-play"></i>
                            </button>
                            ${item.status === 'completed' ? `
                                <button class="btn btn-sm btn-outline-success add-to-notes-btn" data-audio-id="${item.id}" title="Add to notes">
                                    <i class="ph ph-plus"></i>
                                </button>
                            ` : ''}
                            <button class="btn btn-sm btn-outline-danger remove-audio-btn" data-audio-id="${item.id}">
                                <i class="ph ph-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        $('#audio-queue-list').html(queueHtml);

        // Update queue count
        $('#queue-count').text(this.audioQueue.length);

        // Show/hide action buttons
        const hasPending = this.audioQueue.some(item => item.status === 'pending');
        const hasCompleted = this.audioQueue.some(item => item.status === 'completed');

        $('#transcribe-all-btn').toggleClass('d-none', !hasPending);
        $('#add-all-to-notes-btn').toggleClass('d-none', !hasCompleted);

        // Show color legend if any completed items have categories
        const hasCategories = this.audioQueue.some(item =>
            item.status === 'completed' && item.categories && item.categories.length > 0
        );
        if (hasCategories) {
            this.showQueueColorLegend();
        }

        // Bind events
        this.bindQueueEvents();
    }

    /**
     * Render medical terms summary
     */
    renderMedicalTermsSummary(categories) {
        const categoryGroups = {};

        // Group categories by type
        categories.forEach(cat => {
            if (!categoryGroups[cat.category]) {
                categoryGroups[cat.category] = [];
            }
            categoryGroups[cat.category].push(cat);
        });

        // Render grouped terms
        return Object.entries(categoryGroups).map(([category, terms]) => {
            const categoryClass = `medical-category-${category}`;
            const termsList = terms.map(term => term.text).join(', ');
            return `<span class="${categoryClass} medical-term-badge" title="${category}: ${termsList}">${category} (${terms.length})</span>`;
        }).join(' ');
    }

    /**
     * Bind queue-specific events
     */
    bindQueueEvents() {
        // Play audio
        $('.play-audio-btn').off('click').on('click', (e) => {
            const audioId = $(e.currentTarget).data('audio-id');
            this.playAudioFromQueue(audioId);
        });

        // Remove from queue
        $('.remove-audio-btn').off('click').on('click', (e) => {
            const audioId = $(e.currentTarget).data('audio-id');
            this.removeFromQueue(audioId);
        });

        // Toggle highlighting
        $('.toggle-highlight-btn').off('click').on('click', (e) => {
            const audioId = $(e.currentTarget).data('audio-id');
            this.toggleHighlighting(audioId);
        });

        // Copy transcription
        $('.copy-transcription-btn').off('click').on('click', (e) => {
            const audioId = $(e.currentTarget).data('audio-id');
            this.copyTranscriptionToClipboard(audioId);
        });

        // Add individual item to notes
        $('.add-to-notes-btn').off('click').on('click', (e) => {
            const audioId = $(e.currentTarget).data('audio-id');
            this.addSingleTranscriptionToNotes(audioId);
        });
    }

    /**
     * Toggle between highlighted and plain text
     */
    toggleHighlighting(audioId) {
        const item = this.audioQueue.find(a => a.id === audioId);
        if (!item || !item.transcription) return;

        const contentDiv = $(`.transcription-content[data-audio-id="${audioId}"]`);
        const toggleBtn = $(`.toggle-highlight-btn[data-audio-id="${audioId}"]`);
        const currentMode = toggleBtn.data('mode');

        if (currentMode === 'highlighted') {
            // Switch to plain text
            contentDiv.html(this.escapeHtml(item.transcription));
            toggleBtn.data('mode', 'plain')
                .removeClass('btn-outline-secondary')
                .addClass('btn-outline-primary')
                .attr('title', 'Show highlighting');
        } else {
            // Switch to highlighted text
            contentDiv.html(item.highlightedTranscription || this.escapeHtml(item.transcription));
            toggleBtn.data('mode', 'highlighted')
                .removeClass('btn-outline-primary')
                .addClass('btn-outline-secondary')
                .attr('title', 'Hide highlighting');
        }
    }

    /**
     * Copy transcription to clipboard
     */
    async copyTranscriptionToClipboard(audioId) {
        const item = this.audioQueue.find(a => a.id === audioId);
        if (!item || !item.transcription) return;

        try {
            await navigator.clipboard.writeText(item.transcription);
            this.showSuccess(`Transcription copied to clipboard!`);
        } catch (error) {
            console.error('Copy failed:', error);
            this.showError('Failed to copy to clipboard');
        }
    }

    /**
     * Add single transcription to notes
     */
    addSingleTranscriptionToNotes(audioId) {
        const item = this.audioQueue.find(a => a.id === audioId);
        if (!item || !item.transcription) return;

        const textarea = $('#appointment_extra_info');
        const currentText = textarea.val().trim();

        // Format with medical categories if available
        let formattedText = `[${item.name}]\n`;

        if (item.categories && item.categories.length > 0) {
            // Add category summary
            const categoryGroups = {};
            item.categories.forEach(cat => {
                if (!categoryGroups[cat.category]) {
                    categoryGroups[cat.category] = [];
                }
                categoryGroups[cat.category].push(cat.text);
            });

            formattedText += `Medical Terms Found:\n`;
            Object.entries(categoryGroups).forEach(([category, terms]) => {
                formattedText += `- ${category.toUpperCase()}: ${terms.join(', ')}\n`;
            });
            formattedText += `\nTranscription:\n`;
        }

        formattedText += item.transcription;

        if (currentText) {
            textarea.val(currentText + '\n\n' + formattedText);
        } else {
            textarea.val(formattedText);
        }

        // Visual feedback
        textarea.addClass('highlight-success');
        setTimeout(() => textarea.removeClass('highlight-success'), 2000);

        this.showSuccess(`${item.name} added to notes with medical analysis!`);
    }

    /**
     * Show color legend for queue
     */
    showQueueColorLegend() {
        // Check if legend already exists
        if ($('#queue-color-legend').length > 0) return;

        // Get all unique categories from completed items
        const allCategories = new Set();
        this.audioQueue.forEach(item => {
            if (item.categories) {
                item.categories.forEach(cat => allCategories.add(cat.category));
            }
        });

        if (allCategories.size === 0) return;

        const legendHtml = `
            <div id="queue-color-legend" class="mt-3 p-3 bg-light rounded">
                <h6 class="mb-2"><i class="ph ph-palette me-2"></i>Medical Terms Legend</h6>
                <div class="d-flex flex-wrap gap-2">
                    ${Array.from(allCategories).map(category =>
            `<span class="medical-category-${category} medical-term-badge">${category}</span>`
        ).join('')}
                </div>
            </div>
        `;

        $('#audio-queue-container').append(legendHtml);
    }
    getStatusBadge(status) {
        const badges = {
            pending: '<span class="badge bg-secondary">Pending</span>',
            processing: '<span class="badge bg-warning"><i class="ph ph-spinner ph-spin"></i> Processing</span>',
            completed: '<span class="badge bg-success"><i class="ph ph-check"></i> Completed</span>',
            error: '<span class="badge bg-danger"><i class="ph ph-x"></i> Error</span>'
        };
        return badges[status] || badges.pending;
    }

    /**
     * Play audio from queue
     */
    playAudioFromQueue(audioId) {
        const item = this.audioQueue.find(a => a.id === audioId);
        if (!item) return;

        const audioPlayer = $('#audio-player')[0];
        audioPlayer.src = item.url;
        audioPlayer.play();

        this.showInfo(`Playing: ${item.name}`);
    }

    /**
     * Remove audio from queue
     */
    removeFromQueue(audioId) {
        const index = this.audioQueue.findIndex(a => a.id === audioId);
        if (index === -1) return;

        // Revoke object URL to free memory
        URL.revokeObjectURL(this.audioQueue[index].url);

        this.audioQueue.splice(index, 1);
        this.renderQueue();
        this.updateHiddenAudioIdsField(); // Update hidden field after removal
        this.showInfo('Audio removed from queue.');
    }

    /**
     * Clear entire queue
     */
    clearQueue() {
        if (this.audioQueue.length === 0) return;

        if (!confirm(`Clear all ${this.audioQueue.length} audio file(s) from queue?`)) {
            return;
        }

        // Revoke all object URLs
        this.audioQueue.forEach(item => URL.revokeObjectURL(item.url));

        this.audioQueue = [];
        this.renderQueue();
        this.updateHiddenAudioIdsField(); // Update hidden field after clearing
        this.showInfo('Queue cleared.');
    }

    /**
     * Transcribe all pending audio files in queue
     */
    async transcribeAllInQueue() {
        const pendingItems = this.audioQueue.filter(item => item.status === 'pending');

        if (pendingItems.length === 0) {
            this.showInfo('No pending audio files to transcribe.');
            return;
        }

        this.showInfo(`Transcribing ${pendingItems.length} audio file(s)...`);

        for (const item of pendingItems) {
            await this.transcribeSingleAudio(item);
        }

        this.showSuccess('All audio files transcribed!');
    }

    /**
     * Transcribe a single audio item
     */
    async transcribeSingleAudio(item) {
        item.status = 'processing';
        this.renderQueue();

        const formData = new FormData();
        formData.append('audio', item.blob, item.name);

        try {
            const response = await $.ajax({
                url: '/transcribe-audio-enhanced',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 60000
            });

            if (response.success) {
                item.status = 'completed';
                item.transcription = response.combined_text || response.original_text;
                item.transcriptionData = response;
                
                // ✅ Store the database transcription ID
                item.transcriptionId = response.transcription_id || response.audio_id;

                // ✅ NEW: Apply highlighting to queue items
                item.categories = response.categories || [];
                item.highlightedTranscription = this.applyColorCoding(
                    item.transcription,
                    item.categories
                );

                // Store category colors for legend
                item.categoryColors = response.category_colors || {};

                console.log(`✅ Medical entities found for ${item.name}:`, item.categories.length, 'ID:', item.transcriptionId);
                
                // ✅ Update hidden field with all completed audio IDs
                this.updateHiddenAudioIdsField();
            } else {
                item.status = 'error';
                item.error = response.message || 'Transcription failed';
            }

        } catch (error) {
            console.error('Transcription error:', error);
            item.status = 'error';
            item.error = error.responseJSON?.message || 'Network error';
        }

        this.renderQueue();
    }

    /**
     * Add all completed transcriptions to notes
     */
    addAllTranscriptionsToNotes() {
        const completedItems = this.audioQueue.filter(item => item.status === 'completed');

        if (completedItems.length === 0) {
            this.showInfo('No completed transcriptions to add.');
            return;
        }

        const combinedText = completedItems.map((item, index) => {
            let formattedText = `[${item.name}]`;

            // Add medical analysis if available
            if (item.categories && item.categories.length > 0) {
                const categoryGroups = {};
                item.categories.forEach(cat => {
                    if (!categoryGroups[cat.category]) {
                        categoryGroups[cat.category] = [];
                    }
                    categoryGroups[cat.category].push(cat.text);
                });

                formattedText += `\nMedical Terms Found:`;
                Object.entries(categoryGroups).forEach(([category, terms]) => {
                    formattedText += `\n- ${category.toUpperCase()}: ${terms.join(', ')}`;
                });
                formattedText += `\n\nTranscription:`;
            }

            formattedText += `\n${item.transcription}`;
            return formattedText;
        }).join('\n\n---\n\n');

        const textarea = $('#appointment_extra_info');
        const currentText = textarea.val().trim();

        if (currentText) {
            textarea.val(currentText + '\n\n' + combinedText);
        } else {
            textarea.val(combinedText);
        }

        // Visual feedback
        textarea.addClass('highlight-success');
        setTimeout(() => textarea.removeClass('highlight-success'), 2000);

        this.showSuccess(`${completedItems.length} transcription(s) with medical analysis added to notes!`);

        // Clear queue after adding
        if (confirm('Clear queue after adding to notes?')) {
            this.clearQueue();
        }
    }

    async transcribeAudio() {
        const audioBlob = $('#transcribe-btn').data('audio-blob');
        if (!audioBlob) {
            this.showError('No audio to transcribe.');
            return;
        }

        const formData = new FormData();
        formData.append('audio', audioBlob, 'recording.webm');

        this.showTranscriptionStatus(true);
        this.disableTranscribeButton(true);

        try {
            const response = await $.ajax({
                url: '/transcribe-audio-enhanced',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 60000 // 60 second timeout
            });

            if (response.success) {
                this.handleTranscriptionSuccess(response);
            } else {
                this.handleTranscriptionError(response.message || 'Transcription failed');
            }

        } catch (error) {
            console.error('Transcription error:', error);
            this.handleTranscriptionError(
                error.responseJSON?.message || 'Network error during transcription'
            );
        } finally {
            this.showTranscriptionStatus(false);
            this.disableTranscribeButton(false);
        }
    }

    handleTranscriptionSuccess(response) {
        // Store transcription data
        this.currentTranscriptionId = response.transcription_id;

        // Display original text
        $('#original-text').text(response.original_text);

        // Display medical text with color coding
        const medicalHtml = this.applyColorCoding(
            response.medical_text,
            response.categories || []
        );
        $('#medical-text').html(medicalHtml);

        // Show status badges
        if (response.gemini_fallback_used) {
            $('#fallback-status').removeClass('d-none');
            $('#gemini-status').addClass('d-none');
        } else {
            $('#gemini-status').removeClass('d-none');
            $('#fallback-status').addClass('d-none');
        }

        // Show transcription cards and legend
        this.showTranscriptionCards();
        if (response.categories && response.categories.length > 0) {
            this.showColorLegend();
        }

        // Auto-populate main textarea with combined text
        this.populateMainTextarea(response.combined_text);

        // Show success message
        const message = response.gemini_fallback_used
            ? 'Transcription completed (basic mode - AI enhancement temporarily unavailable)'
            : 'Transcription completed with AI medical enhancement!';

        this.showSuccess(message);

        // Log processing times for debugging
        console.log('Processing times:', response.processing_times);
    }

    handleTranscriptionError(message) {
        this.showError(`Transcription failed: ${message}`);
    }

    applyColorCoding(text, categories) {
        if (!categories || categories.length === 0) {
            return this.escapeHtml(text);
        }

        let coloredText = this.escapeHtml(text);

        // Sort categories by start position (descending) to avoid position shifts
        const sortedCategories = [...categories].sort((a, b) => b.start_pos - a.start_pos);

        sortedCategories.forEach(category => {
            const categoryClass = `medical-category-${category.category}`;
            const tooltip = `${category.category} (confidence: ${Math.round(category.confidence * 100)}%)`;

            const before = coloredText.substring(0, category.start_pos);
            const highlighted = `<span class="${categoryClass} medical-term-tooltip" data-tooltip="${tooltip}" title="${tooltip}">${category.text}</span>`;
            const after = coloredText.substring(category.end_pos);

            coloredText = before + highlighted + after;
        });

        return coloredText;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showTranscriptionCards() {
        $('#transcription-cards').removeClass('d-none').addClass('fade-in');
    }

    hideTranscriptionCards() {
        $('#transcription-cards').addClass('d-none');
        $('#color-legend').addClass('d-none');
    }

    showColorLegend() {
        $('#color-legend').removeClass('d-none').addClass('fade-in');
    }

    toggleCardsVisibility() {
        const cards = $('#transcription-cards');
        const button = $('#collapse-cards-btn');

        if (cards.hasClass('d-none')) {
            cards.removeClass('d-none').addClass('fade-in');
            button.html('<i class="ph ph-caret-up"></i> Hide Details');
        } else {
            cards.addClass('d-none');
            button.html('<i class="ph ph-caret-down"></i> Show Details');
        }
    }

    copyToMainTextarea(version) {
        let content = '';

        if (version === 'original') {
            content = $('#original-text').text();
        } else if (version === 'medical') {
            content = $('#medical-text').text(); // Get plain text, not HTML
        }

        if (content) {
            this.populateMainTextarea(content);
            this.showSuccess(`${version === 'original' ? 'Original' : 'Medical'} text copied to main field.`);
        }
    }

    useVersion(version) {
        this.copyToMainTextarea(version);
        this.hideTranscriptionCards();
    }

    useCombinedVersion() {
        const originalText = $('#original-text').text();
        const medicalText = $('#medical-text').text();

        if (originalText && medicalText) {
            const combinedText = `Original: "${originalText}"\n\nMedical: "${medicalText}"`;
            this.populateMainTextarea(combinedText);
            this.showSuccess('Both versions combined in main field.');
        }
    }

    populateMainTextarea(content) {
        const textarea = $('#appointment_extra_info');
        const currentText = textarea.val().trim();

        // Strip HTML tags for plain textarea
        const plainText = $('<div>').html(content).text();

        if (currentText) {
            if (confirm('Add to existing text or replace it?')) {
                textarea.val(currentText + '\n\n' + plainText);
            } else {
                textarea.val(plainText);
            }
        } else {
            textarea.val(plainText);
        }

        // Visual feedback
        textarea.addClass('highlight-success');
        setTimeout(() => textarea.removeClass('highlight-success'), 2000);

        // Focus and scroll
        textarea.focus();
        textarea[0].scrollTop = textarea[0].scrollHeight;
    }

    handleTextEdit() {
        // Mark as user edited when transcription text is modified
        if (this.currentTranscriptionId) {
            // Could send AJAX to mark as edited in database
            console.log('User edited transcription');
        }
    }

    showTranscriptionStatus(show) {
        if (show) {
            $('#transcription-status').removeClass('d-none').addClass('fade-in');
        } else {
            $('#transcription-status').addClass('d-none');
        }
    }

    disableTranscribeButton(disable) {
        const button = $('#transcribe-btn');
        if (disable) {
            button.prop('disabled', true)
                .html('<i class="ph ph-spinner ph-spin"></i> Transcribing...');
        } else {
            button.prop('disabled', false)
                .html('<i class="ph ph-text-aa"></i> Transcribe Audio');
        }
    }

    // Utility methods for user feedback
    showSuccess(message) {
        this.showToast(message, 'success');
    }

    showError(message) {
        this.showToast(message, 'error');
    }

    showInfo(message) {
        this.showToast(message, 'info');
    }

    showToast(message, type = 'info') {
        // Create a simple toast notification
        const toast = $(`
            <div class="toast-notification toast-${type}" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1'};
                color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460'};
                border: 1px solid ${type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb'};
                padding: 12px 20px;
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 9999;
                max-width: 300px;
                animation: slideInRight 0.3s ease;
            ">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="ph ph-${type === 'success' ? 'check-circle' : type === 'error' ? 'warning-circle' : 'info'}"></i>
                    <span>${message}</span>
                </div>
            </div>
        `);

        $('body').append(toast);

        setTimeout(() => {
            toast.fadeOut(300, () => toast.remove());
        }, 4000);
    }

    /**
     * Initialize appointment detail page specific functionality
     */
    initAppointmentDetailMode() {
        console.log('📋 Initializing appointment detail mode');

        // Toggle edit mode
        $('#toggle-edit-mode-btn').on('click', () => this.toggleEditMode());
        $('#cancel-edit-btn').on('click', () => this.cancelEdit());
        $('#save-medical-history-btn').on('click', () => this.saveMedicalHistory());
    }

    /**
     * Toggle between view and edit mode
     */
    toggleEditMode() {
        const viewSection = $('#medical-history-view');
        const editSection = $('#medical-history-edit');
        const toggleBtn = $('#toggle-edit-mode-btn');

        if (editSection.hasClass('d-none')) {
            // Enter edit mode
            viewSection.addClass('d-none');
            editSection.removeClass('d-none');
            toggleBtn.addClass('d-none');
            console.log('✏️ Edit mode activated');
        } else {
            // Exit edit mode
            editSection.addClass('d-none');
            viewSection.removeClass('d-none');
            toggleBtn.removeClass('d-none');
            console.log('👁️ View mode activated');
        }
    }

    /**
     * Cancel edit and return to view mode
     */
    cancelEdit() {
        // Reset form
        this.resetRecording();

        // Clear transcription cards
        $('#transcription-cards').addClass('d-none');
        $('#audio-player-container').addClass('d-none');

        // Return to view mode
        this.toggleEditMode();

        console.log('❌ Edit cancelled');
    }

    /**
     * Save medical history (appointment detail page only)
     */
    async saveMedicalHistory() {
        const textarea = $('#appointment_extra_info');
        const newText = textarea.val().trim();

        if (!newText) {
            this.showAlert('Please enter medical history information.', 'warning');
            return;
        }

        // Get appointment ID from URL
        const appointmentId = window.location.pathname.split('/').pop();

        // Show loading
        const saveBtn = $('#save-medical-history-btn');
        const originalHtml = saveBtn.html();
        saveBtn.prop('disabled', true);
        saveBtn.html('<i class="ph ph-spinner ph-spin"></i> Saving...');

        try {
            const response = await fetch(`/appointments/${appointmentId}/update-medical-history`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    appointment_extra_info: newText
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update view section
                const viewText = $('#medical-history-view p');
                viewText.text(data.appointment_extra_info);
                viewText.removeClass('text-muted');

                // Reset and return to view mode
                this.cancelEdit();

                // Show success message
                this.showAlert('Medical history updated successfully!', 'success');

                console.log('✅ Medical history saved');
            } else {
                this.showAlert('Failed to save: ' + (data.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Save error:', error);
            this.showAlert('Error saving medical history. Please try again.', 'error');
        } finally {
            // Restore button
            saveBtn.prop('disabled', false);
            saveBtn.html(originalHtml);
        }
    }

    /**
     * Show alert message
     */
    showAlert(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' :
            type === 'error' ? 'alert-danger' :
                type === 'warning' ? 'alert-warning' : 'alert-info';

        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                 style="z-index: 9999; min-width: 300px;">
                <i class="ph ph-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

        $('body').append(alert);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            alert.alert('close');
        }, 3000);
    }

    /**
     * Reset recording state
     */
    resetRecording() {
        if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
            this.mediaRecorder.stop();
        }

        this.audioChunks = [];
        this.isRecording = false;

        if (this.recordingTimer) {
            clearInterval(this.recordingTimer);
            this.recordingTimer = null;
        }

        // Reset UI
        $('#record-audio-btn').removeClass('d-none');
        $('#stop-recording-btn').addClass('d-none');
        $('#cancel-recording-btn').addClass('d-none');
        $('#recording-timer').addClass('d-none').text('00:00');
    }

    /**
     * Get all completed audio transcription IDs
     * This is used when submitting the appointment form
     */
    getCompletedAudioIds() {
        return this.audioQueue
            .filter(item => item.status === 'completed' && item.transcriptionId)
            .map(item => item.transcriptionId);
    }

    /**
     * Update the hidden field with current audio transcription IDs
     * This ensures the IDs are always in sync with the queue
     */
    updateHiddenAudioIdsField() {
        const audioIds = this.getCompletedAudioIds();
        const hiddenField = document.getElementById('audio_transcription_ids');
        
        if (hiddenField) {
            hiddenField.value = JSON.stringify(audioIds);
            console.log('📎 Updated hidden field with audio IDs:', audioIds);
        }
    }
}

// Initialize when document is ready
$(document).ready(function () {
    // Check if we're on the booking page OR appointment detail page
    if ($('#appointment_extra_info').length > 0) {
        window.medicalTranscription = new EnhancedMedicalTranscription();

        // Initialize appointment detail mode if on that page
        if ($('#toggle-edit-mode-btn').length) {
            window.medicalTranscription.initAppointmentDetailMode();
        }

        // Add slide-in animation CSS
        $('<style>').text(`
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            
            /* Medical Category Highlighting Styles */
            .medical-category-emergency {
                background-color: #dc3545;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(220, 53, 69, 0.3);
            }
            
            .medical-category-symptoms {
                background-color: #fd7e14;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(253, 126, 20, 0.3);
            }
            
            .medical-category-conditions {
                background-color: #0d6efd;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(13, 110, 253, 0.3);
            }
            
            .medical-category-medications {
                background-color: #198754;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(25, 135, 84, 0.3);
            }
            
            .medical-category-vitals {
                background-color: #6f42c1;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(111, 66, 193, 0.3);
            }
            
            .medical-category-anatomy {
                background-color: #20c997;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(32, 201, 151, 0.3);
            }
            
            .medical-category-procedures {
                background-color: #0dcaf0;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(13, 202, 240, 0.3);
            }
            
            .medical-category-severity {
                background-color: #ffc107;
                color: #000;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(255, 193, 7, 0.3);
            }
            
            .medical-category-duration {
                background-color: #6c757d;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(108, 117, 125, 0.3);
            }
            
            .medical-category-allergies {
                background-color: #e83e8c;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(232, 62, 140, 0.3);
            }
            
            .medical-category-family_history {
                background-color: #795548;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(121, 85, 72, 0.3);
            }
            
            .medical-category-social_history {
                background-color: #607d8b;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-weight: 500;
                box-shadow: 0 1px 3px rgba(96, 125, 139, 0.3);
            }
            
            /* Medical term badges for summary */
            .medical-term-badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 12px;
                font-size: 0.75rem;
                font-weight: 500;
                margin: 2px;
                cursor: help;
                transition: all 0.2s ease;
            }
            
            .medical-term-badge:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            }
            
            /* Tooltip styles */
            .medical-term-tooltip {
                position: relative;
                cursor: help;
            }
            
            .medical-term-tooltip:hover::after {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0,0,0,0.9);
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 0.75rem;
                white-space: nowrap;
                z-index: 1000;
                pointer-events: none;
            }
            
            /* Success highlight animation */
            .highlight-success {
                animation: highlightSuccess 2s ease;
            }
            
            @keyframes highlightSuccess {
                0% { background-color: #d4edda; }
                100% { background-color: transparent; }
            }
            
            /* Medical terms summary styling */
            .medical-terms-summary {
                margin-top: 0.5rem;
            }
            
            /* Queue color legend */
            #queue-color-legend {
                border-left: 4px solid #0d6efd;
            }
            
            #queue-color-legend h6 {
                color: #0d6efd;
                margin-bottom: 0.75rem;
            }
        `).appendTo('head');
    }
});
