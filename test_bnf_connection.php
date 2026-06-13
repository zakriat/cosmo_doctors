<?php
/**
 * Test BNF Connection and Proxies
 * Simple script to test if we can access BNF with proxies
 */

// Load proxies
$proxies = [];
if (file_exists('proxies.txt')) {
    $lines = file('proxies.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && !str_starts_with($line, '#')) {
            $proxies[] = $line;
        }
    }
}

echo "BNF Connection Test\n";
echo "==================\n\n";

echo "Loaded " . count($proxies) . " proxies\n\n";

// Test direct connection first
echo "Testing direct connection to BNF...\n";
$result = testConnection('https://bnf.nice.org.uk/', null);
echo $result ? "✓ Direct connection works\n" : "✗ Direct connection failed\n";

if (!$result && !empty($proxies)) {
    echo "\nTesting with proxies...\n";
    $workingProxies = [];
    
    foreach (array_slice($proxies, 0, 10) as $i => $proxy) {
        echo "Testing proxy " . ($i + 1) . ": $proxy ... ";
        
        if (testConnection('https://bnf.nice.org.uk/', $proxy)) {
            echo "✓ Working\n";
            $workingProxies[] = $proxy;
            
            // Test specific BNF page
            echo "  Testing BNF drug page... ";
            if (testConnection('https://bnf.nice.org.uk/drugs/paracetamol/', $proxy)) {
                echo "✓ Drug page accessible\n";
            } else {
                echo "✗ Drug page blocked\n";
            }
        } else {
            echo "✗ Failed\n";
        }
        
        if (count($workingProxies) >= 3) {
            break; // Found enough working proxies
        }
    }
    
    echo "\nWorking proxies found: " . count($workingProxies) . "\n";
    foreach ($workingProxies as $proxy) {
        echo "  - $proxy\n";
    }
    
    if (!empty($workingProxies)) {
        echo "\n✓ Ready to scrape with proxies!\n";
        
        // Test scraping a sample page
        echo "\nTesting sample scrape...\n";
        $html = fetchPage('https://bnf.nice.org.uk/drugs/paracetamol/', $workingProxies[0]);
        if ($html) {
            echo "✓ Successfully fetched sample page (" . strlen($html) . " bytes)\n";
            
            // Check if it contains expected content
            if (strpos($html, 'paracetamol') !== false || strpos($html, 'Paracetamol') !== false) {
                echo "✓ Page contains expected drug information\n";
            } else {
                echo "⚠ Page may be blocked or redirected\n";
            }
        } else {
            echo "✗ Failed to fetch sample page\n";
        }
    }
} else if ($result) {
    echo "\n✓ Direct connection works - no proxy needed!\n";
} else {
    echo "\n✗ No working connection found. You may need:\n";
    echo "  1. Better proxy servers\n";
    echo "  2. VPN connection\n";
    echo "  3. Different network/location\n";
}

function testConnection($url, $proxy = null)
{
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9',
            'Connection: keep-alive',
        ]
    ]);
    
    if ($proxy) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return $response && $httpCode == 200;
}

function fetchPage($url, $proxy)
{
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_PROXY => $proxy,
        CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
        CURLOPT_HTTPHEADER => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9',
            'Connection: keep-alive',
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ($response && $httpCode == 200) ? $response : false;
}