<?php

if (! function_exists('menuOpen')) {
    function menuOpen(array $routes): string {
        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return 'menu-is-opening menu-open';
            }
        }
        return '';
    }
}

if (! function_exists('menuActive')) {
    function menuActive(array $routes): string {
        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return 'active';
            }
        }
        return '';
    }
}

if (! function_exists('localized_number')) {
    function localized_number(int|float|string|null $value, int $decimals = 0): string
    {
        $numericValue = is_numeric($value) ? (float) $value : 0.0;
        $locale = app()->getLocale();

        if (class_exists(\NumberFormatter::class)) {
            $formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
            $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
            $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

            $formatted = $formatter->format($numericValue);

            if ($formatted !== false) {
                return $formatted;
            }
        }

        return number_format($numericValue, $decimals);
    }
}

if (! function_exists('localized_currency')) {
    function localized_currency(int|float|string|null $value, string $symbol = '৳', int $decimals = 0): string
    {
        return $symbol . localized_number($value, $decimals);
    }
}
