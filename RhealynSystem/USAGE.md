# 🎯 Calendar System - Usage Summary

## Quick Access

| Purpose | Link |
|---------|------|
| **Open Calendar** | http://localhost/RhealynSystem/ |
| **Check Installation** | http://localhost/RhealynSystem/verify.php |
| **View Documentation** | http://localhost/RhealynSystem/README.md |

## System Files Reference

### Application Files
- **index.php** - Main application (1 file = everything you need)
- **config.php** - Reusable functions and configuration
- **verify.php** - Installation verification tool

### Data Files
- **events.json** - Your event storage (auto-created)
- **events.sample.json** - Sample data for testing
- **attachments/** - Uploaded files storage (auto-created)

### Documentation
- **README.md** - Full documentation (start here)
- **QUICKSTART.md** - 5-minute quick start
- **API_DOCS.md** - For developers
- **INSTALL_COMPLETE.md** - Setup verification

## Core Features

### ➕ Adding Events
```
Title *        → Event name (required)
Description    → Optional details
Date *         → Select date (required)
Time          → Optional time
Category      → Tag/label for organization
Attachment    → Upload file (optional)
```

### 🔍 Finding Events
```
Filter by Date     → Show events on specific date
Filter by Category → Show events in category
Search Keywords    → Find by title/description
```

### ✏️ Managing Events
```
Edit   → Update any event details
Delete → Remove event permanently
```

## Usage Scenarios

### Scenario 1: Basic Event Management
1. Open http://localhost/RhealynSystem/
2. Enter "Team Meeting" as title
3. Select today's date
4. Click "Create Event"
5. Event appears in the list below

### Scenario 2: Find Events by Date
1. Click on "Filter by Date" dropdown
2. Select a specific date
3. Only events on that date show
4. Click the date field again to clear filter

### Scenario 3: Organize by Category
1. While creating events, use categories like:
   - Work
   - Personal
   - Health
   - Birthday
   - Shopping
2. Use the "Filter by Category" to group events

### Scenario 4: Upload Documents
1. Create an event
2. Under "Attachment", select a file
3. File is uploaded when event is created
4. Click attachment link to view

### Scenario 5: Edit Event Details
1. Click "Edit" button on event
2. Update fields in popup
3. Click "Save Changes"
4. Event is updated in list

## Data Explained

### What's Stored
```json
{
  "id": "Unique identifier",
  "title": "Event name",
  "description": "Event details",
  "date": "YYYY-MM-DD format",
  "time": "HH:MM format",
  "category": "Custom category",
  "created_at": "Timestamp",
  "attachment": "File path or empty"
}
```

### File Locations
- Events stored in: `events.json`
- Uploads stored in: `attachments/`
- Both in: `c:\xampp\htdocs\RhealynSystem\`

## Common Tasks

### Create Multiple Events Quickly
1. Fill form with first event
2. Click "Create Event"
3. Form auto-fills date
4. Reset form with "Clear" button
5. Enter next event
6. Repeat

### Find Events in June
1. Click "Filter by Date"
2. Select any date in June
3. All June events appear

### View Specific Category
1. Use "Filter by Category" dropdown
2. Select category
3. Only events in category show

### Backup Events
1. Copy `events.json` file
2. Save to USB drive or cloud
3. Restore by replacing file if needed

### Export Events
Check API_DOCS.md for exporting to:
- CSV (for Excel)
- ICS (for Google Calendar, Outlook)
- Custom formats

## Tips & Tricks

💡 **Tip 1**: Categories are flexible - use any category name  
💡 **Tip 2**: Use time field for time-sensitive events  
💡 **Tip 3**: Attachments are useful for event details/files  
💡 **Tip 4**: Delete unwanted events to keep calendar clean  
💡 **Tip 5**: Check statistics at top for overview  

## Troubleshooting Quick Reference

| Problem | Solution |
|---------|----------|
| Page won't load | Start Apache in XAMPP, check URL |
| Can't save events | Check verify.php, ensure permissions |
| File upload fails | Check attachments folder exists |
| Data disappears | Verify events.json exists, backup regularly |
| Slow performance | Check events.json size, archive old events |

## File Size Guide

- Empty system: ~2KB
- 50 events: ~10KB
- 500 events: ~100KB
- 5000 events: ~1MB

*Performance remains good even with 5000+ events*

## Security Notes

✓ All inputs are sanitized  
✓ File uploads validated  
✓ No database = no SQL injection  
✓ Files stored separately  

⚠️ For production:
- Add user authentication
- Use HTTPS/SSL
- Implement access control
- Regular backups

## Sample Use Cases

### Case 1: Personal Calendar
- Create personal, work, health, shopping categories
- Add daily events
- Set reminders with descriptions

### Case 2: Project Management
- Category = Project name
- Events = Milestones/deadlines
- Attachments = Project files

### Case 3: Event Planning
- Create events for party, conference, etc
- Use descriptions for details
- Attach invitations or forms

### Case 4: Team Calendar
- Share via network folder
- All team members access same events
- Central calendar for coordination

## Statistics Dashboard

Shows:
- **Total Events** - All events created
- **Showing** - Events matching current filters
- **Categories** - Number of different categories

Helps track calendar usage and organization.

## Need Help?

1. **First Time?** → Read QUICKSTART.md
2. **How to Use?** → Check README.md
3. **For Developers?** → See API_DOCS.md
4. **Setup Issues?** → Run verify.php
5. **Advanced?** → Edit config.php

## Next Actions

✅ Start XAMPP Apache  
✅ Open http://localhost/RhealynSystem/  
✅ Create first event  
✅ Try filters  
✅ Test edit/delete  
✅ Upload file attachment  

---

**Everything is ready to use!** Start creating events now. 📅✨
