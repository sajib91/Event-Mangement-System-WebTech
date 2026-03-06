<?php
require_once dirname(__DIR__) . '/PARTS/path-config.php';
require_once CONFIG_PATH;

echo "<h1>Form Submission Diagnostic</h1>";
echo "<pre>";

// Check 1: Session data
echo "=== SESSION DATA ===\n";
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "\n";
echo "Username: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'NOT SET') . "\n";
echo "Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET') . "\n";
echo "Role (lowercase): " . (isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : 'NOT SET') . "\n\n";

// Check 2: Database connection
echo "=== DATABASE CONNECTION ===\n";
try {
    $result = $pdo->query("SELECT 1");
    echo "✓ Database connected successfully\n\n";
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n\n";
}

// Check 3: Events table
echo "=== EVENTS TABLE ===\n";
try {
    $stmt = $pdo->query("DESCRIBE events");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Events table exists\n";
    echo "Columns: " . implode(", ", $columns) . "\n\n";
} catch (PDOException $e) {
    echo "✗ Events table error: " . $e->getMessage() . "\n\n";
}

// Check 4: Users table
echo "=== USERS TABLE ===\n";
try {
    $stmt = $pdo->query("SELECT * FROM users WHERE id = " . intval($_SESSION['user_id']) ?? 0);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Users table query successful\n";
    echo "Current User: " . json_encode($user, JSON_PRETTY_PRINT) . "\n\n";
} catch (PDOException $e) {
    echo "✗ Users table error: " . $e->getMessage() . "\n\n";
}

// Check 5: Upload directory
echo "=== UPLOAD DIRECTORY ===\n";
$uploadDir = dirname(__DIR__) . '/UPLOADS/img/EVENTS/';
echo "Path: " . $uploadDir . "\n";
echo "Exists: " . (is_dir($uploadDir) ? "✓ YES" : "✗ NO") . "\n";
echo "Writable: " . (is_writable($uploadDir) ? "✓ YES" : "✗ NO") . "\n\n";

// Check 6: Test insert
echo "=== TEST INSERT ===\n";
if (isset($_POST['test_insert'])) {
    try {
        $testTitle = "Test Event " . time();
        $testDesc = "Test Description";
        $testFacility = "Test Facility";
        $testStart = date('Y-m-d H:i:s', strtotime('+1 day'));
        $testEnd = date('Y-m-d H:i:s', strtotime('+2 days'));
        
        $stmt = $pdo->prepare("INSERT INTO events (user_id, title, description, facility, duration, status, event_start, event_end, event_image) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?)");
        $result = $stmt->execute([$_SESSION['user_id'], $testTitle, $testDesc, $testFacility, 24, $testStart, $testEnd, null]);
        
        echo "✓ Test insert successful!\n";
        echo "Test details:\n";
        echo "  Title: " . $testTitle . "\n";
        echo "  Start: " . $testStart . "\n";
        echo "  End: " . $testEnd . "\n";
    } catch (PDOException $e) {
        echo "✗ Test insert failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "Click button below to test database insert\n";
}

echo "</pre>";

if (isset($_POST['test_insert'])) {
    echo "<p><a href='test_diagnostic.php'>Back</a></p>";
} else {
    echo "<form method='POST'>";
    echo "<button type='submit' name='test_insert'>Test Database Insert</button>";
    echo "</form>";
}
?>
