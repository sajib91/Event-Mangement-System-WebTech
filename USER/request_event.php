<?php
require_once dirname(__DIR__) . '/PARTS/path-config.php';
require_once BACKGROUND_WORKER_PATH;
require_once CONFIG_PATH;

// Debug: Log session info
$debugInfo = "Session User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . " | Session Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET') . " | Request Method: " . $_SERVER['REQUEST_METHOD'];

// Check if user is logged in and is a regular user
if (!(isset($_SESSION['user_id']) && isset($_SESSION['role']))) {
    $_SESSION['error_message'] = 'Session validation failed - User not logged in. ' . $debugInfo;
    header("Location: ../index.php");
    exit();
}

$userRole = strtolower(trim($_SESSION['role']));
if ($userRole !== 'user') {
    $_SESSION['error_message'] = 'Access denied - Invalid role. Role value: "' . $_SESSION['role'] . '". ' . $debugInfo;
    header("Location: ../index.php");
    exit();
}

// Function to redirect to index.php
function redirectToIndex() {
    header("Location: ../index.php");
    exit();
}

try {
    $maxImageSize = 5 * 1024 * 1024; // 5MB
    $eventUploadDirFilesystem = dirname(__DIR__) . '/UPLOADS/img/EVENTS/';
    $eventUploadDirWeb = '../UPLOADS/img/EVENTS/';

    // Ensure facility and title columns are TEXT type (not VARCHAR)
    try {
        $checkFacility = $pdo->query("SHOW COLUMNS FROM events WHERE Field='facility'")->fetch(PDO::FETCH_ASSOC);
        if ($checkFacility && strpos(strtolower($checkFacility['Type']), 'varchar') !== false) {
            $pdo->exec("ALTER TABLE events MODIFY COLUMN facility TEXT NOT NULL");
        }
        
        $checkTitle = $pdo->query("SHOW COLUMNS FROM events WHERE Field='title'")->fetch(PDO::FETCH_ASSOC);
        if ($checkTitle && strpos(strtolower($checkTitle['Type']), 'varchar') !== false) {
            $pdo->exec("ALTER TABLE events MODIFY COLUMN title TEXT NOT NULL");
        }
    } catch (PDOException $colError) {
        // Continue if column check/alter fails
    }

    // Ensure events table supports uploaded image path
    try {
        $checkColumnStmt = $pdo->query("SHOW COLUMNS FROM events LIKE 'event_image'");
        if ($checkColumnStmt->rowCount() === 0) {
            $pdo->exec("ALTER TABLE events ADD COLUMN event_image VARCHAR(255) DEFAULT NULL AFTER event_end");
        }
    } catch (PDOException $colError) {
        // Column might already exist or table structure issue - continue anyway
        // The insert will fail with a clear message if there's a real problem
    }

    // Initialize variables for form input values
    $title = isset($_SESSION['request_event_data']['title']) ? $_SESSION['request_event_data']['title'] : '';
    $description = isset($_SESSION['request_event_data']['description']) ? $_SESSION['request_event_data']['description'] : '';
    $facility = isset($_SESSION['request_event_data']['facility']) ? $_SESSION['request_event_data']['facility'] : '';
    $eventStart = isset($_SESSION['request_event_data']['event_start']) ? $_SESSION['request_event_data']['event_start'] : '';
    $eventEnd = isset($_SESSION['request_event_data']['event_end']) ? $_SESSION['request_event_data']['event_end'] : '';

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form data and trim whitespace
        // Using $_POST directly instead of filter_input to avoid length limitations
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $facility = isset($_POST['facility']) ? trim($_POST['facility']) : '';
        $eventStart = isset($_POST['event_start']) ? trim($_POST['event_start']) : '';
        $eventEnd = isset($_POST['event_end']) ? trim($_POST['event_end']) : '';
        $eventImagePath = null;

        // Additional validation
        if (strlen($title) < 5 || strlen($description) < 5) {
            $_SESSION['error_message'] = 'Title and Description must be at least 5 characters long!';
            $_SESSION['request_event_data'] = [
                'title' => $title,
                'description' => $description,
                'facility' => $facility,
                'event_start' => $eventStart,
                'event_end' => $eventEnd
            ];
            header("Location: request_event.php");
            exit();
        }


        // Additional validation
        if (!$title || !$description || !$facility || !$eventStart || !$eventEnd) {
            $_SESSION['error_message'] = 'Please fill in all required fields!';
            $_SESSION['request_event_data'] = [
                'title' => $title,
                'description' => $description,
                'facility' => $facility,
                'event_start' => $eventStart,
                'event_end' => $eventEnd
            ];
            header("Location: request_event.php");
            exit();
        }

        // Validate event start and end dates
        try {
            $startDateTime = new DateTime($eventStart);
            $endDateTime = new DateTime($eventEnd);
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Invalid date/time format. Please use the date picker.';
            $_SESSION['request_event_data'] = [
                'title' => $title,
                'description' => $description,
                'facility' => $facility,
                'event_start' => $eventStart,
                'event_end' => $eventEnd
            ];
            header("Location: request_event.php");
            exit();
        }

        // Check if event start date is past the current time
        try {
            $now = new DateTime();
        } catch (Exception $e) {
            $now = new DateTime('now');
        }
        
        if ($startDateTime <= $now) {
            $_SESSION['error_message'] = 'Event start date must be in the future!';
            $_SESSION['request_event_data'] = [
                'title' => $title,
                'description' => $description,
                'facility' => $facility,
                'event_start' => $eventStart,
                'event_end' => $eventEnd
            ];
            header("Location: request_event.php");
            exit();
        }

        // Check if event end date is before the start date
        if ($startDateTime >= $endDateTime) {
            $_SESSION['error_message'] = 'Event end date must be after event start date!';
            $_SESSION['request_event_data'] = [
                'title' => $title,
                'description' => $description,
                'facility' => $facility,
                'event_start' => $eventStart,
                'event_end' => $eventEnd
            ];
            header("Location: request_event.php");
            exit();
        }

        // Calculate duration in total hours
        $interval = $startDateTime->diff($endDateTime);
        $duration = ($interval->days * 24) + $interval->h + ($interval->i / 60);

        // Handle optional event image upload
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['event_image']['error'] !== UPLOAD_ERR_OK) {
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE => 'Image exceeds server upload limit.',
                    UPLOAD_ERR_FORM_SIZE => 'Image exceeds form upload limit.',
                    UPLOAD_ERR_PARTIAL => 'Image was only partially uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Server temporary folder is missing.',
                    UPLOAD_ERR_CANT_WRITE => 'Server cannot write uploaded file.',
                    UPLOAD_ERR_EXTENSION => 'Upload blocked by a server extension.'
                ];
                $errorCode = $_FILES['event_image']['error'];
                $_SESSION['error_message'] = isset($uploadErrors[$errorCode])
                    ? $uploadErrors[$errorCode]
                    : 'Failed to upload image. Please try again.';
                $_SESSION['request_event_data'] = [
                    'title' => $title,
                    'description' => $description,
                    'facility' => $facility,
                    'event_start' => $eventStart,
                    'event_end' => $eventEnd
                ];
                header("Location: request_event.php");
                exit();
            }

            if (!is_uploaded_file($_FILES['event_image']['tmp_name'])) {
                $_SESSION['error_message'] = 'Invalid upload source detected.';
                $_SESSION['request_event_data'] = [
                    'title' => $title,
                    'description' => $description,
                    'facility' => $facility,
                    'event_start' => $eventStart,
                    'event_end' => $eventEnd
                ];
                header("Location: request_event.php");
                exit();
            }

            if ($_FILES['event_image']['size'] > $maxImageSize) {
                $_SESSION['error_message'] = 'Image must be 5MB or less.';
                $_SESSION['request_event_data'] = [
                    'title' => $title,
                    'description' => $description,
                    'facility' => $facility,
                    'event_start' => $eventStart,
                    'event_end' => $eventEnd
                ];
                header("Location: request_event.php");
                exit();
            }

            $mimeType = '';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo !== false) {
                    $mimeType = finfo_file($finfo, $_FILES['event_image']['tmp_name']);
                    finfo_close($finfo);
                }
            }

            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];

            if (!isset($allowedMimeTypes[$mimeType])) {
                $_SESSION['error_message'] = 'Invalid image format. Allowed: JPG, PNG, GIF, WEBP.';
                $_SESSION['request_event_data'] = [
                    'title' => $title,
                    'description' => $description,
                    'facility' => $facility,
                    'event_start' => $eventStart,
                    'event_end' => $eventEnd
                ];
                header("Location: request_event.php");
                exit();
            }

            if (!is_dir($eventUploadDirFilesystem)) {
                if (!mkdir($eventUploadDirFilesystem, 0777, true) && !is_dir($eventUploadDirFilesystem)) {
                    $_SESSION['error_message'] = 'Cannot create upload directory on server.';
                    $_SESSION['request_event_data'] = [
                        'title' => $title,
                        'description' => $description,
                        'facility' => $facility,
                        'event_start' => $eventStart,
                        'event_end' => $eventEnd
                    ];
                    header("Location: request_event.php");
                    exit();
                }
            }

            if (!is_writable($eventUploadDirFilesystem)) {
                $_SESSION['error_message'] = 'Upload directory is not writable.';
                $_SESSION['request_event_data'] = [
                    'title' => $title,
                    'description' => $description,
                    'facility' => $facility,
                    'event_start' => $eventStart,
                    'event_end' => $eventEnd
                ];
                header("Location: request_event.php");
                exit();
            }

            $fileName = 'event_' . $_SESSION['user_id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $allowedMimeTypes[$mimeType];
            $uploadFileFilesystem = $eventUploadDirFilesystem . $fileName;
            $uploadFileWeb = $eventUploadDirWeb . $fileName;

            if (!move_uploaded_file($_FILES['event_image']['tmp_name'], $uploadFileFilesystem)) {
                $_SESSION['error_message'] = 'Failed to save uploaded image.';
                $_SESSION['request_event_data'] = [
                    'title' => $title,
                    'description' => $description,
                    'facility' => $facility,
                    'event_start' => $eventStart,
                    'event_end' => $eventEnd
                ];
                header("Location: request_event.php");
                exit();
            }

            $eventImagePath = $uploadFileWeb;
        }

        // Insert the event into the database
        try {
            // Debug: Check facility length
            $facilityLength = strlen($facility);
            
            // Debug: Verify column type
            $checkStmt = $pdo->query("SHOW COLUMNS FROM events WHERE Field='facility'");
            $colInfo = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->prepare("INSERT INTO events (user_id, title, description, facility, duration, status, event_start, event_end, event_image) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?)");
            $result = $stmt->execute([$_SESSION['user_id'], $title, $description, $facility, $duration, $eventStart, $eventEnd, $eventImagePath]);
            
            if ($result) {
                $_SESSION['success_message'] = 'Event submitted successfully!';
                header("Location: ../index.php");
                exit();
            }
        } catch (PDOException $e) {
            $debugInfo = "Facility length: " . strlen($facility) . " chars. ";
            $debugInfo .= "DB Column type: " . ($colInfo['Type'] ?? 'unknown') . ". ";
            $_SESSION['error_message'] = 'Database error: ' . $e->getMessage() . ' | ' . $debugInfo;
            header("Location: request_event.php");
            exit();
        }
    }
} catch(Exception $e) {
    // Set error message and continue to show form with error
    $_SESSION['error_message'] = 'An error occurred: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Event</title>

    <!-- CSS.PHP -->
    <?php require '../PARTS/CSS.php'; ?>

    <style>
        .submit-btn {
            background-color: #161c27;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #0d1117;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: scale 00.3s;
            scale: 1.05;
        }
    </style>
</head>
<body>
<!-- Header -->
<?php require '../PARTS/header.php'; ?>
<!-- End Header -->

<div class="container mt-5 flex-grow-1">
    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']); // Clear message after displaying
    }

    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']); // Clear message after displaying
    }
    ?>
    <h2>Request Event</h2>
    <hr style="border: none; height: 4px; background-color: #1c2331;">
    <!-- Event request form -->
    <form action="request_event.php" method="POST" id="eventForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Event Title *</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Event Description *</label>
            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div class="form-group">
            <label for="facility">Facility *</label>
            <input type="text" class="form-control" id="facility" name="facility" value="<?php echo htmlspecialchars($facility); ?>" required>
        </div>
        <div class="form-group">
            <label for="event_start">Event Start Date and Time *</label>
            <input type="datetime-local" class="form-control" id="event_start" name="event_start" value="<?php echo htmlspecialchars($eventStart); ?>" required>
        </div>
        <div class="form-group">
            <label for="event_end">Event End Date and Time *</label>
            <input type="datetime-local" class="form-control" id="event_end" name="event_end" value="<?php echo htmlspecialchars($eventEnd); ?>" required>
        </div>
        <div class="form-group">
            <label for="duration">Duration (in hours)</label>
            <input type="number" class="form-control" id="duration" name="duration" min="1" readonly>
        </div>
        <div class="form-group mt-3">
            <label for="event_image">Event Image (optional)</label>
            <input type="file" class="form-control" id="event_image" name="event_image" accept="image/jpeg,image/png,image/gif,image/webp">
            <small class="text-muted">Upload from your browser. Allowed: JPG, PNG, GIF, WEBP (max 5MB).</small>
        </div>
        <button type="submit" class="btn btn-primary mt-3 submit-btn">Submit Event</button>
    </form>
