# 🔌 Calendar API Documentation

This document describes how to interact with the calendar system programmatically.

## File-Based Data Structure

### Events File Location
```
events.json (in the root RhealynSystem directory)
```

### Event Object Structure
```json
{
  "id": "event_1234567890.1234",
  "title": "Event Name",
  "description": "Event description",
  "date": "2024-05-26",
  "time": "14:30",
  "category": "Work",
  "created_at": "2024-05-26 10:15:30",
  "attachment": "attachments/filename.pdf"
}
```

## Using Helper Functions

Include the config file to access helper functions:

```php
<?php
require_once 'config.php';

// Load all events
$events = loadEvents();

// Save events
saveEvents($events);

// Generate event ID
$id = generateEventId();

// Get categories
$categories = getCategories($events);

// Filter by date
$dateEvents = filterEventsByDate($events, '2024-05-26');

// Filter by category
$categoryEvents = filterEventsByCategory($events, 'Work');

// Get upcoming events
$upcoming = getUpcomingEvents($events, 7);
?>
```

## Direct JSON Manipulation

### Read Events
```php
<?php
$json = file_get_contents('events.json');
$events = json_decode($json, true);

// Access specific event
echo $events[0]['title'];
?>
```

### Add Event
```php
<?php
$events = json_decode(file_get_contents('events.json'), true);

$newEvent = [
    'id' => uniqid('event_', true),
    'title' => 'My Event',
    'description' => 'Description',
    'date' => '2024-05-26',
    'time' => '14:30',
    'category' => 'Personal',
    'created_at' => date('Y-m-d H:i:s'),
    'attachment' => ''
];

$events[] = $newEvent;
file_put_contents('events.json', json_encode($events, JSON_PRETTY_PRINT));
?>
```

### Update Event
```php
<?php
$events = json_decode(file_get_contents('events.json'), true);

// Find event by ID
$eventId = 'event_1234567890.1234';
$key = array_search($eventId, array_column($events, 'id'));

if ($key !== false) {
    $events[$key]['title'] = 'Updated Title';
    $events[$key]['date'] = '2024-05-27';
    
    file_put_contents('events.json', json_encode($events, JSON_PRETTY_PRINT));
}
?>
```

### Delete Event
```php
<?php
$events = json_decode(file_get_contents('events.json'), true);

// Find event by ID
$eventId = 'event_1234567890.1234';
$key = array_search($eventId, array_column($events, 'id'));

if ($key !== false) {
    // Delete attachment if exists
    if ($events[$key]['attachment'] && file_exists($events[$key]['attachment'])) {
        unlink($events[$key]['attachment']);
    }
    
    unset($events[$key]);
    $events = array_values($events); // Re-index array
    
    file_put_contents('events.json', json_encode($events, JSON_PRETTY_PRINT));
}
?>
```

## Available Configuration Constants

```php
define('APP_NAME', 'Calendar Management System');
define('APP_VERSION', '1.0.0');
define('BASE_DIR', __DIR__);
define('ATTACHMENTS_DIR', BASE_DIR . '/attachments');
define('EVENTS_FILE', BASE_DIR . '/events.json');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'txt']);
```

## Query Examples

### Get events for a specific month
```php
<?php
function getEventsForMonth($events, $year, $month) {
    return array_filter($events, function($event) use ($year, $month) {
        return substr($event['date'], 0, 7) === sprintf('%04d-%02d', $year, $month);
    });
}

$mayEvents = getEventsForMonth($events, 2024, 5);
?>
```

### Get events by year
```php
<?php
function getEventsForYear($events, $year) {
    return array_filter($events, function($event) use ($year) {
        return substr($event['date'], 0, 4) === (string)$year;
    });
}

$2024Events = getEventsForYear($events, 2024);
?>
```

### Search events by keyword
```php
<?php
function searchEvents($events, $keyword) {
    $keyword = strtolower($keyword);
    return array_filter($events, function($event) use ($keyword) {
        return strpos(strtolower($event['title']), $keyword) !== false ||
               strpos(strtolower($event['description']), $keyword) !== false;
    });
}

$results = searchEvents($events, 'meeting');
?>
```

