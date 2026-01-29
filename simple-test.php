<?php
echo "Testing basic PHP execution...\n";

// Test if the file exists
if (file_exists('includes/azampay-integration.php')) {
    echo "✅ AzamPay integration file found\n";
} else {
    echo "❌ AzamPay integration file not found\n";
}

// Test basic class loading
try {
    require_once 'includes/azampay-integration.php';
    echo "✅ File included successfully\n";
} catch (Exception $e) {
    echo "❌ Error including file: " . $e->getMessage() . "\n";
}

echo "Done.\n";
?>

