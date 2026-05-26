<?php
// Calendar Management System

// File paths
$eventsFile = __DIR__ . '/events.json';
$uploadsDir = __DIR__ . '/attachments';

// Initialize attachments directory
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

// Initialize events file
if (!file_exists($eventsFile)) {
    file_put_contents($eventsFile, json_encode([], JSON_PRETTY_PRINT));
}

// Helper functions
function loadEvents() {
    global $eventsFile;
    $content = file_get_contents($eventsFile);
    return json_decode($content, true) ?? [];
}

function saveEvents($events) {
    global $eventsFile;
    file_put_contents($eventsFile, json_encode($events, JSON_PRETTY_PRINT));
}

function generateEventId() {
    return uniqid('event_', true);
}

// Handle actions
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add_event') {
        $events = loadEvents();
        
        $newEvent = [
            'id' => generateEventId(),
            'title' => htmlspecialchars($_POST['title'] ?? ''),
            'description' => htmlspecialchars($_POST['description'] ?? ''),
            'date' => $_POST['event_date'] ?? '',
            'time' => $_POST['event_time'] ?? '',
            'category' => htmlspecialchars($_POST['category'] ?? 'General'),
            'created_at' => date('Y-m-d H:i:s'),
            'attachment' => ''
        ];
        
        // Handle file upload
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['attachment'];
            $filename = uniqid() . '_' . basename($uploadedFile['name']);
            $filepath = $uploadsDir . '/' . $filename;
            
            if (move_uploaded_file($uploadedFile['tmp_name'], $filepath)) {
                $newEvent['attachment'] = 'attachments/' . $filename;
            }
        }
        
        $events[] = $newEvent;
        saveEvents($events);
        
        $message = 'Event added successfully!';
        $messageType = 'success';
    }
    
    elseif ($action === 'delete_event') {
        $eventId = $_POST['event_id'] ?? '';
        $events = loadEvents();
        
        $eventIndex = array_search($eventId, array_column($events, 'id'));
        if ($eventIndex !== false) {
            $event = $events[$eventIndex];
            
            // Delete attachment if exists
            if ($event['attachment'] && file_exists(__DIR__ . '/' . $event['attachment'])) {
                unlink(__DIR__ . '/' . $event['attachment']);
            }
            
            array_splice($events, $eventIndex, 1);
            saveEvents($events);
            
            $message = 'Event deleted successfully!';
            $messageType = 'success';
        }
    }
    
    elseif ($action === 'update_event') {
        $eventId = $_POST['event_id'] ?? '';
        $events = loadEvents();
        
        $eventIndex = array_search($eventId, array_column($events, 'id'));
        if ($eventIndex !== false) {
            $events[$eventIndex]['title'] = htmlspecialchars($_POST['title'] ?? '');
            $events[$eventIndex]['description'] = htmlspecialchars($_POST['description'] ?? '');
            $events[$eventIndex]['date'] = $_POST['event_date'] ?? '';
            $events[$eventIndex]['time'] = $_POST['event_time'] ?? '';
            $events[$eventIndex]['category'] = htmlspecialchars($_POST['category'] ?? 'General');
            
            saveEvents($events);
            
            $message = 'Event updated successfully!';
            $messageType = 'success';
        }
    }
}

// Get events based on filter
$events = loadEvents();
$searchDate = $_GET['search_date'] ?? '';
$searchCategory = $_GET['search_category'] ?? '';

$filteredEvents = $events;

// Filter by date
if ($searchDate) {
    $filteredEvents = array_filter($filteredEvents, function($event) use ($searchDate) {
        return $event['date'] === $searchDate;
    });
}

// Filter by category
if ($searchCategory) {
    $filteredEvents = array_filter($filteredEvents, function($event) use ($searchCategory) {
        return $event['category'] === $searchCategory;
    });
}

// Sort by date
usort($filteredEvents, function($a, $b) {
    $dateCompare = strcmp($a['date'], $b['date']);
    if ($dateCompare === 0) {
        return strcmp($a['time'], $b['time']);
    }
    return $dateCompare;
});

