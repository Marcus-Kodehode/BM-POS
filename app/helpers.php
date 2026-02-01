<?php

/**
 * File: app/helpers.php
 * Purpose: Global helper functions for the application
 */

if (!function_exists('format_nok')) {
    /**
     * Format an integer amount (øre) to Norwegian kroner string
     * 
     * @param int $amount Amount in øre
     * @return string Formatted string (e.g., "150,00 kr")
     */
    function format_nok(int $amount): string
    {
        $kr = $amount / 100;
        return number_format($kr, 2, ',', ' ') . ' kr';
    }
}

/**
 * Summary: Global helper functions including currency formatting for Norwegian kroner
 */
