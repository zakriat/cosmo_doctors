<?php
/**
 * BNF Scraper Configuration
 * Configure proxy settings and other options here
 */

return [
    // Proxy settings (add your proxy servers here to bypass geo-blocking)
    'proxies' => [
        // Example proxy formats:
        // 'ip:port',
        // 'username:password@ip:port',
        // 'http://proxy.example.com:8080',
        
        // Free proxy services (update with working proxies)
        '185.199.229.156:7492',
        '185.199.228.220:7300',
        '185.199.231.45:8382',
    ],
    
    // User agents for rotation
    'user_agents' => [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/121.0',
    ],
    
    // Request settings
    'request_delay' => [2, 5], // Random delay between requests (min, max seconds)
    'timeout' => 30, // Request timeout in seconds
    'retries' => 3, // Number of retries for failed requests
    
    // Scraping settings
    'max_medicines_per_category' => 100, // Limit medicines per category (0 = no limit)
    'categories_to_skip' => [
        // Add category names to skip
        // 'Emergency treatment of poisoning',
        // 'Guidance on prescribing',
    ],
    
    // Output settings
    'output_files' => [
        'json' => 'bnf_medicines_data.json',
        'sql' => 'bnf_medicines_insert.sql',
        'log' => 'bnf_scraper.log',
        'csv' => 'bnf_medicines_data.csv', // Optional CSV export
    ],
    
    // Database table mapping
    'db_fields' => [
        'name',
        'generic_name',
        'brand_name',
        'strength',
        'dosage_form',
        'manufacturer',
        'category',
        'formulae',
        'side_effects',
        'indication',
        'contraindication',
        'drug_interactions',
        'pregnancy_category',
        'storage_conditions',
        'price',
        'url',
        'status'
    ]
];