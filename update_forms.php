<?php
/**
 * Script to update all form files with modern UI styling
 * Run: php update_forms.php
 */

$formsPath = '/home/faisal/Sites/school/resources/views/pages';
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($formsPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$formFiles = [];
foreach ($files as $file) {
    if ($file->isFile() && ($file->getFilename() === 'create.blade.php' || $file->getFilename() === 'edit.blade.php')) {
        $formFiles[] = $file->getRealPath();
    }
}

echo "Found " . count($formFiles) . " form files\n";

$updatedCount = 0;
foreach ($formFiles as $filePath) {
    $content = file_get_contents($filePath);
    
    // Skip if already updated
    if (strpos($content, 'bg-gradient-primary') !== false) {
        continue;
    }

    // Skip empty files
    if (strlen($content) < 100) {
        continue;
    }

    // Update container
    $content = preg_replace(
        '/<div class="col-md-\d+">\s*<div class="card">/',
        '<div class="container-fluid px-3 py-3"><div class="card shadow-sm border-0">',
        $content
    );

    // Update card header
    $content = preg_replace(
        '/<div class="card-header">\s*<h3 class="card-title">([^<]+)<\/h3>/',
        '<div class="card-header bg-gradient-primary text-white py-3"><div class="d-flex justify-content-between align-items-center"><h4 class="card-title mb-0 font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>$1</h4>',
        $content
    );

    // Add back button if not exists
    if (strpos($content, 'btn-light') === false && strpos($content, 'Back') !== false) {
        $content = preg_replace(
            '/<div class="card-header bg-gradient-primary[^>]*>/',
            '$0<a href="{{ route(\'$1.index\') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-left mr-1"></i> Back</a></div><div class="d-flex justify-content-between align-items-center"><h4 class="card-title mb-0 font-weight-bold">',
            $content
        );
    }

    // Update form body padding
    $content = str_replace(
        '<div class="card-body">',
        '<div class="card-body p-3">',
        $content
    );

    // Update form footer
    $content = preg_replace(
        '/<div class="card-footer">\s*<button class="btn btn-([^"]+)">([^<]+)<\/button>/',
        '<div class="card-footer bg-light border-top py-2 px-3"><div class="d-flex justify-content-between gap-2"><a href="{{ route(\'$1.index\') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times mr-1"></i>Cancel</a><button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i>$2</button>',
        $content
    );

    // Add styles section if not exists
    if (strpos($content, '@section(\'styles\')') === false) {
        $content = str_replace(
            '@endsection',
            '@endsection

@section(\'styles\')
@include(\'components.form-styles\')
@endsection',
            $content,
            1
        );
    }

    file_put_contents($filePath, $content);
    $updatedCount++;
    echo "Updated: " . basename(dirname($filePath)) . "/" . $file->getFilename() . "\n";
}

echo "\nTotal updated: $updatedCount files\n";
?>
