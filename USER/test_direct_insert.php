<?php
require_once dirname(__DIR__) . '/PARTS/path-config.php';
require_once CONFIG_PATH;

echo "<h1>Direct Database Insert Test</h1>";
echo "<pre>";

// Test facility with various lengths
$testFacilities = [
    "Short Facility",
    str_repeat("A", 100) . " - 100 chars",
    str_repeat("B", 500) . " - 500 chars",
    str_repeat("C", 1000) . " - 1000 chars"
];

foreach ($testFacilities as $index => $testFacility) {
    echo "\n========== Test " . ($index + 1) . " ==========\n";
    echo "Facility length: " . strlen($testFacility) . " characters\n";
    
    try {
        // First check the column type
        $checkStmt = $pdo->query("SHOW COLUMNS FROM events WHERE Field='facility'");
        $colInfo = $checkStmt->fetch(PDO::FETCH_ASSOC);
        echo "Current DB column type: " . $colInfo['Type'] . "\n";
        
        // Try the insert
        $stmt = $pdo->prepare("INSERT INTO events (user_id, title, description, facility, duration, status, event_start, event_end) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)");
        
        $result = $stmt->execute([
            $_SESSION['user_id'] ?? 2,
            "Test Title " . time(),
            "Test description",
            $testFacility,
            24,
            date('Y-m-d H:i:s', strtotime('+1 day')),
            date('Y-m-d H:i:s', strtotime('+2 days'))
        ]);
        
        if ($result) {
            echo "✓ SUCCESS - Inserted with ID: " . $pdo->lastInsertId() . "\n";
        }
    } catch (PDOException $e) {
        echo "✗ FAILED - Error: " . $e->getMessage() . "\n";
        echo "Error Code: " . $e->getCode() . "\n";
        
        // Check if it's a string truncation error
        if (strpos($e->getMessage(), '22001') !== false || strpos($e->getMessage(), 'Data too long') !== false) {
            echo "\nDEBUG: This is a string truncation error!\n";
            echo "The database thinks facility column is limited.\n";
            
            // Try to alter it again
            echo "\nAttempting to alter column again...\n";
            try {
                $pdo->exec("ALTER TABLE events MODIFY facility TEXT NOT NULL");
                echo "✓ Column altered successfully\n";
            } catch (PDOException $alterError) {
                echo "✗ Alter failed: " . $alterError->getMessage() . "\n";
            }
        }
    }
}

echo "\n========== Final Schema Check ==========\n";
$finalCheck = $pdo->query("SHOW CREATE TABLE events");
$createTable = $finalCheck->fetch(PDO::FETCH_ASSOC);
echo $createTable['Create Table'];

echo "</pre>";
echo "<p><a href='request_event.php'>Back to Request Event Form</a></p>";
?>