// Get unique categories
$categories = [];
foreach ($events as $event) {
    if ($event['category'] && !in_array($event['category'], $categories)) {
        $categories[] = $event['category'];
    }
}
sort($categories);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --text-primary: #333;
            --text-secondary: #666;
            --bg-light: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            padding: 20px;
            transition: background 0.8s ease;
        }
        
        /* Weather Themes */
        body.theme-sunny {
            --primary-color: #FF9500;
            --secondary-color: #FFB627;
            --bg-gradient: linear-gradient(135deg, #FFB627 0%, #FF9500 100%);
        }
        
        body.theme-rainy {
            --primary-color: #4a5568;
            --secondary-color: #2d3748;
            --bg-gradient: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
        }
        
        body.theme-cloudy {
            --primary-color: #8b9dc3;
            --secondary-color: #6b7aa8;
            --bg-gradient: linear-gradient(135deg, #8b9dc3 0%, #6b7aa8 100%);
        }
        
        body.theme-snowy {
            --primary-color: #a0d8ff;
            --secondary-color: #64b5f6;
            --bg-gradient: linear-gradient(135deg, #a0d8ff 0%, #64b5f6 100%);
        }
        
        body.theme-stormy {
            --primary-color: #1a202c;
            --secondary-color: #2d3748;
            --bg-gradient: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
        }
        
        body.theme-clear-night {
            --primary-color: #1a237e;
            --secondary-color: #283593;
            --bg-gradient: linear-gradient(135deg, #1a237e 0%, #283593 100%);
        }
        
        body.theme-foggy {
            --primary-color: #9e9e9e;
            --secondary-color: #757575;
            --bg-gradient: linear-gradient(135deg, #9e9e9e 0%, #757575 100%);
        }
        
        body.theme-hot {
            --primary-color: #e53935;
            --secondary-color: #d32f2f;
            --bg-gradient: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 968px) {
            .content {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .card h2 {
            color: var(--text-primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
            font-size: 0.9em;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--bg-gradient);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        
        .message.show {
            display: block;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .filters {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .filters {
                grid-template-columns: 1fr;
            }
        }
        
        .filter-btn {
            background: var(--primary-color);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .filter-btn:hover {
            background: var(--secondary-color);
        }
        
        .event-item {
            background: var(--bg-light);
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .event-item:hover {
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }
        
        .event-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .event-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 10px;
            font-size: 0.9em;
            color: #666;
        }
        
        .event-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 600;
            background: #667eea;
            color: white;
        }
        
        .event-description {
            color: #555;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .event-attachment {
            margin-bottom: 10px;
        }
        
        .event-attachment a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .event-attachment a:hover {
            text-decoration: underline;
        }
        
        .event-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn-small {
            padding: 8px 15px;
            font-size: 0.9em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #333;
        }
        
        .btn-edit:hover {
            background: #e0a800;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .empty-state svg {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.show {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .modal-header h3 {
            color: #333;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
            color: #999;
        }
        
        .close-btn:hover {
            color: #333;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: var(--bg-gradient);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .mini-calendar {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .mini-calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .mini-calendar-header h3 {
            color: #333;
            font-size: 1em;
            margin: 0;
        }
        
        .mini-calendar-nav {
            display: flex;
            gap: 8px;
        }
        
        .mini-calendar-nav button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85em;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .mini-calendar-nav button:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 10px;
        }
        
        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.75em;
            padding: 5px 0;
            text-transform: uppercase;
        }
        
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            font-size: 0.85em;
            cursor: pointer;
            background: white;
            border: 1px solid #e0e0e0;
            transition: all 0.2s;
            font-weight: 500;
            color: #333;
        }
        
        .calendar-day:hover {
            background: #e8e8f0;
            transform: scale(1.1);
        }
        
        .calendar-day.other-month {
            color: #ccc;
            cursor: default;
            background: #fafafa;
        }
        
        .calendar-day.other-month:hover {
            background: #fafafa;
            transform: scale(1);
        }
        
        .calendar-day.today {
            background: var(--primary-color);
            color: white;
            font-weight: 700;
            border-color: var(--primary-color);
        }
        
        .calendar-day.has-event {
            background: var(--bg-gradient);
            color: white;
            font-weight: 700;
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        
        .calendar-day.has-event:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5);
        }
        
        .calendar-day.selected {
            background: #ffc107;
            color: #333;
            border-color: #e0a800;
            font-weight: 700;
        }
        
        .mini-calendar-legend {
            display: flex;
            gap: 15px;
            font-size: 0.75em;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }
        
        .legend-dot.has-event {
            background: var(--bg-gradient);
        }
        
        .legend-dot.today {
            background: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📅 Calendar Management System</h1>
            <p>Organize your events efficiently</p>
            <div id="weather-widget" style="margin-top: 15px; font-size: 0.95em; opacity: 0.9;">
                <span id="weather-info">🌤️ Loading weather...</span>
            </div>
        </header>
        
        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?> show" id="message">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($events); ?></div>
                <div class="stat-label">Total Events</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($filteredEvents); ?></div>
                <div class="stat-label">Showing</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($categories); ?></div>
                <div class="stat-label">Categories</div>
            </div>
        </div>
        
        <div class="content">
            <!-- Add Event Form -->
            <div class="card">
                <h2>➕ Add New Event</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_event">
                    
                    <div class="form-group">
                        <label for="title">Event Title *</label>
                        <input type="text" id="title" name="title" required placeholder="e.g., Team Meeting">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Add event details..."></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="event_date">Date *</label>
                            <input type="date" id="event_date" name="event_date" required>
                        </div>
                        <div class="form-group">
                            <label for="event_time">Time</label>
                            <input type="time" id="event_time" name="event_time">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" id="category" name="category" placeholder="e.g., Work, Personal, Birthday" value="General">
                    </div>
                    
                    <div class="form-group">
                        <label for="attachment">Attachment (Optional)</label>
                        <input type="file" id="attachment" name="attachment">
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn-primary">Create Event</button>
                        <button type="reset" class="btn-secondary">Clear</button>
                    </div>
                </form>
                
                <!-- Mini Calendar -->
                <div class="mini-calendar">
                    <div class="mini-calendar-header">
                        <h3 id="calendar-month-year">May 2026</h3>
                        <div class="mini-calendar-nav">
                            <button onclick="previousMonth()">← Prev</button>
                            <button onclick="nextMonth()">Next →</button>
                        </div>
                    </div>
                    
                    <div class="calendar-grid" id="calendar-grid">
                        <!-- Generated by JavaScript -->
                    </div>
                    
                    <div class="mini-calendar-legend">
                        <div class="legend-item">
                            <div class="legend-dot has-event"></div>
                            <span>Has Event</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot today"></div>
                            <span>Today</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Events List -->
            <div class="card">
                <h2>📋 Events</h2>
                
                <div class="filters">
                    <div class="form-group">
                        <label for="filter_date">Filter by Date:</label>
                        <input type="date" id="filter_date" onchange="filterByDate()">
                    </div>
                    <div class="form-group">
                        <label for="filter_category">Filter by Category:</label>
                        <select id="filter_category" onchange="filterByCategory()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo urlencode($cat); ?>" <?php echo ($searchCategory === $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div id="events-list">
                    <?php if (empty($filteredEvents)): ?>
                    <div class="empty-state">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6-4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        <h3>No events found</h3>
                        <p>Create your first event to get started!</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($filteredEvents as $event): ?>
                        <div class="event-item">
                            <div class="event-header">
                                <div style="flex: 1;">
                                    <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                                    <div class="event-meta">
                                        <span>📅 <?php echo date('M d, Y', strtotime($event['date'])); ?></span>
                                        <?php if ($event['time']): ?>
                                        <span>🕒 <?php echo $event['time']; ?></span>
                                        <?php endif; ?>
                                        <span class="badge"><?php echo htmlspecialchars($event['category']); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($event['description']): ?>
                            <div class="event-description"><?php echo nl2br(htmlspecialchars($event['description'])); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($event['attachment']): ?>
                            <div class="event-attachment">
                                📎 <a href="<?php echo htmlspecialchars($event['attachment']); ?>" target="_blank">View Attachment</a>
                            </div>
                            <?php endif; ?>
                            
                            <div class="event-actions">
                                <button class="btn-small btn-edit" onclick="editEvent('<?php echo htmlspecialchars($event['id']); ?>', '<?php echo htmlspecialchars($event['title']); ?>', '<?php echo htmlspecialchars($event['description']); ?>', '<?php echo htmlspecialchars($event['date']); ?>', '<?php echo htmlspecialchars($event['time']); ?>', '<?php echo htmlspecialchars($event['category']); ?>')">Edit</button>
                                <button class="btn-small btn-delete" onclick="deleteEvent('<?php echo htmlspecialchars($event['id']); ?>', '<?php echo htmlspecialchars($event['title']); ?>')">Delete</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Event</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="update_event">
                <input type="hidden" name="event_id" id="edit_event_id">
                
                <div class="form-group">
                    <label for="edit_title">Event Title</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_date">Date</label>
                        <input type="date" id="edit_date" name="event_date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_time">Time</label>
                        <input type="time" id="edit_time" name="event_time">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_category">Category</label>
                    <input type="text" id="edit_category" name="category">
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function editEvent(id, title, description, date, time, category) {
            document.getElementById('edit_event_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_time').value = time;
            document.getElementById('edit_category').value = category;
            document.getElementById('editModal').classList.add('show');
        }
        
        function closeModal() {
            document.getElementById('editModal').classList.remove('show');
        }
        
        function deleteEvent(id, title) {
            if (confirm('Are you sure you want to delete "' + title + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="action" value="delete_event"><input type="hidden" name="event_id" value="' + id + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function filterByDate() {
            const date = document.getElementById('filter_date').value;
            const url = new URL(window.location);
            if (date) {
                url.searchParams.set('search_date', date);
                url.searchParams.delete('search_category');
            } else {
                url.searchParams.delete('search_date');
            }
            window.location = url.toString();
        }
        
        function filterByCategory() {
            const category = document.getElementById('filter_category').value;
            const url = new URL(window.location);
            if (category) {
                url.searchParams.set('search_category', category);
                url.searchParams.delete('search_date');
            } else {
                url.searchParams.delete('search_category');
            }
            window.location = url.toString();
        }
        
        // Auto-hide messages after 5 seconds
        const message = document.getElementById('message');
        if (message) {
            setTimeout(() => {
                message.classList.remove('show');
            }, 5000);
        }
        
        // Set today's date as default
        document.getElementById('event_date').valueAsDate = new Date();
        
        // Mini Calendar Functionality
        let currentCalendarDate = new Date();
        
        // Get event dates from PHP (all events)
        const allEvents = <?php echo json_encode($events); ?>;
        const eventDates = {};
        
        // Build event dates map
        allEvents.forEach(event => {
            if (!eventDates[event.date]) {
                eventDates[event.date] = 0;
            }
            eventDates[event.date]++;
        });
        
        function renderCalendar() {
            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth();
            
            // Update header
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                              'July', 'August', 'September', 'October', 'November', 'December'];
            document.getElementById('calendar-month-year').textContent = monthNames[month] + ' ' + year;
            
            // Get calendar grid
            const grid = document.getElementById('calendar-grid');
            grid.innerHTML = '';
            
            // Day headers
            const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayHeaders.forEach(day => {
                const header = document.createElement('div');
                header.className = 'calendar-day-header';
                header.textContent = day;
                grid.appendChild(header);
            });
            
            // Get first day of month and number of days
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();
            
            // Previous month days
            for (let i = firstDay - 1; i >= 0; i--) {
                const day = daysInPrevMonth - i;
                const cell = createCalendarDay(day, month - 1, year, true);
                grid.appendChild(cell);
            }
            
            // Current month days
            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const cell = createCalendarDay(day, month, year, false);
                
                // Check if today
                if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    cell.classList.add('today');
                }
                
                // Check if has events
                const dateStr = String(year) + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                if (eventDates[dateStr]) {
                    cell.classList.add('has-event');
                    cell.title = eventDates[dateStr] + ' event(s)';
                }
                
                // Add click handler
                cell.addEventListener('click', function() {
                    document.getElementById('filter_date').value = dateStr;
                    filterByDate();
                });
                
                grid.appendChild(cell);
            }
            
            // Next month days
            const totalCells = grid.children.length - 7; // Subtract day headers
            const remainingCells = 42 - totalCells; // 6 weeks * 7 days
            for (let day = 1; day <= remainingCells; day++) {
                const cell = createCalendarDay(day, month + 1, year, true);
                grid.appendChild(cell);
            }
        }
        
        function createCalendarDay(day, month, year, isOtherMonth) {
            const cell = document.createElement('div');
            cell.className = 'calendar-day';
            if (isOtherMonth) {
                cell.classList.add('other-month');
            }
            cell.textContent = day;
            return cell;
        }
        
        function previousMonth() {
            currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
            renderCalendar();
        }
        
        function nextMonth() {
            currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
            renderCalendar();
        }
        
        // Initial render
        renderCalendar();
        
        // Weather-based Theme System
        function getWeatherTheme(weatherCode, isNight) {
            // WMO Weather interpretation codes
            // Clear sky
            if (weatherCode === 0) return isNight ? 'theme-clear-night' : 'theme-sunny';
            
            // Mainly clear, partly cloudy
            if (weatherCode === 1 || weatherCode === 2) return 'theme-cloudy';
            
            // Overcast
            if (weatherCode === 3) return 'theme-cloudy';
            
            // Fog
            if (weatherCode === 45 || weatherCode === 48) return 'theme-foggy';
            
            // Drizzle or Light rain
            if (weatherCode === 51 || weatherCode === 53 || weatherCode === 55 || weatherCode === 80 || weatherCode === 81) return 'theme-rainy';
            
            // Rain
            if (weatherCode === 61 || weatherCode === 63 || weatherCode === 65 || weatherCode === 82) return 'theme-rainy';
            
            // Snow
            if (weatherCode === 71 || weatherCode === 73 || weatherCode === 75 || weatherCode === 77 || weatherCode === 85 || weatherCode === 86) return 'theme-snowy';
            
            // Thunderstorm
            if (weatherCode === 80 || weatherCode === 81 || weatherCode === 82 || weatherCode === 95 || weatherCode === 96 || weatherCode === 99) return 'theme-stormy';
            
            // Hot/Sunny (high temperature)
            return 'theme-sunny';
        }
        
        function applyWeatherTheme(weatherCode, isNight, temperature) {
            const theme = getWeatherTheme(weatherCode, isNight);
            document.body.className = theme;
            
            // Get emoji and description
            const weatherInfo = getWeatherInfo(weatherCode, isNight, temperature);
            document.getElementById('weather-info').innerHTML = weatherInfo;
        }
        
        function getWeatherInfo(weatherCode, isNight, temperature) {
            let emoji = '🌤️';
            let description = 'Clear';
            
            if (weatherCode === 0) {
                emoji = isNight ? '🌙' : '☀️';
                description = isNight ? 'Clear Night' : 'Sunny';
            } else if (weatherCode === 1 || weatherCode === 2) {
                emoji = isNight ? '🌤️' : '⛅';
                description = 'Partly Cloudy';
            } else if (weatherCode === 3) {
                emoji = '☁️';
                description = 'Overcast';
            } else if (weatherCode === 45 || weatherCode === 48) {
                emoji = '🌫️';
                description = 'Foggy';
            } else if (weatherCode === 51 || weatherCode === 53 || weatherCode === 55) {
                emoji = '🌦️';
                description = 'Drizzle';
            } else if (weatherCode === 61 || weatherCode === 63 || weatherCode === 65) {
                emoji = '🌧️';
                description = 'Rainy';
            } else if (weatherCode === 71 || weatherCode === 73 || weatherCode === 75 || weatherCode === 77 || weatherCode === 85 || weatherCode === 86) {
                emoji = '❄️';
                description = 'Snowy';
            } else if (weatherCode === 80 || weatherCode === 81 || weatherCode === 82) {
                emoji = '⛈️';
                description = 'Shower';
            } else if (weatherCode === 95 || weatherCode === 96 || weatherCode === 99) {
                emoji = '⚡';
                description = 'Thunderstorm';
            }
            
            const tempText = temperature ? `${Math.round(temperature)}°C` : '';
            return `${emoji} ${description} ${tempText}`;
        }
        
        // Fetch weather data using Open-Meteo API (free, no key required)
        function fetchWeatherAndApplyTheme() {
            if (!navigator.geolocation) {
                console.log('Geolocation not supported, using default theme');
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    // Using Open-Meteo free API
                    fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=weather_code,temperature,is_day&timezone=auto`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.current) {
                                const weatherCode = data.current.weather_code;
                                const isDay = data.current.is_day;
                                const temperature = data.current.temperature;
                                applyWeatherTheme(weatherCode, isDay, temperature);
                            }
                        })
                        .catch(error => {
                            console.log('Weather fetch failed:', error);
                            // Default theme if API fails
                        });
                },
                function(error) {
                    console.log('Location access denied or unavailable:', error.message);
                    // User denied geolocation, use default theme
                }
            );
        }
        
        // Load weather theme on page load
        window.addEventListener('load', fetchWeatherAndApplyTheme);
    </script>
</body>
</html>
