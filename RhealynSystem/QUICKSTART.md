# 🚀 Quick Start Guide

## Step 1: Start XAMPP
1. Open **XAMPP Control Panel**
2. Click **Start** next to Apache
3. You should see green indicator for Apache

## Step 2: Access the Application
1. Open your web browser
2. Go to: `http://localhost/RhealynSystem/`

## Step 3: Create Your First Event

### Using the Web Interface:
1. **Event Title**: Enter a name for your event (e.g., "Team Meeting")
2. **Description**: Add optional details
3. **Date**: Select a date from the calendar picker
4. **Time**: Set a time (optional)
5. **Category**: Type or select a category (e.g., Work, Personal, Birthday)
6. **Attachment**: Optionally upload a file
7. Click **Create Event** button

## Step 4: View Your Events

All events appear below the form, sorted by date. You'll see:
- Event title and date
- Category badge (colored label)
- Description (if any)
- Attachment link (if any)
- Edit and Delete buttons

## Step 5: Search & Filter

### Filter by Date:
1. Click on the date picker under "Filter by Date"
2. Select the date
3. Events for that date will be shown

### Filter by Category:
1. Click the dropdown under "Filter by Category"
2. Select a category
3. Events in that category will be shown

## Common Actions

### Edit an Event
1. Click the **Edit** button on the event card
2. Update the fields in the popup
3. Click **Save Changes**

### Delete an Event
1. Click the **Delete** button on the event card
2. Confirm the deletion when prompted

### View an Attachment
1. Click the attachment link on the event
2. The file opens in a new tab/window

## File Structure

```
RhealynSystem/
├── index.php              ← Main application (open this in browser)
├── config.php             ← Configuration and helper functions
├── events.json            ← Your event data (created automatically)
├── events.sample.json     ← Sample data for reference
├── README.md              ← Full documentation
├── QUICKSTART.md          ← This file
└── attachments/           ← Uploaded files (created automatically)
```

## Sample Data

To see the calendar with sample events:
1. Open `events.sample.json`
2. Copy all content
3. Replace content in `events.json` with the copied data
4. Refresh the browser page

## Troubleshooting

### Page doesn't load
- Verify Apache is running in XAMPP
- Check URL is: `http://localhost/RhealynSystem/`
- Clear browser cache (Ctrl+F5 in Chrome)

### Can't create events
- Ensure XAMPP Apache is running
- Check if `attachments` folder exists
- Look for PHP errors in browser console (F12)

### Events disappear after refresh
- Check if `events.json` file exists
- Verify the file has write permissions
- Try creating a simple test event

## Need Help?

1. Check the **README.md** for detailed documentation
2. Review **config.php** for available functions
3. Check browser console for errors (F12)
4. Verify XAMPP Apache service is running

---

**Enjoy organizing your events!** 📅✨
