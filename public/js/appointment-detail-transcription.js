// Appointment Detail - Medical History Speech-to-Text
// Allows patients to update medical history after booking

(function() {
    let mediaRecorder;
    let audioChunks = [];
    let recordingStartTime;
    let timerInterval;
    let audioBlob = null;

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeEditMode();
        initializeRecording();
        initializeTranscription();
        initializeSave();
    });

    /**
     * Initialize edit mode toggle
     */
    function initializeEditMode() {
        const editBtn = document.getElementById('edit-medical-history-btn');
        const cancelBtn = document.getElementById('cancel-edit-detail-btn');
        const viewSection = document.getElementById('medical-history-view');
        const editSection = document.getElementById('medical-history-edit');

        if (editBtn) {
            editBtn.addEventListener('click', function() {
                viewSection.classList.add('d-none');
                editSection.classList.remove('d-none');
                editBtn.classList.add('d-none');
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                // Reset form
                resetForm();
                
                // Switch back to view mode
                editSection.classList.add('d-none');
                viewSection.classList.remove('d-none');
                editBtn.classList.remove('d-none');
            });
        }
    }

    /**
     * Initialize recording functionality
     */
    function initializeRecording() {
        const recordBtn = document.getElementById('record-audio-detail-btn');
        const stopBtn = document.getElementById('stop-recording-detail-btn');
        const cancelRecordingBtn = document.getElementById('cancel-recording-detail-btn');
        const deleteBtn = document.getElementById('delete-recording-detail-btn');

        if (recordBtn) {
            recordBtn.addEventListener('click', startRecording);
        }

        if (stopBtn) {
            stopBtn.addEventListener('click', stopRecording);
        }

        if (cancelRecordingBtn) {
            cancelRecordingBtn.addEventListener('click', cancelRecording);
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', deleteRecording);
        }
    }

    /**
     * Start audio recording
     */
    async function startRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];

            mediaRecorder.ondataavailable = (event) => {
                audioChunks.push(event.data);
            };

            mediaRecorder.onstop = () => {
                audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                const audioUrl = URL.createObjectURL(audioBlob);
                
                const audioPlayer = document.getElementById('audio-player-detail');
                audioPlayer.src = audioUrl;
                
                document.getElementById('audio-player-container-detail').classList.remove('d-none');
                
                // Stop all tracks
                stream.getTracks().forEach(track => track.stop());
            };

            mediaRecorder.start();
            recordingStartTime = Date.now();
            
            // Update UI
            document.getElementById('record-audio-detail-btn').classList.add('d-none');
            document.getElementById('stop-recording-detail-btn').classList.remove('d-none');
            document.getElementById('cancel-recording-detail-btn').classList.remove('d-none');
            document.getElementById('recording-timer-detail').classList.remove('d-none');
            
            // Start timer
            startTimer();
            
            console.log('🎤 Recording started');
        } catch (error) {
            console.error('Error accessing microphone:', error);
            alert('Could not access microphone. Please check permissions.');
        }
    }

    /**
     * Stop recording
     */
    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            stopTimer();
            
            // Update UI
            document.getElementById('record-audio-detail-btn').classList.remove('d-none');
            document.getElementById('stop-recording-detail-btn').classList.add('d-none');
            document.getElementById('cancel-recording-detail-btn').classList.add('d-none');
            document.getElementById('recording-timer-detail').classList.add('d-none');
            
            console.log('⏹️ Recording stopped');
        }
    }

    /**
     * Cancel recording
     */
    function cancelRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            stopTimer();
            
            // Clear audio chunks
            audioChunks = [];
            audioBlob = null;
            
            // Reset UI
            document.getElementById('record-audio-detail-btn').classList.remove('d-none');
            document.getElementById('stop-recording-detail-btn').classList.add('d-none');
            document.getElementById('cancel-recording-detail-btn').classList.add('d-none');
            document.getElementById('recording-timer-detail').classList.add('d-none');
            document.getElementById('audio-player-container-detail').classList.add('d-none');
            
            console.log('❌ Recording cancelled');
        }
    }

    /**
     * Delete recording
     */
    function deleteRecording() {
        audioBlob = null;
        audioChunks = [];
        
        document.getElementById('audio-player-container-detail').classList.add('d-none');
        document.getElementById('transcription-cards-detail').classList.add('d-none');
        
        console.log('🗑️ Recording deleted');
    }

    /**
     * Start recording timer
     */
    function startTimer() {
        const timerElement = document.getElementById('recording-timer-detail');
        
        timerInterval = setInterval(() => {
            const elapsed = Date.now() - recordingStartTime;
            const minutes = Math.floor(elapsed / 60000);
            const seconds = Math.floor((elapsed % 60000) / 1000);
            
            timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }, 1000);
    }

    /**
     * Stop recording timer
     */
    function stopTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }
    }

    /**
     * Initialize transcription functionality
     */
    function initializeTranscription() {
        const transcribeBtn = document.getElementById('transcribe-detail-btn');
        const copyOriginalBtn = document.getElementById('copy-original-detail-btn');
        const copyMedicalBtn = document.getElementById('copy-medical-detail-btn');
        const useMedicalBtn = document.getElementById('use-medical-detail-btn');
        const useCombinedBtn = document.getElementById('use-combined-detail-btn');

        if (transcribeBtn) {
            transcribeBtn.addEventListener('click', transcribeAudio);
        }

        if (copyOriginalBtn) {
            copyOriginalBtn.addEventListener('click', () => copyToTextarea('original'));
        }

        if (copyMedicalBtn) {
            copyMedicalBtn.addEventListener('click', () => copyToTextarea('medical'));
        }

        if (useMedicalBtn) {
            useMedicalBtn.addEventListener('click', () => copyToTextarea('medical'));
        }

        if (useCombinedBtn) {
            useCombinedBtn.addEventListener('click', () => copyToTextarea('combined'));
        }
    }

    /**
     * Transcribe audio using Groq API
     */
    async function transcribeAudio() {
        if (!audioBlob) {
            alert('No audio recording found. Please record audio first.');
            return;
        }

        // Show loading
        document.getElementById('transcription-status-detail').classList.remove('d-none');
        document.getElementById('transcribe-detail-btn').disabled = true;

        try {
            const formData = new FormData();
            formData.append('audio', audioBlob, 'recording.webm');

            const response = await fetch('/transcribe-audio', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Display transcriptions
                document.getElementById('original-text-detail').textContent = data.original_text;
                document.getElementById('medical-text-detail').innerHTML = data.medical_enhanced_html;
                
                // Show transcription cards
                document.getElementById('transcription-cards-detail').classList.remove('d-none');
                
                // Show status badge
                if (data.gemini_used) {
                    document.getElementById('gemini-status-detail').classList.remove('d-none');
                }
                
                console.log('✅ Transcription successful');
            } else {
                alert('Transcription failed: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Transcription error:', error);
            alert('Error transcribing audio. Please try again.');
        } finally {
            // Hide loading
            document.getElementById('transcription-status-detail').classList.add('d-none');
            document.getElementById('transcribe-detail-btn').disabled = false;
        }
    }

    /**
     * Copy transcription to textarea
     */
    function copyToTextarea(type) {
        const textarea = document.getElementById('appointment_extra_info_edit');
        const currentText = textarea.value.trim();
        let newText = '';

        if (type === 'original') {
            newText = document.getElementById('original-text-detail').textContent;
        } else if (type === 'medical') {
            newText = document.getElementById('medical-text-detail').textContent;
        } else if (type === 'combined') {
            const original = document.getElementById('original-text-detail').textContent;
            const medical = document.getElementById('medical-text-detail').textContent;
            newText = `Original:\n${original}\n\nMedical Enhanced:\n${medical}`;
        }

        // Append or replace based on whether there's existing text
        if (currentText) {
            textarea.value = currentText + '\n\n' + newText;
        } else {
            textarea.value = newText;
        }

        // Scroll to textarea
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
        textarea.focus();

        console.log(`📋 Copied ${type} text to textarea`);
    }

    /**
     * Initialize save functionality
     */
    function initializeSave() {
        const saveBtn = document.getElementById('save-medical-history-btn');

        if (saveBtn) {
            saveBtn.addEventListener('click', saveMedicalHistory);
        }
    }

    /**
     * Save medical history
     */
    async function saveMedicalHistory() {
        const textarea = document.getElementById('appointment_extra_info_edit');
        const newText = textarea.value.trim();

        if (!newText) {
            alert('Please enter medical history information.');
            return;
        }

        // Get appointment ID from URL
        const appointmentId = window.location.pathname.split('/').pop();

        // Show loading
        const saveBtn = document.getElementById('save-medical-history-btn');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Saving...';

        try {
            const response = await fetch(`/appointments/${appointmentId}/update-medical-history`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    appointment_extra_info: newText
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update view
                const viewSection = document.querySelector('#medical-history-view p');
                viewSection.textContent = data.appointment_extra_info;
                viewSection.classList.remove('text-muted');

                // Reset form and switch to view mode
                resetForm();
                document.getElementById('medical-history-edit').classList.add('d-none');
                document.getElementById('medical-history-view').classList.remove('d-none');
                document.getElementById('edit-medical-history-btn').classList.remove('d-none');

                // Show success message
                showSuccessMessage('Medical history updated successfully!');
                
                console.log('✅ Medical history saved');
            } else {
                alert('Failed to save: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Save error:', error);
            alert('Error saving medical history. Please try again.');
        } finally {
            // Restore button
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    }

    /**
     * Reset form
     */
    function resetForm() {
        // Clear audio
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        audioBlob = null;
        audioChunks = [];
        stopTimer();

        // Hide elements
        document.getElementById('audio-player-container-detail').classList.add('d-none');
        document.getElementById('transcription-cards-detail').classList.add('d-none');
        document.getElementById('transcription-status-detail').classList.add('d-none');

        // Reset buttons
        document.getElementById('record-audio-detail-btn').classList.remove('d-none');
        document.getElementById('stop-recording-detail-btn').classList.add('d-none');
        document.getElementById('cancel-recording-detail-btn').classList.add('d-none');
        document.getElementById('recording-timer-detail').classList.add('d-none');
    }

    /**
     * Show success message
     */
    function showSuccessMessage(message) {
        // Create toast/alert element
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            <i class="ph ph-check-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alert);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }

})();
