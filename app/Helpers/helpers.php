<?php

if (! function_exists('menuOpen')) {
    function menuOpen(array $routes): bool {
        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return true;
            }
        }
        return false;
    }
}
