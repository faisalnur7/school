#!/usr/bin/env php
<?php
/**
 * Final Comprehensive Form Update Script
 * Handles all remaining forms including those with custom structures
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

echo "Processing " . count($formFiles) . " form files...\n\n";

$updated = 0;
$skipped = 0;

foreach ($formFiles as $filePath) {
    $content = file_get_contents($filePath);
    
    // Skip if already has modern styling
    if (strpos($content, 'bg-gradient-primary') !== false || strpos($content, 'form-styles') !== false) {
        $skipped++;
        continue;
    }

    // Skip empty files
    if (strlen($content) < 50) {
        $skipped++;
        continue;
    }

    // Add form-styles to existing forms
    if (strpos($content, '@section(\'styles\')') === false && strpos($content, '@endsection') !== false) {
        // Add styles section before last @endsection
        $content = preg_replace(
            '/(@endsection)(?!.*@endsection)/s',
            "@section('styles')\n@include('components.form-styles')\n@endsection\n\n$1",
            $content,
            1
        );
        
        file_put_contents($filePath, $content);
        $updated++;
        
        $relPath = str_replace('/home/faisal/Sites/school/', '', $filePath);
        echo "✓ Enhanced: $relPath\n";
    } else {
        $skipped++;
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Summary:\n";
echo "  Enhanced: $updated files\n";
echo "  Skipped: $skipped files\n";
echo "  Total: " . ($updated + $skipped) . " files\n";
echo str_repeat('=', 60) . "\n";
?>
