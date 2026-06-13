<?php
/**
 * BNF Scraper Runner
 * Simple script to run the BNF scraper with different options
 */

require_once 'bnf_scraper.php';

// Load configuration
$config = include 'bnf_scraper_config.php';

echo "BNF Medicine Scraper\n";
echo "===================\n\n";

// Check if running from command line
if (php_sapi_name() !== 'cli') {
    echo "This script should be run from command line.\n";
    echo "Usage: php run_bnf_scraper.php [options]\n";
    exit(1);
}

// Parse command line arguments
$options = getopt('h', ['help', 'test', 'categories', 'sample::', 'proxy::', 'output::']);

if (isset($options['h']) || isset($options['help'])) {
    showHelp();
    exit(0);
}

if (isset($options['test'])) {
    testConnection();
    exit(0);
}

if (isset($options['categories'])) {
    listCategories();
    exit(0);
}

// Initialize scraper
$scraper = new BNFScraper();

// Set configuration
if (isset($options['proxy'])) {
    $proxyFile = $options['proxy'] ?: 'proxies.txt';
    if (file_exists($proxyFile)) {
        $proxies = array_filter(array_map('trim', file($proxyFile)));
        echo "Loaded " . count($proxies) . " proxies from $proxyFile\n";
    }
}

// Run scraper
if (isset($options['sample'])) {
    $sampleSize = intval($options['sample']) ?: 10;
    echo "Running sample scrape ($sampleSize medicines)...\n\n";
    $medicines = $scraper->scrapeSampleMedicines($sampleSize);
} else {
    echo "Starting full scrape...\n\n";
    $medicines = $scraper->scrapeAllMedicines();
}

echo "\nScraping completed!\n";
echo "Total medicines scraped: " . count($medicines) . "\n";

// Save results
$outputDir = isset($options['output']) ? $options['output'] : '.';
$scraper->saveResults($outputDir);

echo "\nFiles generated in '$outputDir':\n";
echo "- " . $config['output_files']['json'] . " (Raw JSON data)\n";
echo "- " . $config['output_files']['sql'] . " (SQL insert queries)\n";
echo "- " . $config['output_files']['log'] . " (Scraping log)\n";

// Generate summary
generateSummary($medicines);

function showHelp()
{
    echo "BNF Medicine Scraper\n\n";
    echo "Usage: php run_bnf_scraper.php [options]\n\n";
    echo "Options:\n";
    echo "  -h, --help           Show this help message\n";
    echo "  --test               Test connection to BNF website\n";
    echo "  --categories         List available drug categories\n";
    echo "  --sample[=N]         Scrape sample of N medicines (default: 10)\n";
    echo "  --proxy[=file]       Use proxies from file (default: proxies.txt)\n";
    echo "  --output[=dir]       Output directory (default: current directory)\n\n";
    echo "Examples:\n";
    echo "  php run_bnf_scraper.php --test\n";
    echo "  php run_bnf_scraper.php --sample=50\n";
    echo "  php run_bnf_scraper.php --proxy=my_proxies.txt\n";
    echo "  php run_bnf_scraper.php --output=./output\n";
}

function testConnection()
{
    echo "Testing connection to BNF website...\n";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://bnf.nice.org.uk/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($response && $httpCode == 200) {
        echo "✓ Connection successful (HTTP $httpCode)\n";
        echo "✓ Response received (" . strlen($response) . " bytes)\n";
        
        if (strpos($response, 'geo') !== false || strpos($response, 'location') !== false) {
            echo "⚠ Warning: Possible geo-blocking detected\n";
            echo "  Consider using proxy servers\n";
        }
    } else {
        echo "✗ Connection failed (HTTP $httpCode)\n";
        if ($error) {
            echo "  Error: $error\n";
        }
        echo "  You may need to use proxy servers\n";
    }
}

function listCategories()
{
    echo "Fetching drug categories from BNF...\n\n";
    
    $scraper = new BNFScraper();
    // This would need to be implemented in the scraper class
    echo "This feature will be implemented in the scraper class.\n";
}

function generateSummary($medicines)
{
    if (empty($medicines)) {
        return;
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "SCRAPING SUMMARY\n";
    echo str_repeat("=", 50) . "\n";
    
    $stats = [
        'total' => count($medicines),
        'with_generic_name' => 0,
        'with_brand_name' => 0,
        'with_strength' => 0,
        'with_price' => 0,
        'categories' => [],
        'dosage_forms' => [],
    ];
    
    foreach ($medicines as $medicine) {
        if (!empty($medicine['generic_name'])) $stats['with_generic_name']++;
        if (!empty($medicine['brand_name'])) $stats['with_brand_name']++;
        if (!empty($medicine['strength'])) $stats['with_strength']++;
        if (!empty($medicine['price'])) $stats['with_price']++;
        
        if (!empty($medicine['category'])) {
            $stats['categories'][$medicine['category']] = ($stats['categories'][$medicine['category']] ?? 0) + 1;
        }
        
        if (!empty($medicine['dosage_form'])) {
            $stats['dosage_forms'][$medicine['dosage_form']] = ($stats['dosage_forms'][$medicine['dosage_form']] ?? 0) + 1;
        }
    }
    
    echo "Total medicines: " . $stats['total'] . "\n";
    echo "With generic name: " . $stats['with_generic_name'] . " (" . round($stats['with_generic_name']/$stats['total']*100, 1) . "%)\n";
    echo "With brand name: " . $stats['with_brand_name'] . " (" . round($stats['with_brand_name']/$stats['total']*100, 1) . "%)\n";
    echo "With strength: " . $stats['with_strength'] . " (" . round($stats['with_strength']/$stats['total']*100, 1) . "%)\n";
    echo "With price: " . $stats['with_price'] . " (" . round($stats['with_price']/$stats['total']*100, 1) . "%)\n";
    
    if (!empty($stats['categories'])) {
        echo "\nTop categories:\n";
        arsort($stats['categories']);
        $topCategories = array_slice($stats['categories'], 0, 5, true);
        foreach ($topCategories as $category => $count) {
            echo "  - $category: $count\n";
        }
    }
    
    if (!empty($stats['dosage_forms'])) {
        echo "\nDosage forms:\n";
        arsort($stats['dosage_forms']);
        foreach ($stats['dosage_forms'] as $form => $count) {
            echo "  - $form: $count\n";
        }
    }
}