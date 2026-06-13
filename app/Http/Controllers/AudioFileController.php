<?php

namespace App\Http\Controllers;

use App\Models\AudioTranscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AudioFileController extends Controller
{
    /**
     * Serve audio file securely
     */
    public function serve($id)
    {
        $transcription = AudioTranscription::findOrFail($id);
        
        // Check authorization (admin, doctor, or the patient who owns it)
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }
        
        $user = auth()->user();
        $isAuthorized = $user->hasRole(['admin', 'demo_admin', 'doctor']) 
                       || $user->id === $transcription->user_id;
        
        if (!$isAuthorized) {
            abort(403, 'Unauthorized to access this audio file');
        }
        
        // Get file path
        $filePath = $transcription->audio_file_path;
        
        // Check if file exists in storage
        if (!Storage::exists($filePath)) {
            abort(404, 'Audio file not found');
        }
        
        // Get file content
        $file = Storage::get($filePath);
        $mimeType = Storage::mimeType($filePath);
        
        // Return streamed response
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . basename($filePath) . '"')
            ->header('Accept-Ranges', 'bytes')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
