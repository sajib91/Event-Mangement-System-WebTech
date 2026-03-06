<?php
require_once dirname(__DIR__) . '/PARTS/path-config.php';
require_once CONFIG_PATH;

echo "<!DOCTYPE html>";
echo "<html><head><title>Form Test</title></head><body>";
echo "<h1>Event Submission Test</h1>";

// Test 1: Check database connection
try {
    $pdo->query("SELECT 1");
    echo "<p style='color: green;'>✓ Database connected</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test 2: Verify database schema
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM events");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p style='color: green;'>✓ Events table structure verified</p>";
    echo "<p>Columns: " . implode(", ", $columns) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Table error: " . $e->getMessage() . "</p>";
}

// Test 3: Check file permissions
$uploadDir = dirname(__DIR__) . '/UPLOADS/img/EVENTS/';
echo "<p>";
echo (is_dir($uploadDir) ? "✓" : "✗") . " Upload directory exists<br>";
echo (is_writable($uploadDir) ? "✓" : "✗") . " Upload directory writable";
echo "</p>";

// Test 4: Check session
echo "<p>";
echo (isset($_SESSION['user_id']) ? "✓" : "✗") . " User ID in session: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
echo (isset($_SESSION['username']) ? "✓" : "✗") . " Username in session: " . ($_SESSION['username'] ?? 'NOT SET') . "<br>";
echo (isset($_SESSION['role']) ? "✓" : "✗") . " Role in session: " . ($_SESSION['role'] ?? 'NOT SET');
echo "</p>";

// Test 5: Try a test insert
if (isset($_POST['test'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO events (user_id, title, description, facility, duration, status, event_start, event_end) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)");
        
        $testStart = date('Y-m-d H:i:s', strtotime('+1 day'));
        $testEnd = date('Y-m-d H:i:s', strtotime('+2 days'));
        
        $result = $stmt->execute([
            $_SESSION['user_id'],
            "Test Event Title - " . time(),
            "This is a test description that is longer than 5 characters",
            "This is a test facility name that could be very long and have lots of text in it so we can verify it works properly",
            24,
            $testStart,
            $testEnd
        ]);
        
        echo "<p style='color: green;'>✓ Test insert successful! Event ID: " . $pdo->lastInsertId() . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Test insert failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<form method='POST'>";
    echo "<button type='submit' name='test'>Test Database Insert</button>";
    echo "</form>";
}

echo "<p><a href='request_event.php'>Go to Request Event Form</a></p>";
echo "</body></html>";
?>
