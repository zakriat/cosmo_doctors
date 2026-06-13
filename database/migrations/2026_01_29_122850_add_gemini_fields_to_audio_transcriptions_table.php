<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audio_transcriptions', function (Blueprint $table) {
            // Store different versions of transcription
            $table->text('original_text')->nullable()->after('transcription_text')->comment('Raw Whisper transcription');
            $table->text('medical_text')->nullable()->after('original_text')->comment('Gemini enhanced medical text');
            $table->text('final_text')->nullable()->after('medical_text')->comment('User selected/edited final version');
            
            // Medical categorization data
            $table->json('medical_categories')->nullable()->after('final_text')->comment('Medical term categories for color coding');
            $table->json('confidence_scores')->nullable()->after('medical_categories')->comment('AI confidence scores');
            $table->json('word_mappings')->nullable()->after('confidence_scores')->comment('Original to medical word mappings');
            
            // Gemini processing metadata
            $table->string('gemini_model_used')->nullable()->after('word_mappings')->comment('Gemini model version used');
            $table->integer('gemini_processing_time_ms')->nullable()->after('gemini_model_used')->comment('Gemini processing time in milliseconds');
            $table->boolean('gemini_fallback_used')->default(false)->after('gemini_processing_time_ms')->comment('Whether fallback was used due to API issues');
            
            // User interaction tracking
            $table->boolean('user_edited')->default(false)->after('gemini_fallback_used')->comment('Whether user manually edited the transcription');
            $table->integer('edit_count')->default(0)->after('user_edited')->comment('Number of times user edited the text');
            $table->enum('preferred_version', ['original', 'medical', 'custom'])->default('medical')->after('edit_count')->comment('User preferred version type');
            $table->timestamp('last_edited_at')->nullable()->after('preferred_version')->comment('When user last edited the transcription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_transcriptions', function (Blueprint $table) {
            $table->dropColumn([
                'original_text',
                'medical_text', 
                'final_text',
                'medical_categories',
                'confidence_scores',
                'word_mappings',
                'gemini_model_used',
                'gemini_processing_time_ms',
                'gemini_fallback_used',
                'user_edited',
                'edit_count',
                'preferred_version',
                'last_edited_at'
            ]);
        });
    }
};