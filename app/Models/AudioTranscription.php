<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AudioTranscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'user_id',
        'audio_file_path',
        'transcription_text',
        'original_text',
        'medical_text',
        'final_text',
        'medical_categories',
        'confidence_scores',
        'word_mappings',
        'duration_seconds',
        'model_used',
        'gemini_model_used',
        'processing_time_ms',
        'gemini_processing_time_ms',
        'gemini_fallback_used',
        'user_edited',
        'edit_count',
        'preferred_version',
        'last_edited_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'processing_time_ms' => 'integer',
        'gemini_processing_time_ms' => 'integer',
        'medical_categories' => 'array',
        'confidence_scores' => 'array',
        'word_mappings' => 'array',
        'user_edited' => 'boolean',
        'gemini_fallback_used' => 'boolean',
        'edit_count' => 'integer',
        'last_edited_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transcription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the audio file URL.
     */
    public function getAudioUrlAttribute(): string
    {
        // Check if file is in public storage
        if (file_exists(public_path('storage/' . $this->audio_file_path))) {
            return asset('storage/' . $this->audio_file_path);
        }
        
        // Otherwise, use route to serve from private storage
        return route('audio.serve', ['id' => $this->id]);
    }

    /**
     * Get the best available transcription text
     */
    public function getBestTranscriptionAttribute(): string
    {
        // Priority: final_text > medical_text > original_text > transcription_text
        return $this->final_text 
            ?? $this->medical_text 
            ?? $this->original_text 
            ?? $this->transcription_text;
    }

    /**
     * Get combined display text (original + medical)
     */
    public function getCombinedDisplayTextAttribute(): string
    {
        if ($this->original_text && $this->medical_text) {
            return "Original: \"{$this->original_text}\"\n\nMedical: \"{$this->medical_text}\"";
        }
        
        return $this->getBestTranscriptionAttribute();
    }

    /**
     * Check if Gemini enhancement was successful
     */
    public function hasGeminiEnhancement(): bool
    {
        return !empty($this->medical_text) && !$this->gemini_fallback_used;
    }

    /**
     * Get medical categories with colors
     */
    public function getMedicalCategoriesWithColors(): array
    {
        if (empty($this->medical_categories)) {
            return [];
        }

        $colors = config('gemini.category_colors', []);
        
        return collect($this->medical_categories)->map(function ($category) use ($colors) {
            $category['color'] = $colors[$category['category']] ?? '#6c757d';
            return $category;
        })->toArray();
    }

    /**
     * Mark as user edited
     */
    public function markAsEdited(string $finalText, string $preferredVersion = 'custom'): void
    {
        $this->update([
            'final_text' => $finalText,
            'user_edited' => true,
            'edit_count' => $this->edit_count + 1,
            'preferred_version' => $preferredVersion,
            'last_edited_at' => now()
        ]);
    }
}
