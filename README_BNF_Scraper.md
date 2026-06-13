# BNF Medicine Scraper

A PHP scraper for extracting medicine data from the British National Formulary (BNF) website at https://bnf.nice.org.uk/

## Features

- Scrapes comprehensive medicine data from BNF
- Handles geo-IP blocking with proxy support
- Generates SQL insert queries for your database
- Exports data in JSON and SQL formats
- Configurable scraping parameters
- Detailed logging and error handling
- User agent rotation to avoid detection

## Files

- `bnf_scraper.php` - Main scraper class
- `run_bnf_scraper.php` - Command-line runner script
- `bnf_scraper_config.php` - Configuration file
- `proxies.txt` - Proxy server list (for geo-blocking bypass)

## Installation

1. Ensure PHP 7.4+ is installed with cURL extension
2. Place all files in your project root directory
3. Update `proxies.txt` with working proxy servers (if needed for geo-blocking)

## Usage

### Basic Usage

```bash
# Test connection to BNF
php run_bnf_scraper.php --test

# Scrape a sample of 10 medicines
php run_bnf_scraper.php --sample=10

# Full scrape (may take several hours)
php run_bnf_scraper.php
```

### Advanced Usage

```bash
# Use custom proxy file
php run_bnf_scraper.php --proxy=my_proxies.txt

# Save output to specific directory
php run_bnf_scraper.php --output=./scraped_data

# Get help
php run_bnf_scraper.php --help
```

## Configuration

Edit `bnf_scraper_config.php` to customize:

- Proxy servers
- Request delays
- Output file names
- Database field mapping
- Categories to skip

## Handling Geo-blocking

If you're blocked due to geographic restrictions:

1. Add working proxy servers to `proxies.txt`
2. Use VPN service
3. Consider using residential proxies for better success rates

## Output Files

The scraper generates:

- `bnf_medicines_data.json` - Raw scraped data in JSON format
- `bnf_medicines_insert.sql` - SQL INSERT queries for your database
- `bnf_scraper.log` - Detailed scraping log

## Database Schema

The scraper extracts data for these fields (matching your Medicine model):

- `name` - Medicine name
- `generic_name` - Generic/active ingredient name
- `brand_name` - Brand names
- `strength` - Dosage strength (e.g., "20mg")
- `dosage_form` - Form (tablet, capsule, etc.)
- `manufacturer` - Manufacturer name
- `category` - Therapeutic category
- `formulae` - Chemical formula
- `side_effects` - Common side effects
- `indication` - Medical indications
- `contraindication` - Contraindications
- `drug_interactions` - Drug interactions
- `pregnancy_category` - Pregnancy safety category
- `storage_conditions` - Storage requirements
- `price` - Price information (if available)
- `url` - Source BNF URL
- `status` - Active status (always 1)

## Sample Output

```sql
INSERT INTO medicines (name, generic_name, strength, dosage_form, category, side_effects, indication, url, status, created_at, updated_at) 
VALUES ('Simvastatin 20mg Tablets', 'Simvastatin', '20mg', 'tablet', 'statin', 'Muscle pain, headache, nausea', 'Hypercholesterolemia', 'https://bnf.nice.org.uk/drugs/simvastatin/', 1, NOW(), NOW());
```

## Important Notes

1. **Respect Rate Limits**: The scraper includes delays between requests
2. **Legal Compliance**: Ensure your use complies with BNF's terms of service
3. **Data Accuracy**: Always verify scraped data before using in production
4. **Geo-blocking**: Use proxies if accessing from restricted regions
5. **Large Dataset**: Full scrape may take several hours and generate thousands of records

## Troubleshooting

### Connection Issues
- Check internet connection
- Verify proxy settings
- Try different user agents

### Geo-blocking
- Add working proxies to `proxies.txt`
- Use VPN service
- Try different geographic proxy locations

### Parsing Issues
- Check BNF website structure changes
- Update CSS selectors in scraper
- Enable detailed logging for debugging

## Customization

To modify the scraper for different data extraction:

1. Update the extraction methods in `BNFScraper` class
2. Modify CSS selectors for different page elements
3. Adjust the database field mapping in config
4. Add new extraction methods as needed

## Performance Tips

- Use sample mode for testing: `--sample=50`
- Implement caching for repeated requests
- Use multiple proxy servers for better reliability
- Monitor logs for failed requests and retry

## Legal Disclaimer

This scraper is for educational and research purposes. Ensure compliance with:
- BNF website terms of service
- Local data protection laws
- Ethical web scraping practices
- Rate limiting and respectful usage