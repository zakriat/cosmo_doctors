<?php

namespace App\Exports\Traits;

use Modules\Currency\Models\Currency;

trait CurrencyFormatting
{
    /**
     * Get currency symbol from currency table
     */
    protected function getCurrencySymbol()
    {
        $currency = Currency::getAllCurrency()->where('is_primary', 1)->first();
        $symbol = $currency->currency_symbol ?? '$';
        
        // Handle Rupee symbol encoding for Excel compatibility
        if ($symbol === '₹') {
            // For Excel compatibility, use Rs. instead of ₹ symbol
            return 'Rs.';
        }
        
        return $symbol;
    }

    /**
     * Format amount with currency symbol
     */
    protected function formatAmountWithCurrency($amount)
    {
        if (is_null($amount) || $amount === '') {
            return '';
        }
        
        $currencySymbol = $this->getCurrencySymbol();
        $formattedAmount = number_format((float)$amount, 2);
        
        // For Excel compatibility, ensure proper encoding
        return $currencySymbol . $formattedAmount;
    }

    /**
     * Format amount with currency symbol (no decimals for whole numbers)
     */
    protected function formatAmountWithCurrencyNoDecimals($amount)
    {
        if (is_null($amount) || $amount === '') {
            return '';
        }
        
        $currencySymbol = $this->getCurrencySymbol();
        $formattedAmount = (float)$amount;
        
        // If it's a whole number, don't show decimals
        if (floor($formattedAmount) == $formattedAmount) {
            $formattedAmount = number_format($formattedAmount, 0);
        } else {
            $formattedAmount = number_format($formattedAmount, 2);
        }
        
        // For Excel compatibility, ensure proper encoding
        return $currencySymbol . $formattedAmount;
    }
}