</div>

<!-- Footer -->
<?php require '../PARTS/footer.php'; ?>

<!-- JS.PHP -->
<?php require '../PARTS/JS.php'; ?>

<script>
    // Function to set min attribute of event_start input to tomorrow's date
    function setMinStartDate() {
        var currentDate = new Date();
        var tomorrowDate = new Date(currentDate.getTime() + (24 * 60 * 60 * 1000));
        // Set min attribute of event start input to tomorrow's date
        document.getElementById("event_start").min = tomorrowDate.toISOString().slice(0, 16);
    }

    // Function to set min attribute of event_end input based on event start date
    function setMinEndDate() {
        var eventStartInput = document.getElementById("event_start");
        var eventEndInput = document.getElementById("event_end");
        // Ensure event end date cannot be before event start date
        if (eventStartInput.value) {
            var startDate = new Date(eventStartInput.value);
            // Set min date for event end to event start date
            eventEndInput.min = startDate.toISOString().slice(0, 16);
        }
        // Calculate duration and fill in the input
        if (eventStartInput.value && eventEndInput.value) {
            var startDateTime = new Date(eventStartInput.value);
            var endDateTime = new Date(eventEndInput.value);
            var durationHours = Math.abs(endDateTime - startDateTime) / 36e5; // Milliseconds to hours
            document.getElementById("duration").value = Math.ceil(durationHours);
        }
    }

    // Event listener to call setMinEndDate function when event start date changes
    var eventStartEl = document.getElementById("event_start");
    if (eventStartEl) {
        eventStartEl.addEventListener("change", function() {
            setMinEndDate();
        });
    }

    // Event listener to call setMinEndDate function when event end date changes
    var eventEndEl = document.getElementById("event_end");
    if (eventEndEl) {
        eventEndEl.addEventListener("change", function() {
            setMinEndDate();
        });
    }

    // Call setMinStartDate function to set min attribute of event_start input
    setMinStartDate();
    // Call setMinEndDate function initially to set min attribute of event_end input
    setMinEndDate();
</script>

</body>
</html>
