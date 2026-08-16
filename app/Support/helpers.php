<?php

declare(strict_types=1);

if (! function_exists('csp_nonce')) {
    /** CSP nonce for the current request — required on any inline <script> tag. */
    function csp_nonce(): string
    {
        return request()->attributes->get('csp_nonce', '');
    }
}

if (! function_exists('money')) {
    /** Formats DECIMAL(15,2) values consistently across all views. */
    function money(float|string|null $amount, string $currency = 'NPR'): string
    {
        return $currency.' '.number_format((float) $amount, 2);
    }
}
