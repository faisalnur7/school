#!/usr/bin/env php
<?php
/**
 * Enhanced Form Update Script - Handles complex form structures
 */

$formsPath = '/home/faisal/Sites/school/resources/views/pages';
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($formsPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$formFiles = [];
foreach ($iterator as $file) {
    if ($file->isFile() && in_array($file->getFilename(), ['create.blade.php', 'edit.blade.php'])) {
        $formFiles[] = $file->getRealPath();
    }
}

echo "Found " . count($formFiles) . " form files\n\n";

$updated = 0;
$skipped = 0;

foreach ($formFiles as $filePath) {
    $content = file_get_contents($filePath);
    
    // Skip if already updated
    if (strpos($content, 'bg-gradient-primary') !== false) {
        $skipped++;
        continue;
    }

    // Skip empty files
    if (strlen($content) < 200) {
        $skipped++;
        continue;
    }

    // Extract title
    preg_match('/<h3[^>]*>([^<]+)<\/h3>/', $content, $titleMatches);
    $title = trim($titleMatches[1] ?? 'Form');

    // Determine if create or edit
    $isEdit = strpos($filePath, 'edit.blade.php') !== false;
    $submitText = $isEdit ? 'Update' : 'Create';
    $icon = $isEdit ? 'fas fa-edit' : 'fas fa-plus-circle';

    // Extract route name
    preg_match('/route\([\'"]([^\'"]+)[\'"]/', $content, $routeMatches);
    $routeName = $routeMatches[1] ?? 'dashboard';
    $baseRoute = preg_replace('/\.(store|update)$/', '', $routeName);

    // Extract form action and method
    preg_match('/<form[^>]*method=[\'"]([^\'"]+)[\'"][^>]*action=[\'"]([^\'"]+)[\'"]/', $content, $formMatches);
    if (empty($formMatches)) {
        preg_match('/<form[^>]*action=[\'"]([^\'"]+)[\'"][^>]*method=[\'"]([^\'"]+)[\'"]/', $content, $formMatches);
    }
    
    $method = strtoupper($formMatches[1] ?? 'POST');
    $action = $formMatches[2] ?? '';

    // Extract form body content
    preg_match('/<form[^>]*>.*?<div[^>]*class="card-body"[^>]*>(.*?)<\/div>\s*<div[^>]*class="card-footer"/', $content, $bodyMatches);
    if (empty($bodyMatches)) {
        preg_match('/<form[^>]*>(.*?)<\/form>/s', $content, $formContent);
        if (!empty($formContent)) {
            $bodyContent = $formContent[1];
            // Remove @csrf and @method
            $bodyContent = preg_replace('/@csrf/', '', $bodyContent);
            $bodyContent = preg_replace('/@method\([^)]+\)/', '', $bodyContent);
            // Remove card-header and card-footer
            $bodyContent = preg_replace('/<div[^>]*class="card-header".*?<\/div>/s', '', $bodyContent);
            $bodyContent = preg_replace('/<div[^>]*class="card-footer".*?<\/div>/s', '', $bodyContent);
            // Remove card-body div tags
            $bodyContent = preg_replace('/<div[^>]*class="card-body"[^>]*>/', '', $bodyContent);
            $bodyContent = preg_replace('/<\/div>\s*$/', '', $bodyContent);
        } else {
            $bodyContent = '';
        }
    } else {
        $bodyContent = $bodyMatches[1];
    }

    // Clean up body content
    $bodyContent = preg_replace('/@if\(\$errors->any\(\)\).*?@endif/s', '', $bodyContent);
    $bodyContent = trim($bodyContent);

    if (empty($bodyContent)) {
        $skipped++;
        continue;
    }

    // Build new form
    $methodField = ($method === 'PUT') ? "            @method('PUT')\n" : '';

    $newContent = <<<BLADE
@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold">
                    <i class="$icon mr-2"></i>$title
                </h4>
                <a href="{{ route('$baseRoute.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="$action" id="modernForm">
            @csrf
$methodField
            <div class="card-body p-3">
                @if(\$errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Errors:</strong>
                        <ul class="mb-0 mt-1 ml-4">
                            @foreach(\$errors->all() as \$error)
                                <li>{{ \$error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

$bodyContent
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('$baseRoute.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>$submitText
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
@include('components.form-styles')
@endsection

@section('scripts')
<script>
    \$(function () {
        if (\$('.is-invalid').length > 0) {
            \$('html, body').animate({
                scrollTop: \$('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection
BLADE;

    file_put_contents($filePath, $newContent);
    $updated++;
    
    $relPath = str_replace('/home/faisal/Sites/school/', '', $filePath);
    echo "✓ Updated: $relPath\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Summary:\n";
echo "  Updated: $updated files\n";
echo "  Skipped: $skipped files\n";
echo "  Total: " . ($updated + $skipped) . " files\n";
echo str_repeat('=', 60) . "\n";
?>
