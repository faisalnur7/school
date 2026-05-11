#!/usr/bin/env php
<?php
/**
 * Standalone Form Update Script
 * Updates all create/edit forms with modern UI styling
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

echo "Found " . count($formFiles) . " form files to update\n\n";

$updated = 0;
$skipped = 0;

foreach ($formFiles as $filePath) {
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Skip if already updated
    if (strpos($content, 'bg-gradient-primary') !== false || strpos($content, 'modern-form') !== false) {
        $skipped++;
        continue;
    }

    // Skip empty or very small files
    if (strlen($content) < 200) {
        $skipped++;
        continue;
    }

    // Extract route name from form action
    preg_match('/route\([\'"]([^\'"]+)[\'"]/', $content, $routeMatches);
    $routeName = $routeMatches[1] ?? 'dashboard';
    $baseRoute = preg_replace('/\.(store|update)$/', '', $routeName);

    // Extract title from card-title
    preg_match('/<h3 class="card-title">([^<]+)<\/h3>/', $content, $titleMatches);
    $title = $titleMatches[1] ?? 'Form';

    // Determine if create or edit
    $isEdit = strpos($filePath, 'edit.blade.php') !== false;
    $submitText = $isEdit ? 'Update' : 'Create';
    $submitIcon = 'fas fa-save';
    $icon = $isEdit ? 'fas fa-edit' : 'fas fa-plus-circle';

    // Extract form content (everything between <form> and </form>)
    preg_match('/<form[^>]*>(.*?)<\/form>/s', $content, $formMatches);
    if (!isset($formMatches[1])) {
        $skipped++;
        continue;
    }

    $formContent = $formMatches[1];

    // Extract form fields (card-body content)
    preg_match('/<div class="card-body">(.*?)<\/div>\s*<div class="card-footer">/s', $formContent, $bodyMatches);
    $bodyContent = $bodyMatches[1] ?? '';

    // Clean up body content - remove error display
    $bodyContent = preg_replace('/@if\(\$errors->any\(\)\).*?@endif/s', '', $bodyContent);
    $bodyContent = trim($bodyContent);

    // Extract form method and action
    preg_match('/method="([^"]+)"/', $formMatches[0], $methodMatches);
    preg_match('/action="([^"]+)"/', $formMatches[0], $actionMatches);
    $method = strtoupper($methodMatches[1] ?? 'POST');
    $action = $actionMatches[1] ?? '';

    // Build new form structure
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
                        <i class="$submitIcon mr-1"></i>$submitText
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

    // Write updated content
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
