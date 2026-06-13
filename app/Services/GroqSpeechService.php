<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GroqSpeechService
{
    private $apiKey;
    private $baseUrl = 'https://api.groq.com/openai/v1';
    private $model;
    private $cacheEnabled;
    private $cacheTtl;

    public function __construct()
    {
        $this->apiKey = config('groq.api_key');
        $this->model = config('groq.model', 'whisper-large-v3-turbo');
        $this->cacheEnabled = config('groq.cache_enabled', true);
        $this->cacheTtl = config('groq.cache_ttl', 3600);

        if (!$this->apiKey) {
            throw new \Exception('Groq API key not configured');
        }
    }

    /**
     * Transcribe audio file with medical context
     */
    public function transcribeAudio(string $audioPath, array $options = []): array
    {
        $startTime = microtime(true);

        try {
            // Validate file
            if (!file_exists($audioPath) || !is_readable($audioPath)) {
                throw new \Exception("Audio file not accessible: $audioPath");
            }

            $fileSize = filesize($audioPath);
            $maxSize = 25 * 1024 * 1024; // 25MB for free tier
            
            if ($fileSize > $maxSize) {
                throw new \Exception("Audio file too large. Maximum size is 25MB.");
            }

            // Check cache
            $cacheKey = 'groq_transcription_' . md5_file($audioPath);
            if ($this->cacheEnabled && Cache::has($cacheKey)) {
                Log::info('Groq transcription cache hit', ['file' => basename($audioPath)]);
                return Cache::get($cacheKey);
            }

            // Prepare medical prompt for better context
            $prompt = $options['prompt'] ?? $this->getMedicalPrompt();
            $language = $options['language'] ?? 'en';
            $responseFormat = $options['response_format'] ?? 'verbose_json';

            // Make API request with multipart form data
            $multipart = [
                [
                    'name' => 'file',
                    'contents' => fopen($audioPath, 'r'),
                    'filename' => basename($audioPath)
                ],
                [
                    'name' => 'model',
                    'contents' => $this->model
                ],
                [
                    'name' => 'prompt',
                    'contents' => $prompt
                ],
                [
                    'name' => 'language',
                    'contents' => $language
                ],
                [
                    'name' => 'response_format',
                    'contents' => $responseFormat
                ],
                [
                    'name' => 'temperature',
                    'contents' => '0.0'
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->asMultipart()
            ->post($this->baseUrl . '/audio/transcriptions', $multipart);

            if (!$response->successful()) {
                throw new \Exception('Groq API error: ' . $response->body());
            }

            $result = $response->json();
            $processingTime = (microtime(true) - $startTime) * 1000;

            // Parse response based on format
            $transcription = $this->parseTranscriptionResponse($result, $responseFormat);
            $transcription['processing_time_ms'] = round($processingTime);
            $transcription['model_used'] = $this->model;
            $transcription['timestamp'] = now()->toISOString();

            // Cache successful result
            if ($this->cacheEnabled) {
                Cache::put($cacheKey, $transcription, $this->cacheTtl);
            }

            Log::info('Groq transcription completed', [
                'file' => basename($audioPath),
                'text_length' => strlen($transcription['text']),
                'processing_time_ms' => $transcription['processing_time_ms'],
                'segments' => count($transcription['segments'] ?? [])
            ]);

            return $transcription;

        } catch (\Exception $e) {
            Log::error('Groq transcription failed', [
                'error' => $e->getMessage(),
                'file' => basename($audioPath ?? ''),
                'processing_time_ms' => round((microtime(true) - $startTime) * 1000)
            ]);

            throw $e;
        }
    }

    /**
     * Parse transcription response based on format
     */
    private function parseTranscriptionResponse(array $response, string $format): array
    {
        if ($format === 'verbose_json') {
            return [
                'text' => $response['text'] ?? '',
                'segments' => $response['segments'] ?? [],
                'words' => $response['words'] ?? [],
                'language' => $response['language'] ?? 'en',
                'duration' => $response['duration'] ?? 0,
            ];
        }

        // Simple json format
        return [
            'text' => $response['text'] ?? $response,
            'segments' => [],
            'words' => [],
            'language' => 'en',
            'duration' => 0,
        ];
    }

    /**
     * Get medical-specific prompt for better transcription
     */
    private function getMedicalPrompt(): string
    {
        return "Medical consultation. Use proper medical terminology. Include: Chief Complaint, History, "
            . "Medications, Allergies, Vital Signs (BP, HR, RR, Temp), Physical Exam, Assessment, Treatment Plan. "
            . "Terms: symptoms (pain, fever, cough, nausea, fatigue, dizziness), "
            . "conditions (hypertension, diabetes, asthma, arthritis, depression, anxiety), "
            . "medications (aspirin, ibuprofen, metformin, antibiotics), "
            . "procedures (exam, blood test, X-ray, ECG, ultrasound), "
            . "anatomy (chest, abdomen, head, heart, lung), "
            . "severity (mild, moderate, severe, acute, chronic). "
            . "Abbreviations: BP, HR, RR, Temp, Dx, Rx, Hx, PE.";
    }

    /**
     * Extract medical entities from transcription with comprehensive medical terminology
     */
    public function extractMedicalEntities(string $text): array
    {
        // Comprehensive medical categories with keywords, priorities, and metadata
        $categories = $this->getMedicalCategories();

        $found = [];
        $textLower = strtolower($text);
        $alreadyFound = []; // Track positions to avoid duplicates

        // First pass: Find multi-word phrases (longer matches first)
        foreach ($categories as $category => $data) {
            $keywords = $data['keywords'];
            
            // Sort keywords by length (longest first) to prioritize multi-word phrases
            usort($keywords, function($a, $b) {
                return strlen($b) - strlen($a);
            });

            foreach ($keywords as $keyword) {
                $offset = 0;
                $keywordLower = strtolower($keyword);
                
                // Find all occurrences of the keyword
                while (($pos = stripos($textLower, $keywordLower, $offset)) !== false) {
                    $endPos = $pos + strlen($keyword);
                    
                    // Check word boundaries for better accuracy
                    $beforeChar = $pos > 0 ? $textLower[$pos - 1] : ' ';
                    $afterChar = $endPos < strlen($textLower) ? $textLower[$endPos] : ' ';
                    $isWordBoundary = !ctype_alnum($beforeChar) && !ctype_alnum($afterChar);
                    
                    // Check if this position overlaps with already found entities
                    $overlaps = false;
                    foreach ($alreadyFound as $foundPos) {
                        if (($pos >= $foundPos['start'] && $pos < $foundPos['end']) ||
                            ($endPos > $foundPos['start'] && $endPos <= $foundPos['end']) ||
                            ($pos <= $foundPos['start'] && $endPos >= $foundPos['end'])) {
                            $overlaps = true;
                            break;
                        }
                    }
                    
                    if (!$overlaps && $isWordBoundary) {
                        // Get the actual text from original (preserving case)
                        $actualText = substr($text, $pos, strlen($keyword));
                        
                        // Calculate confidence based on context and specificity
                        $confidence = $this->calculateConfidence($keyword, $category, $data['priority']);
                        
                        // Check for negation context
                        $isNegated = $this->checkNegation($text, $pos);
                        
                        // Check for severity modifiers
                        $severity = $this->extractSeverity($text, $pos);
                        
                        $found[] = [
                            'text' => $actualText,
                            'category' => $category,
                            'confidence' => $confidence,
                            'start_pos' => $pos,
                            'end_pos' => $endPos,
                            'priority' => $data['priority'],
                            'is_negated' => $isNegated,
                            'severity' => $severity
                        ];
                        
                        $alreadyFound[] = [
                            'start' => $pos,
                            'end' => $endPos,
                            'length' => strlen($keyword)
                        ];
                    }
                    
                    $offset = $pos + 1;
                }
            }
        }

        // Sort by start position for proper highlighting
        usort($found, function($a, $b) {
            return $a['start_pos'] - $b['start_pos'];
        });

        return $found;
    }

    /**
     * Get comprehensive medical categories with keywords and metadata
     */
    private function getMedicalCategories(): array
    {
        return [
            'emergency' => [
                'keywords' => [
                    'chest pain', 'difficulty breathing', 'shortness of breath', 'unconscious', 'unresponsive',
                    'seizure', 'seizures', 'stroke', 'heart attack', 'cardiac arrest', 'severe bleeding', 'hemorrhage',
                    'anaphylaxis', 'choking', 'severe trauma', 'head injury', 'loss of consciousness',
                    'severe abdominal pain', 'acute confusion', 'sudden weakness', 'paralysis', 'paralyzed'
                ],
                'priority' => 'critical',
                'color' => '#dc3545'
            ],
            
            'symptoms' => [
                'keywords' => [
                    // Pain-related (with variations)
                    'pain', 'pains', 'painful', 'ache', 'aches', 'aching', 'discomfort', 'soreness', 'sore',
                    'tenderness', 'tender', 'sharp pain', 'dull ache', 'dull pain',
                    'throbbing', 'burning sensation', 'burning', 'stabbing pain', 'stabbing', 
                    'cramping', 'cramps', 'radiating pain',
                    
                    // General symptoms (with variations)
                    'fever', 'fevers', 'feverish', 'chills', 'sweating', 'sweats', 'night sweats', 
                    'fatigue', 'fatigued', 'tiredness', 'tired', 'exhaustion', 'exhausted',
                    'weakness', 'weak', 'malaise', 'lethargy', 'lethargic', 'drowsiness', 'drowsy',
                    'feeling unwell', 'not feeling well', 'feeling sick', 'feeling bad', 'feeling',
                    
                    // Suffering/experiencing
                    'suffering', 'suffer', 'suffered', 'experiencing', 'experience', 'experienced',
                    'having', 'having problems',
                    
                    // Respiratory (with variations)
                    'cough', 'coughs', 'coughing', 'dry cough', 'productive cough', 'wheezing', 'wheeze',
                    'dyspnea', 'breathlessness', 'breathless', 'short of breath',
                    'congestion', 'congested', 'runny nose', 'nasal discharge', 'sore throat', 'hoarseness', 'hoarse',
                    
                    // Gastrointestinal (with variations)
                    'nausea', 'nauseous', 'vomiting', 'vomit', 'vomited', 'throwing up',
                    'diarrhea', 'diarrhoea', 'constipation', 'constipated', 'bloating', 'bloated',
                    'gas', 'gassy', 'indigestion', 'heartburn', 'acid reflux', 'reflux',
                    'abdominal pain', 'stomach ache', 'stomach pain', 'belly ache', 'tummy ache',
                    'loss of appetite', 'no appetite', 'weight loss', 'losing weight', 'weight gain', 'gaining weight',
                    
                    // Neurological (with variations)
                    'headache', 'headaches', 'head ache', 'migraine', 'migraines',
                    'dizziness', 'dizzy', 'vertigo', 'lightheadedness', 'lightheaded',
                    'confusion', 'confused', 'memory loss', 'forgetful', 'forgetfulness',
                    'numbness', 'numb', 'tingling', 'pins and needles', 'tremor', 'tremors', 'shaking', 'shakes',
                    
                    // Dermatological (with variations)
                    'rash', 'rashes', 'itching', 'itchy', 'itch', 'hives', 'swelling', 'swollen',
                    'redness', 'red', 'bruising', 'bruise', 'bruises', 'lesion', 'lesions',
                    'skin irritation', 'irritated skin', 'dry skin', 'peeling', 'peeling skin',
                    
                    // Other
                    'bleeding', 'bleed', 'discharge', 'palpitations', 'irregular heartbeat',
                    'joint pain', 'muscle pain', 'back pain', 'neck pain', 'stiffness', 'stiff',
                    'insomnia', 'sleep problems', 'trouble sleeping', 'cannot sleep'
                ],
                'priority' => 'high',
                'color' => '#fd7e14'
            ],
            
            'conditions' => [
                'keywords' => [
                    // Cardiovascular
                    'hypertension', 'high blood pressure', 'hypotension', 'low blood pressure',
                    'heart disease', 'coronary artery disease', 'arrhythmia', 'atrial fibrillation',
                    'heart failure', 'angina', 'myocardial infarction',
                    'medical condition', 'medical conditions', 'health condition', 'health conditions', 'condition', 'conditions',
                    
                    // Endocrine
                    'diabetes', 'diabetic', 'type 1 diabetes', 'type 2 diabetes', 'prediabetes', 
                    'hypoglycemia', 'hyperglycemia', 'thyroid disorder', 'hypothyroidism', 'hyperthyroidism',
                    
                    // Respiratory
                    'asthma', 'asthmatic', 'COPD', 
                    
                    // Musculoskeletal
                    'arthritis', 'osteoarthritis', 'rheumatoid arthritis', 'osteoporosis', 'gout',
                    'fibromyalgia', 'back pain', 'sciatica',
                    
                    // Mental Health
                    'depression', 'anxiety', 'panic disorder', 'PTSD', 'bipolar disorder', 'schizophrenia',
                    'OCD', 'ADHD', 'insomnia', 'stress',
                    
                    // Infectious
                    'infection', 'bacterial infection', 'viral infection', 'fungal infection',
                    'UTI', 'urinary tract infection', 'upper respiratory infection', 'flu', 'influenza',
                    'COVID-19', 'coronavirus',
                    
                    // Allergic/Immune
                    'allergy', 'allergic reaction', 'hay fever', 'food allergy', 'drug allergy',
                    'autoimmune disease', 'lupus',
                    
                    // Gastrointestinal
                    'GERD', 'IBS', 'irritable bowel syndrome', 'Crohn\'s disease', 'ulcerative colitis',
                    'gastritis', 'peptic ulcer', 'celiac disease',
                    
                    // Neurological
                    'epilepsy', 'Parkinson\'s disease', 'Alzheimer\'s disease', 'dementia', 'migraine',
                    'multiple sclerosis', 'neuropathy',
                    
                    // Other
                    'chronic', 'acute', 'chronic condition', 'acute condition', 'cancer', 'tumor',
                    'kidney disease', 'liver disease', 'anemia', 'obesity'
                ],
                'priority' => 'high',
                'color' => '#0d6efd'
            ],
            
            'medications' => [
                'keywords' => [
                    // Pain relievers
                    'aspirin', 'ibuprofen', 'paracetamol', 'acetaminophen', 'naproxen', 'diclofenac',
                    'codeine', 'morphine', 'tramadol', 'oxycodone',
                    
                    // Antibiotics
                    'antibiotic', 'amoxicillin', 'penicillin', 'azithromycin', 'ciprofloxacin',
                    'doxycycline', 'cephalexin', 'metronidazole',
                    
                    // Cardiovascular
                    'lisinopril', 'amlodipine', 'metoprolol', 'atenolol', 'losartan', 'simvastatin',
                    'atorvastatin', 'warfarin', 'aspirin', 'clopidogrel',
                    
                    // Diabetes
                    'metformin', 'insulin', 'glipizide', 'glyburide', 'sitagliptin',
                    
                    // Respiratory
                    'albuterol', 'inhaler', 'salbutamol', 'fluticasone', 'montelukast',
                    
                    // Mental Health
                    'sertraline', 'fluoxetine', 'escitalopram', 'citalopram', 'paroxetine',
                    'venlafaxine', 'duloxetine', 'bupropion', 'alprazolam', 'lorazepam',
                    'diazepam', 'clonazepam',
                    
                    // Gastrointestinal
                    'omeprazole', 'lansoprazole', 'pantoprazole', 'ranitidine', 'antacid',
                    
                    // General terms
                    'prescription', 'medication', 'medicine', 'drug', 'tablet', 'capsule', 'pill',
                    'injection', 'IV', 'intravenous', 'topical', 'ointment', 'cream', 'drops',
                    'syrup', 'suspension'
                ],
                'priority' => 'high',
                'color' => '#198754'
            ],
            
            'vitals' => [
                'keywords' => [
                    'blood pressure', 'BP', 'systolic', 'diastolic', 'mmHg',
                    'heart rate', 'HR', 'pulse', 'bpm', 'beats per minute',
                    'respiratory rate', 'RR', 'breathing rate', 'breaths per minute',
                    'temperature', 'temp', 'fever', 'celsius', 'fahrenheit',
                    'oxygen saturation', 'SpO2', 'O2 sat', 'oxygen level',
                    'weight', 'BMI', 'body mass index', 'height',
                    'glucose', 'blood sugar', 'blood glucose'
                ],
                'priority' => 'medium',
                'color' => '#6f42c1'
            ],
            
            'anatomy' => [
                'keywords' => [
                    // Head & Neck
                    'head', 'skull', 'brain', 'face', 'eye', 'eyes', 'ear', 'ears', 'nose',
                    'mouth', 'throat', 'neck', 'jaw', 'tongue', 'teeth', 'gums',
                    
                    // Torso
                    'chest', 'breast', 'thorax', 'abdomen', 'stomach', 'belly', 'back',
                    'spine', 'pelvis', 'hip', 'groin',
                    
                    // Limbs
                    'arm', 'shoulder', 'elbow', 'wrist', 'hand', 'finger', 'thumb',
                    'leg', 'thigh', 'knee', 'ankle', 'foot', 'toe',
                    
                    // Internal organs
                    'heart', 'lung', 'lungs', 'liver', 'kidney', 'kidneys', 'spleen',
                    'pancreas', 'gallbladder', 'bladder', 'intestine', 'colon', 'rectum',
                    'esophagus', 'trachea', 'bronchi',
                    
                    // Systems
                    'cardiovascular', 'respiratory', 'gastrointestinal', 'neurological',
                    'musculoskeletal', 'dermatological', 'genitourinary', 'endocrine'
                ],
                'priority' => 'medium',
                'color' => '#20c997'
            ],
            
            'procedures' => [
                'keywords' => [
                    // Examinations
                    'examination', 'physical exam', 'PE', 'checkup', 'assessment', 'evaluation',
                    'inspection', 'palpation', 'auscultation', 'percussion',
                    
                    // Lab tests
                    'blood test', 'blood work', 'CBC', 'complete blood count', 'metabolic panel',
                    'lipid panel', 'liver function test', 'kidney function test',
                    'urine test', 'urinalysis', 'stool test', 'culture', 'biopsy',
                    
                    // Imaging
                    'X-ray', 'radiograph', 'CT scan', 'CAT scan', 'MRI', 'ultrasound', 'sonogram',
                    'mammogram', 'PET scan', 'bone scan', 'fluoroscopy',
                    
                    // Cardiac
                    'ECG', 'EKG', 'electrocardiogram', 'echocardiogram', 'stress test',
                    'cardiac catheterization', 'angiogram',
                    
                    // Other procedures
                    'endoscopy', 'colonoscopy', 'bronchoscopy', 'cystoscopy',
                    'surgery', 'operation', 'procedure', 'intervention',
                    'vaccination', 'immunization', 'injection', 'IV', 'infusion',
                    'consultation', 'referral', 'follow-up', 'screening'
                ],
                'priority' => 'medium',
                'color' => '#0dcaf0'
            ],
            
            'severity' => [
                'keywords' => [
                    'mild', 'moderate', 'severe', 'critical', 'life-threatening',
                    'acute', 'chronic', 'persistent', 'intermittent', 'constant',
                    'sudden', 'gradual', 'progressive', 'stable', 'worsening', 'improving'
                ],
                'priority' => 'low',
                'color' => '#ffc107'
            ],
            
            'duration' => [
                'keywords' => [
                    'minutes', 'hours', 'days', 'weeks', 'months', 'years',
                    'since yesterday', 'for a week', 'for several days', 'ongoing',
                    'recent', 'long-standing', 'new onset', 'recurrent'
                ],
                'priority' => 'low',
                'color' => '#6c757d'
            ],
            
            'allergies' => [
                'keywords' => [
                    'allergic to', 'allergy', 'allergic reaction', 'anaphylaxis',
                    'penicillin allergy', 'drug allergy', 'food allergy', 'latex allergy',
                    'pollen allergy', 'dust allergy', 'pet allergy', 'shellfish allergy',
                    'nut allergy', 'egg allergy', 'milk allergy'
                ],
                'priority' => 'critical',
                'color' => '#e83e8c'
            ],
            
            'family_history' => [
                'keywords' => [
                    'family history', 'father had', 'mother had', 'sibling had',
                    'runs in family', 'genetic', 'hereditary', 'familial'
                ],
                'priority' => 'low',
                'color' => '#795548'
            ],
            
            'social_history' => [
                'keywords' => [
                    'smoking', 'smoker', 'tobacco', 'cigarettes', 'vaping',
                    'alcohol', 'drinking', 'drug use', 'recreational drugs',
                    'exercise', 'diet', 'occupation', 'stress', 'sleep'
                ],
                'priority' => 'low',
                'color' => '#607d8b'
            ]
        ];
    }

    /**
     * Calculate confidence score based on keyword specificity and context
     */
    private function calculateConfidence(string $keyword, string $category, string $priority): float
    {
        $baseConfidence = 0.85;
        
        // Longer, more specific terms get higher confidence
        $lengthBonus = min(strlen($keyword) / 100, 0.10);
        
        // Multi-word phrases get higher confidence
        $wordCount = str_word_count($keyword);
        $phraseBonus = $wordCount > 1 ? 0.05 : 0;
        
        // Critical priority terms get higher confidence
        $priorityBonus = match($priority) {
            'critical' => 0.10,
            'high' => 0.05,
            'medium' => 0.02,
            default => 0
        };
        
        return min($baseConfidence + $lengthBonus + $phraseBonus + $priorityBonus, 0.99);
    }

    /**
     * Check if a term is negated in context
     */
    private function checkNegation(string $text, int $position): bool
    {
        $negationWords = ['no', 'not', 'never', 'without', 'denies', 'negative for', 'absent'];
        
        // Check 50 characters before the term
        $contextStart = max(0, $position - 50);
        $context = substr($text, $contextStart, $position - $contextStart);
        $contextLower = strtolower($context);
        
        foreach ($negationWords as $negation) {
            if (strpos($contextLower, $negation) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Extract severity modifier from context
     */
    private function extractSeverity(string $text, int $position): ?string
    {
        $severityWords = [
            'severe' => 'severe',
            'moderate' => 'moderate',
            'mild' => 'mild',
            'slight' => 'mild',
            'extreme' => 'severe',
            'intense' => 'severe',
            'minor' => 'mild'
        ];
        
        // Check 30 characters before the term
        $contextStart = max(0, $position - 30);
        $context = substr($text, $contextStart, $position - $contextStart);
        $contextLower = strtolower($context);
        
        foreach ($severityWords as $word => $severity) {
            if (strpos($contextLower, $word) !== false) {
                return $severity;
            }
        }
        
        return null;
    }

    /**
     * Format transcription for medical records
     */
    public function formatForMedicalRecord(array $transcription): string
    {
        $text = $transcription['text'];
        
        // Capitalize first letter of sentences
        $text = preg_replace_callback('/([.!?]\s+)([a-z])/', function($matches) {
            return $matches[1] . strtoupper($matches[2]);
        }, ucfirst($text));

        // Add proper punctuation if missing
        if (!preg_match('/[.!?]$/', $text)) {
            $text .= '.';
        }

        return $text;
    }

    /**
     * Test Groq connection
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/models');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Groq API connection successful',
                    'models' => $response->json()['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'error' => 'API request failed: ' . $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get quality metrics from verbose response
     */
    public function getQualityMetrics(array $transcription): array
    {
        $segments = $transcription['segments'] ?? [];
        
        if (empty($segments)) {
            return [
                'avg_confidence' => 0,
                'low_confidence_segments' => 0,
                'total_segments' => 0
            ];
        }

        $totalLogProb = 0;
        $lowConfidenceCount = 0;

        foreach ($segments as $segment) {
            $avgLogProb = $segment['avg_logprob'] ?? -1;
            $totalLogProb += $avgLogProb;
            
            // Flag segments with low confidence (more negative = lower confidence)
            if ($avgLogProb < -0.5) {
                $lowConfidenceCount++;
            }
        }

        return [
            'avg_confidence' => $totalLogProb / count($segments),
            'low_confidence_segments' => $lowConfidenceCount,
            'total_segments' => count($segments),
            'quality_score' => max(0, min(1, 1 + ($totalLogProb / count($segments))))
        ];
    }
}
