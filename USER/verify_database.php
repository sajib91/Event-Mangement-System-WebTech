<?php
require_once dirname(__DIR__) . '/PARTS/path-config.php';
require_once CONFIG_PATH;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Verification</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .status { padding: 20px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        pre { background: white; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h1 { color: #333; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔍 Database Schema Verification</h1>
    
    <?php
    try {
        // Check facility column
        $facilityCheck = $pdo->query("SHOW COLUMNS FROM events WHERE Field='facility'")->fetch(PDO::FETCH_ASSOC);
        
        if ($facilityCheck) {
            if (strtolower($facilityCheck['Type']) === 'text') {
                echo '<div class="status success">';
                echo '<strong>✓ FACILITY COLUMN:</strong> Correctly set to TEXT (unlimited length)';
                echo '</div>';
            } else {
                echo '<div class="status error">';
                echo '<strong>✗ FACILITY COLUMN:</strong> Still ' . htmlspecialchars($facilityCheck['Type']) . ' (should be TEXT)';
                echo '</div>';
            }
        }
        
        // Check title column
        $titleCheck = $pdo->query("SHOW COLUMNS FROM events WHERE Field='title'")->fetch(PDO::FETCH_ASSOC);
        
        if ($titleCheck) {
            if (strtolower($titleCheck['Type']) === 'text') {
                echo '<div class="status success">';
                echo '<strong>✓ TITLE COLUMN:</strong> Correctly set to TEXT (unlimited length)';
                echo '</div>';
            } else {
                echo '<div class="status error">';
                echo '<strong>✗ TITLE COLUMN:</strong> Still ' . htmlspecialchars($titleCheck['Type']) . ' (should be TEXT)';
                echo '</div>';
            }
        }
        
        // Check event_image column
        $imageCheck = $pdo->query("SHOW COLUMNS FROM events WHERE Field='event_image'")->fetch(PDO::FETCH_ASSOC);
        
        if ($imageCheck) {
            echo '<div class="status success">';
            echo '<strong>✓ EVENT_IMAGE COLUMN:</strong> Exists (' . htmlspecialchars($imageCheck['Type']) . ')';
            echo '</div>';
        } else {
            echo '<div class="status error">';
            echo '<strong>✗ EVENT_IMAGE COLUMN:</strong> Missing';
            echo '</div>';
        }
        
        // Show full table structure
        echo '<h2>Complete Events Table Structure:</h2>';
        echo '<pre>';
        $stmt = $pdo->query("DESCRIBE events");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            printf("%-20s %-30s %s\n", 
                $col['Field'], 
                $col['Type'], 
                $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL'
            );
        }
        echo '</pre>';
        
        // Test message
        if (strtolower($facilityCheck['Type']) === 'text' && strtolower($titleCheck['Type']) === 'text') {
            echo '<div class="info">';
            echo '<strong>✓ ALL CHECKS PASSED!</strong><br>';
            echo 'Your database is correctly configured. You can now submit events with long facility names.<br><br>';
            echo '<a href="request_event.php" style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">Go to Request Event Form →</a>';
            echo '</div>';
        }
        
    } catch (PDOException $e) {
        echo '<div class="status error">';
        echo '<strong>✗ DATABASE ERROR:</strong> ' . htmlspecialchars($e->getMessage());
        echo '</div>';
    }
    ?>
    
</body>
</html>