### Get events in date range
```php
<?php
function getEventsInRange($events, $startDate, $endDate) {
    return array_filter($events, function($event) use ($startDate, $endDate) {
        return $event['date'] >= $startDate && $event['date'] <= $endDate;
    });
}

$rangeEvents = getEventsInRange($events, '2024-05-01', '2024-05-31');
?>
```

## File Attachment Handling

### Directory Structure
```
attachments/
├── 1234567890_document.pdf
├── 1234567891_image.jpg
└── 1234567892_spreadsheet.xlsx
```

### Upload File
```php
<?php
if (isset($_FILES['attachment'])) {
    $file = $_FILES['attachment'];
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = 'attachments/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $attachmentPath = 'attachments/' . $filename;
        // Save $attachmentPath to event
    }
}
?>
```

### Delete Attachment
```php
<?php
$attachmentPath = 'attachments/1234567890_document.pdf';
if (file_exists($attachmentPath)) {
    unlink($attachmentPath);
}
?>
```

## Backup & Restore

### Create Backup
```php
<?php
$timestamp = date('Y-m-d_H-i-s');
$backupFile = 'backups/events_' . $timestamp . '.json';

if (!is_dir('backups')) {
    mkdir('backups', 0755, true);
}

copy('events.json', $backupFile);
?>
```

### List Backups
```php
<?php
$backups = glob('backups/events_*.json');
sort($backups);
rsort($backups); // Latest first

foreach ($backups as $backup) {
    echo $backup . "\n";
}
?>
```

### Restore from Backup
```php
<?php
$backupFile = 'backups/events_2024-05-26_10-00-00.json';
copy($backupFile, 'events.json');
?>
```

## Export Functions

### Export as CSV
```php
<?php
function exportToCSV($events) {
    $csv = "Title,Date,Time,Category,Description\n";
    
    foreach ($events as $event) {
        $csv .= '"' . $event['title'] . '",';
        $csv .= '"' . $event['date'] . '",';
        $csv .= '"' . $event['time'] . '",';
        $csv .= '"' . $event['category'] . '",';
        $csv .= '"' . str_replace('"', '""', $event['description']) . "\"\n";
    }
    
    return $csv;
}

$csv = exportToCSV($events);
file_put_contents('export_' . date('Y-m-d_H-i-s') . '.csv', $csv);
?>
```

### Export as ICS (iCalendar format)
```php
<?php
function exportToICS($events) {
    $ics = "BEGIN:VCALENDAR\n";
    $ics .= "VERSION:2.0\n";
    $ics .= "PRODID:-//Calendar System//EN\n";
    $ics .= "CALSCALE:GREGORIAN\n";
    
    foreach ($events as $event) {
        $ics .= "BEGIN:VEVENT\n";
        $ics .= "UID:" . $event['id'] . "\n";
        $ics .= "DTSTAMP:" . date('Ymd\THis\Z', strtotime($event['created_at'])) . "\n";
        $ics .= "DTSTART:" . str_replace('-', '', $event['date']) . "\n";
        $ics .= "SUMMARY:" . $event['title'] . "\n";
        $ics .= "DESCRIPTION:" . $event['description'] . "\n";
        $ics .= "CATEGORIES:" . $event['category'] . "\n";
        $ics .= "END:VEVENT\n";
    }
    
    $ics .= "END:VCALENDAR";
    return $ics;
}

$ics = exportToICS($events);
file_put_contents('export_' . date('Y-m-d_H-i-s') . '.ics', $ics);
?>
```

## Statistics

### Get event statistics
```php
<?php
function getStatistics($events) {
    $stats = [
        'total' => count($events),
        'by_category' => [],
        'by_month' => [],
        'upcoming' => 0
    ];
    
    foreach ($events as $event) {
        // By category
        if (!isset($stats['by_category'][$event['category']])) {
            $stats['by_category'][$event['category']] = 0;
        }
        $stats['by_category'][$event['category']]++;
        
        // By month
        $month = substr($event['date'], 0, 7);
        if (!isset($stats['by_month'][$month])) {
            $stats['by_month'][$month] = 0;
        }
        $stats['by_month'][$month]++;
        
        // Upcoming
        if ($event['date'] >= date('Y-m-d')) {
            $stats['upcoming']++;
        }
    }
    
    return $stats;
}

$stats = getStatistics($events);
?>
```

---

**For more information, see README.md**
