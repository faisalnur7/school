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