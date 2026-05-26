<?php
/**
 * Calendar System Configuration
 * 
 * Basic configuration settings for the calendar application
 */

// Application Settings
define('APP_NAME', 'Calendar Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/RhealynSystem/');

// File Paths
define('BASE_DIR', __DIR__);
define('DATA_DIR', BASE_DIR . '/data');
define('ATTACHMENTS_DIR', BASE_DIR . '/attachments');
define('EVENTS_FILE', BASE_DIR . '/events.json');

// Security Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'txt']);

// Display Settings
define('ITEMS_PER_PAGE', 50);
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Ensure directories exist
if (!is_dir(DATA_DIR)) {
    @mkdir(DATA_DIR, 0755, true);
}

if (!is_dir(ATTACHMENTS_DIR)) {
    @mkdir(ATTACHMENTS_DIR, 0755, true);
}

// Initialize events file if not exists
if (!file_exists(EVENTS_FILE)) {
    file_put_contents(EVENTS_FILE, json_encode([], JSON_PRETTY_PRINT));
}

/**
 * Helper function to load events
 */
function loadEvents() {
    $content = file_get_contents(EVENTS_FILE);
    return json_decode($content, true) ?? [];
}

/**
 * Helper function to save events
 */
function saveEvents($events) {
    file_put_contents(EVENTS_FILE, json_encode($events, JSON_PRETTY_PRINT));
}

/**
 * Generate unique event ID
 */
function generateEventId() {
    return uniqid('event_', true);
}

/**
 * Validate file upload
 */
function validateFileUpload($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_FILE_TYPES)) {
        return false;
    }
    
    return true;
}

/**
 * Filter events by date
 */
function filterEventsByDate($events, $date) {
    return array_filter($events, function($event) use ($date) {
        return $event['date'] === $date;
    });
}

/**
 * Filter events by category
 */
function filterEventsByCategory($events, $category) {
    return array_filter($events, function($event) use ($category) {
        return $event['category'] === $category;
    });
}

/**
 * Get upcoming events (next 7 days)
 */
function getUpcomingEvents($events, $days = 7) {
    $today = date('Y-m-d');
    $future = date('Y-m-d', strtotime("+$days days"));
    
    return array_filter($events, function($event) use ($today, $future) {
        return $event['date'] >= $today && $event['date'] <= $future;
    });
}

/**
 * Get all unique categories
 */
function getCategories($events) {
    $categories = [];
    foreach ($events as $event) {
        if ($event['category'] && !in_array($event['category'], $categories)) {
            $categories[] = $event['category'];
        }
    }
    sort($categories);
    return $categories;
}
?>
