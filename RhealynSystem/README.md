# 📅 File-Based Calendar System

A lightweight, file-based calendar management system built with PHP and XAMPP. Store events with dates, retrieve them efficiently, and manage your schedule with ease.

## ✨ Features

- **Add Events**: Create events with title, description, date, time, and category
- **View Events**: Display all events in a clean, organized interface
- **Date-Based Search**: Filter events by specific dates
- **Category Filter**: Organize events by custom categories
- **Edit Events**: Update event details anytime
- **Delete Events**: Remove events you no longer need
- **File Attachments**: Attach files to events
- **Responsive Design**: Works seamlessly on desktop and mobile
- **Statistics Dashboard**: See total events, showing events, and categories count

## 📋 Requirements

- XAMPP (or any local PHP server with Apache)
- PHP 7.0 or higher
- Web browser with JavaScript enabled

## 🚀 Installation & Setup

### 1. Start XAMPP
- Open XAMPP Control Panel
- Start Apache server
- Start MySQL (optional - not required for this system)

### 2. File Placement
The system is already set up in: `C:\xampp\htdocs\RhealynSystem\`

### 3. Access the Application
- Open your browser
- Navigate to: `http://localhost/RhealynSystem/`

### 4. File Structure
```
RhealynSystem/
├── index.php           # Main application file (all-in-one)
├── events.json         # Data file storing all events
├── attachments/        # Created automatically for uploaded files
└── README.md          # Documentation
```

## 📖 How to Use

### Adding an Event
1. Fill in the **Event Title** (required)
2. Add a **Description** (optional)
3. Select **Date** (required) and **Time** (optional)
4. Choose or create a **Category** (defaults to "General")
5. Optionally upload an **Attachment**
6. Click **Create Event**

### Viewing Events
- All events are displayed in the main list sorted by date and time
- Each event shows:
  - Title, Date, Time
  - Category badge
  - Description
  - Attachment link (if available)

### Searching Events

#### By Date
1. Use the **Filter by Date** dropdown
2. Select a date
3. Only events on that date will be shown

#### By Category
1. Use the **Filter by Category** dropdown
2. Select a category
3. Only events in that category will be shown

### Editing an Event
1. Click the **Edit** button on any event
2. Update the fields in the modal
3. Click **Save Changes**

### Deleting an Event
1. Click the **Delete** button on any event
2. Confirm the deletion
3. Event will be permanently removed

## 💾 Data Storage

### events.json Structure
Events are stored in JSON format for easy access and portability:

```json
[
  {
    "id": "event_1234567890.1234",
    "title": "Team Meeting",
    "description": "Weekly sync with the team",
    "date": "2024-05-26",
    "time": "14:30",
    "category": "Work",
    "created_at": "2024-05-26 10:15:30",
    "attachment": "attachments/1234567890_document.pdf"
  },
  {
    "id": "event_9876543210.5678",
    "title": "Birthday Party",
    "description": "Celebrating John's birthday",
    "date": "2024-06-15",
    "time": "18:00",
    "category": "Personal",
    "created_at": "2024-05-26 11:20:45",
    "attachment": ""
  }
]
```

### File Permissions
- `events.json` must be writable by the web server
- `attachments/` folder must exist and be writable
- Both are automatically created if missing

## 🔒 Security Notes

- All user inputs are sanitized using `htmlspecialchars()`
- File uploads are stored separately in `attachments/` folder
- File types are not restricted (consider adding validation if needed)
- Recommended: Use authentication for multi-user environments

## 🎨 Customization

### Adding More Fields
Edit the form section in `index.php` and add fields to the POST handling.

### Changing Colors
Modify the CSS gradients:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Adding Categories
Categories are created dynamically as you add events. Just type a new category name when creating an event.

## 🐛 Troubleshooting

### "attachments" folder not created
- Check PHP permissions
- Manually create `attachments/` folder in the RhealynSystem directory

### Events not saving
- Verify `events.json` is writable
- Check Apache error logs in XAMPP

### File uploads not working
- Ensure `attachments/` folder exists
- Check PHP `upload_max_filesize` setting in `php.ini`

### Events not displaying
- Clear browser cache (Ctrl+F5)
- Verify `events.json` contains valid JSON

## 📊 Statistics Dashboard

The dashboard at the top shows:
- **Total Events**: All events in the system
- **Showing**: Events currently displayed (after filters)
- **Categories**: Number of unique categories used

## 🔄 Backup & Export

### Manual Backup
1. Copy `events.json` to a safe location
2. Copy `attachments/` folder if you have file uploads

### Restore
1. Replace `events.json` with backup
2. Restore `attachments/` folder

## 📱 Responsive Design

The application automatically adjusts to:
- Desktop screens (1200px+)
- Tablets (768px - 1199px)
- Mobile phones (< 768px)

## 🚀 Performance Tips

- For large numbers of events (1000+), consider adding pagination
- Archive old events periodically to keep JSON file size manageable
- Use date filters to reduce displayed events

## 📝 License

This calendar system is free to use and modify.

## 🤝 Support

For issues or questions:
1. Check the Troubleshooting section
2. Verify XAMPP is running properly
3. Check browser console for JavaScript errors

---

**Happy scheduling!** 🎉
