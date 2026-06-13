<?php
/**
 * BNF Medicine Scraper
 * Scrapes medicine data from https://bnf.nice.org.uk/
 * Handles geo-IP blocking and generates SQL insert queries
 */

class BNFScraper
{
    private $baseUrl = 'https://bnf.nice.org.uk';
    private $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/121.0',
    ];
    
    private $proxyList = [];
    
    private $medicines = [];
    private $logFile = 'bnf_scraper.log';
    
    public function __construct()
    {
        // Load proxies from file
        $this->loadProxies();
        
        // Create log file
        file_put_contents($this->logFile, "BNF Scraper started at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    }
    
    /**
     * Load proxies from file
     */
    private function loadProxies()
    {
        if (file_exists('proxies.txt')) {
            $proxies = file('proxies.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($proxies as $proxy) {
                $proxy = trim($proxy);
                if (!empty($proxy) && !str_starts_with($proxy, '#')) {
                    $this->proxyList[] = $proxy;
                }
            }
            $this->log("Loaded " . count($this->proxyList) . " proxies");
        } else {
            $this->log("No proxies.txt file found - may encounter geo-blocking");
        }
    }
    
    /**
     * Main scraping function
     */
    public function scrapeAllMedicines()
    {
        $this->log("Starting BNF medicine scraping...");
        
        // Test proxies first
        $this->testProxies();
        
        // First, get all drug categories/sections
        $categories = $this->getDrugCategories();
        
        if (empty($categories)) {
            $this->log("No categories found, trying alternative approach...");
            return $this->scrapeDirectDrugs();
        }
        
        foreach ($categories as $category) {
            $this->log("Scraping category: " . $category['name']);
            $drugs = $this->getDrugsFromCategory($category['url']);
            
            foreach ($drugs as $drug) {
                $this->log("Scraping drug: " . $drug['name']);
                $medicineData = $this->scrapeMedicineDetails($drug['url']);
                
                if ($medicineData) {
                    $this->medicines[] = $medicineData;
                    $this->log("Successfully scraped: " . $medicineData['name']);
                    
                    // Add delay to avoid being blocked
                    sleep(rand(2, 5));
                }
                
                // Limit for testing
                if (count($this->medicines) >= 50) {
                    $this->log("Reached limit of 50 medicines for testing");
                    break 2;
                }
            }
        }
        
        $this->log("Scraping completed. Total medicines: " . count($this->medicines));
        return $this->medicines;
    }
    
    /**
     * Test proxy connections
     */
    private function testProxies()
    {
        if (empty($this->proxyList)) {
            $this->log("No proxies to test");
            return;
        }
        
        $this->log("Testing " . count($this->proxyList) . " proxies...");
        $workingProxies = [];
        
        foreach ($this->proxyList as $proxy) {
            if ($this->testSingleProxy($proxy)) {
                $workingProxies[] = $proxy;
                $this->log("Proxy working: $proxy");
            } else {
                $this->log("Proxy failed: $proxy");
            }
            
            // Test only first 10 proxies to save time
            if (count($workingProxies) >= 10) {
                break;
            }
        }
        
        $this->proxyList = $workingProxies;
        $this->log("Found " . count($workingProxies) . " working proxies");
    }
    
    /**
     * Test single proxy
     */
    private function testSingleProxy($proxy)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://httpbin.org/ip',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_PROXY => $proxy,
            CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $response && $httpCode == 200;
    }
    
    /**
     * Scrape direct drugs when categories fail
     */
    private function scrapeDirectDrugs()
    {
        $commonDrugs = [
            'paracetamol', 'ibuprofen', 'aspirin', 'amoxicillin', 'simvastatin',
            'metformin', 'omeprazole', 'amlodipine', 'atorvastatin', 'levothyroxine',
            'ramipril', 'bendroflumethiazide', 'salbutamol', 'prednisolone', 'warfarin',
            'furosemide', 'bisoprolol', 'clopidogrel', 'lansoprazole', 'sertraline',
            'diclofenac', 'codeine', 'tramadol', 'morphine', 'gabapentin',
            'amitriptyline', 'fluoxetine', 'citalopram', 'lorazepam', 'diazepam'
        ];
        
        $this->log("Trying direct drug scraping for " . count($commonDrugs) . " common drugs");
        
        foreach ($commonDrugs as $drugName) {
            $url = $this->baseUrl . '/drugs/' . strtolower($drugName) . '/';
            $this->log("Trying direct URL: $url");
            
            $medicineData = $this->scrapeMedicineDetails($url);
            if ($medicineData) {
                $this->medicines[] = $medicineData;
                $this->log("Successfully scraped: " . $medicineData['name']);
            }
            
            sleep(rand(3, 6)); // Longer delay for direct scraping
        }
        
        return $this->medicines;
    }
    
    /**
     * Get drug categories from BNF
     */
    private function getDrugCategories()
    {
        // Try different approaches to get drug listings
        $urls = [
            $this->baseUrl . '/drugs/',
            $this->baseUrl . '/drug/',
            $this->baseUrl . '/medicines-guidance/',
            $this->baseUrl . '/treatment-summaries/',
        ];
        
        foreach ($urls as $url) {
            $this->log("Trying to fetch categories from: $url");
            $html = $this->fetchPage($url);
            
            if (!$html) {
                continue;
            }
            
            $categories = $this->extractCategoriesFromHtml($html, $url);
            if (!empty($categories)) {
                $this->log("Found " . count($categories) . " categories from $url");
                return $categories;
            }
        }
        
        $this->log("Failed to fetch categories from all URLs, trying direct drug search");
        return $this->getDirectDrugList();
    }
    
    /**
     * Extract categories from HTML
     */
    private function extractCategoriesFromHtml($html, $baseUrl)
    {
        $categories = [];
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        
        // Multiple selectors to try
        $selectors = [
            '//a[contains(@href, "/drugs/")]',
            '//a[contains(@href, "/drug/")]',
            '//a[contains(@href, "/treatment-summary/")]',
            '//div[@class="drug-index"]//a',
            '//ul[@class="drug-list"]//a',
            '//div[contains(@class, "drug")]//a',
        ];
        
        foreach ($selectors as $selector) {
            $links = $xpath->query($selector);
            
            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                $text = trim($link->textContent);
                
                if (!empty($text) && !empty($href) && strlen($text) > 2) {
                    // Make sure href is absolute
                    if (str_starts_with($href, '/')) {
                        $href = $this->baseUrl . $href;
                    }
                    
                    $categories[] = [
                        'name' => $text,
                        'url' => $href
                    ];
                }
            }
            
            if (!empty($categories)) {
                break;
            }
        }
        
        // Remove duplicates
        $unique = [];
        foreach ($categories as $cat) {
            $key = $cat['url'];
            if (!isset($unique[$key])) {
                $unique[$key] = $cat;
            }
        }
        
        return array_values($unique);
    }
    
    /**
     * Get direct drug list by trying common drug names
     */
    private function getDirectDrugList()
    {
        $commonDrugs = [
            'paracetamol', 'ibuprofen', 'aspirin', 'amoxicillin', 'simvastatin',
            'metformin', 'omeprazole', 'amlodipine', 'atorvastatin', 'levothyroxine',
            'ramipril', 'bendroflumethiazide', 'salbutamol', 'prednisolone', 'warfarin',
            'furosemide', 'bisoprolol', 'clopidogrel', 'lansoprazole', 'sertraline'
        ];
        
        $drugs = [];
        foreach ($commonDrugs as $drugName) {
            $url = $this->baseUrl . '/drugs/' . strtolower($drugName) . '/';
            $drugs[] = [
                'name' => ucfirst($drugName),
                'url' => $url
            ];
        }
        
        $this->log("Generated direct drug list with " . count($drugs) . " common drugs");
        return $drugs;
    }
    
    /**
     * Get drugs from a specific category
     */
    private function getDrugsFromCategory($categoryUrl)
    {
        $html = $this->fetchPage($categoryUrl);
        
        if (!$html) {
            return [];
        }
        
        $drugs = [];
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        
        // Look for individual drug links
        $links = $xpath->query('//a[contains(@href, "/drugs/") and not(contains(@href, "/drugs/"))]');
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $text = trim($link->textContent);
            
            if (!empty($text) && !empty($href) && strpos($href, '/drugs/') !== false) {
                $drugs[] = [
                    'name' => $text,
                    'url' => $this->baseUrl . $href
                ];
            }
        }
        
        return $drugs;
    }
    
    /**
     * Scrape detailed medicine information
     */
    private function scrapeMedicineDetails($drugUrl)
    {
        $html = $this->fetchPage($drugUrl);
        
        if (!$html) {
            return null;
        }
        
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        
        // Extract medicine data
        $medicine = [
            'name' => $this->extractText($xpath, '//h1[@class="drug-header__title"]'),
            'generic_name' => $this->extractGenericName($xpath),
            'brand_name' => $this->extractBrandName($xpath),
            'strength' => $this->extractStrength($xpath),
            'dosage_form' => $this->extractDosageForm($xpath),
            'manufacturer' => $this->extractManufacturer($xpath),
            'category' => $this->extractCategory($xpath),
            'formulae' => $this->extractFormulae($xpath),
            'side_effects' => $this->extractSideEffects($xpath),
            'indication' => $this->extractIndications($xpath),
            'contraindication' => $this->extractContraindications($xpath),
            'drug_interactions' => $this->extractDrugInteractions($xpath),
            'pregnancy_category' => $this->extractPregnancyCategory($xpath),
            'storage_conditions' => $this->extractStorageConditions($xpath),
            'price' => $this->extractPrice($xpath),
            'url' => $drugUrl,
            'status' => 1
        ];
        
        // Clean and validate data
        $medicine = $this->cleanMedicineData($medicine);
        
        return $medicine;
    }
    
    /**
     * Extract text from XPath query
     */
    private function extractText($xpath, $query)
    {
        $nodes = $xpath->query($query);
        return $nodes->length > 0 ? trim($nodes->item(0)->textContent) : null;
    }
    
    /**
     * Extract generic name
     */
    private function extractGenericName($xpath)
    {
        // Try multiple selectors for generic name
        $queries = [
            '//span[contains(@class, "generic-name")]',
            '//div[contains(@class, "drug-generic")]//text()',
            '//h1[@class="drug-header__title"]'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result) {
                return $this->cleanGenericName($result);
            }
        }
        
        return null;
    }
    
    /**
     * Extract brand names
     */
    private function extractBrandName($xpath)
    {
        $queries = [
            '//div[contains(@class, "brand-names")]//text()',
            '//span[contains(@class, "brand-name")]',
            '//div[contains(text(), "Brand names")]//following-sibling::div'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result) {
                return $result;
            }
        }
        
        return null;
    }
    
    /**
     * Extract strength information
     */
    private function extractStrength($xpath)
    {
        $queries = [
            '//div[contains(@class, "strength")]//text()',
            '//span[contains(@class, "dose")]',
            '//div[contains(text(), "Strength")]//following-sibling::div'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result && preg_match('/\d+\s*(mg|g|ml|mcg|units?)/i', $result)) {
                return $result;
            }
        }
        
        return null;
    }
    
    /**
     * Extract dosage form
     */
    private function extractDosageForm($xpath)
    {
        $queries = [
            '//div[contains(@class, "dosage-form")]//text()',
            '//span[contains(@class, "form")]',
            '//div[contains(text(), "Form")]//following-sibling::div'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result) {
                return strtolower($result);
            }
        }
        
        return null;
    }
    
    /**
     * Extract manufacturer
     */
    private function extractManufacturer($xpath)
    {
        $queries = [
            '//div[contains(@class, "manufacturer")]//text()',
            '//span[contains(@class, "manufacturer")]',
            '//div[contains(text(), "Manufacturer")]//following-sibling::div'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result) {
                return $result;
            }
        }
        
        return null;
    }
    
    /**
     * Extract category/therapeutic class
     */
    private function extractCategory($xpath)
    {
        $queries = [
            '//div[contains(@class, "therapeutic-class")]//text()',
            '//span[contains(@class, "category")]',
            '//div[contains(text(), "Therapeutic class")]//following-sibling::div'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result) {
                return $result;
            }
        }
        
        return null;
    }
    
    /**
     * Extract chemical formulae
     */
    private function extractFormulae($xpath)
    {
        $queries = [
            '//div[contains(@class, "chemical-formula")]//text()',
            '//span[contains(@class, "formula")]',
            '//div[contains(text(), "Chemical formula")]//following-sibling::div'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result && preg_match('/^[A-Z][a-z]?[0-9]*/', $result)) {
                return $result;
            }
        }
        
        return null;
    }
    
    /**
     * Extract side effects
     */
    private function extractSideEffects($xpath)
    {
        $queries = [
            '//div[contains(@class, "side-effects")]//text()',
            '//div[contains(text(), "Side effects")]//following-sibling::div//text()',
            '//section[contains(@class, "adverse-effects")]//text()'
        ];
        
        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes->length > 0) {
                $sideEffects = [];
                foreach ($nodes as $node) {
                    $text = trim($node->textContent);
                    if (!empty($text)) {
                        $sideEffects[] = $text;
                    }
                }
                return implode(', ', array_slice($sideEffects, 0, 10)); // Limit to 10 side effects
            }
        }
        
        return null;
    }
    
    /**
     * Extract indications
     */
    private function extractIndications($xpath)
    {
        $queries = [
            '//div[contains(@class, "indications")]//text()',
            '//div[contains(text(), "Indications")]//following-sibling::div//text()',
            '//section[contains(@class, "therapeutic-indications")]//text()'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result) {
                return $result;
            }
        }
        
        return null;
    }
    
    /**
     * Extract contraindications
     */
    private function extractContraindications($xpath)
    {
        $queries = [
            '//div[contains(@class, "contraindications")]//text()',
            '//div[contains(text(), "Contraindications")]//following-sibling::div//text()',
            '//section[contains(@class, "contraindications")]//text()'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result) {
                return $result;
            }
        }
        
        return null;
    }
    
    /**
     * Extract drug interactions
     */
    private function extractDrugInteractions($xpath)
    {
        $queries = [
            '//div[contains(@class, "interactions")]//text()',
            '//div[contains(text(), "Interactions")]//following-sibling::div//text()',
            '//section[contains(@class, "drug-interactions")]//text()'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result) {
                return $result;
            }
        }
        
        return null;
    }
    
    /**
     * Extract pregnancy category
     */
    private function extractPregnancyCategory($xpath)
    {
        $queries = [
            '//div[contains(@class, "pregnancy")]//text()',
            '//div[contains(text(), "Pregnancy")]//following-sibling::div//text()',
            '//span[contains(@class, "pregnancy-category")]'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result && preg_match('/[A-DX]/i', $result)) {
                return strtoupper($result);
            }
        }
        
        return null;
    }
    
    /**
     * Extract storage conditions
     */
    private function extractStorageConditions($xpath)
    {
        $queries = [
            '//div[contains(@class, "storage")]//text()',
            '//div[contains(text(), "Storage")]//following-sibling::div//text()',
            '//section[contains(@class, "storage-conditions")]//text()'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result) {
                return $result;
            }
        }
        
        return null;
    }
    
    /**
     * Extract price information
     */
    private function extractPrice($xpath)
    {
        $queries = [
            '//div[contains(@class, "price")]//text()',
            '//span[contains(@class, "cost")]',
            '//div[contains(text(), "Price")]//following-sibling::div//text()'
        ];
        
        foreach ($queries as $query) {
            $result = $this->extractText($xpath, $query);
            if ($result && preg_match('/£?(\d+\.?\d*)/', $result, $matches)) {
                return floatval($matches[1]);
            }
        }
        
        return null;
    }
    
    /**
     * Clean generic name from title
     */
    private function cleanGenericName($name)
    {
        // Remove dosage information and brand names
        $name = preg_replace('/\s+\d+\s*(mg|g|ml|mcg|units?)/i', '', $name);
        $name = preg_replace('/\s+tablets?/i', '', $name);
        $name = preg_replace('/\s+capsules?/i', '', $name);
        
        return trim($name);
    }
    
    /**
     * Clean and validate medicine data
     */
    private function cleanMedicineData($medicine)
    {
        foreach ($medicine as $key => $value) {
            if (is_string($value)) {
                $medicine[$key] = trim($value);
                if (empty($medicine[$key])) {
                    $medicine[$key] = null;
                }
            }
        }
        
        return $medicine;
    }
    
    /**
     * Fetch page with proxy and user agent rotation
     */
    private function fetchPage($url, $retries = 3)
    {
        for ($i = 0; $i < $retries; $i++) {
            $ch = curl_init();
            
            // Basic curl options
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_USERAGENT => $this->userAgents[array_rand($this->userAgents)],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_ENCODING => 'gzip, deflate',
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.9',
                    'Accept-Encoding: gzip, deflate, br',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-Site: none',
                    'Cache-Control: max-age=0',
                ]
            ]);
            
            // Use proxy if available
            if (!empty($this->proxyList)) {
                $proxy = $this->proxyList[array_rand($this->proxyList)];
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                $this->log("Using proxy: $proxy for $url");
            }
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            
            curl_close($ch);
            
            if ($response && $httpCode == 200) {
                $this->log("Successfully fetched $url (HTTP $httpCode)");
                return $response;
            }
            
            $this->log("Failed to fetch $url (attempt " . ($i + 1) . "): HTTP $httpCode, Error: $error");
            
            // If blocked, try different proxy
            if ($httpCode == 403 || $httpCode == 429) {
                $this->log("Blocked response detected, rotating proxy...");
                // Remove failed proxy from list temporarily
                if (!empty($this->proxyList) && isset($proxy)) {
                    $key = array_search($proxy, $this->proxyList);
                    if ($key !== false) {
                        unset($this->proxyList[$key]);
                        $this->proxyList = array_values($this->proxyList);
                    }
                }
            }
            
            // Wait before retry with exponential backoff
            $waitTime = pow(2, $i) + rand(1, 3);
            $this->log("Waiting {$waitTime} seconds before retry...");
            sleep($waitTime);
        }
        
        return false;
    }
    
    /**
     * Generate SQL insert queries
     */
    public function generateInsertQueries()
    {
        if (empty($this->medicines)) {
            return "-- No medicines data available\n";
        }
        
        $sql = "-- BNF Medicines Insert Queries\n";
        $sql .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($this->medicines as $medicine) {
            $sql .= $this->generateSingleInsertQuery($medicine) . "\n\n";
        }
        
        return $sql;
    }
    
    /**
     * Generate single insert query
     */
    private function generateSingleInsertQuery($medicine)
    {
        $fields = [];
        $values = [];
        
        // Map medicine data to database fields
        $fieldMapping = [
            'name' => 'name',
            'generic_name' => 'generic_name',
            'brand_name' => 'brand_name',
            'strength' => 'strength',
            'dosage_form' => 'dosage_form',
            'manufacturer' => 'manufacturer',
            'category' => 'category',
            'formulae' => 'formulae',
            'side_effects' => 'side_effects',
            'indication' => 'indication',
            'contraindication' => 'contraindication',
            'drug_interactions' => 'drug_interactions',
            'pregnancy_category' => 'pregnancy_category',
            'storage_conditions' => 'storage_conditions',
            'price' => 'price',
            'url' => 'url',
            'status' => 'status'
        ];
        
        foreach ($fieldMapping as $medicineKey => $dbField) {
            if (isset($medicine[$medicineKey]) && $medicine[$medicineKey] !== null) {
                $fields[] = $dbField;
                
                if (is_numeric($medicine[$medicineKey])) {
                    $values[] = $medicine[$medicineKey];
                } else {
                    $values[] = "'" . addslashes($medicine[$medicineKey]) . "'";
                }
            }
        }
        
        // Add timestamps
        $fields[] = 'created_at';
        $fields[] = 'updated_at';
        $values[] = "NOW()";
        $values[] = "NOW()";
        
        $sql = "INSERT INTO medicines (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");";
        
        return $sql;
    }
    
    /**
     * Save results to files
     */
    public function saveResults()
    {
        // Save raw data as JSON
        file_put_contents('bnf_medicines_data.json', json_encode($this->medicines, JSON_PRETTY_PRINT));
        
        // Save SQL queries
        file_put_contents('bnf_medicines_insert.sql', $this->generateInsertQueries());
        
        $this->log("Results saved to bnf_medicines_data.json and bnf_medicines_insert.sql");
    }
    
    /**
     * Log messages
     */
    private function log($message)
    {
        $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }
}

// Usage
if (php_sapi_name() === 'cli') {
    echo "BNF Medicine Scraper\n";
    echo "===================\n\n";
    
    $scraper = new BNFScraper();
    
    // Scrape all medicines
    $medicines = $scraper->scrapeAllMedicines();
    
    echo "\nScraping completed!\n";
    echo "Total medicines scraped: " . count($medicines) . "\n";
    
    // Save results
    $scraper->saveResults();
    
    echo "\nFiles generated:\n";
    echo "- bnf_medicines_data.json (Raw data)\n";
    echo "- bnf_medicines_insert.sql (SQL insert queries)\n";
    echo "- bnf_scraper.log (Scraping log)\n";
}