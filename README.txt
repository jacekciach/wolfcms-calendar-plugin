# Description

The plugins lets to maintain **a simple events list** and to show the event(s) in the frontend.

Each event has its:
1. Title,
2. Date,
3. Date of end (optional),
4. Description (optional).

The plugin also, or maybe first of all, **adds a _calendar_ behaviour**.

_Calendar_ behaviour lets to show events in the particular day (similar to the _Archive_ behaviour).

With the calendar plugin one can show a clickable calendar view\*. Events are presented on the view with boxes in appropriate days. The calendar behaviour lets to "travel" in the past and in the future.

Clicking on a day with an event redirects to a "the day" page, which lists all events (title, dates, description) from that day. "The day" page can be easy customized with the standard WolfCMS subpage system.

The plugin comes with a default cascade style sheet file, designed and tested on the default WolfCMS theme. However, I believe, it will work with other themes as well; maybe even without any modifications. If mods are needed, the CSS is simple and easy to change.

The plugin has been tested on a real, living page, with Wolf CMS 0.7.8.

All views are translatable. There are three translations included: Polish, English and Dutch. The names of days of the week and of months are based on a current system locale.

\* the calendar view should be used on the top calendar behaviour page; but it's not a requirement

# Installation

1. Copy `plugins/calendar/` to `wolf/plugins/calendar/`
2. Copy `public/css/calendar.css` to, for example, `public/themes/calendar/calendar.css`
3. Insert to your layout (change the path, if needed):
   ```
   `<link rel="stylesheet" href="<?php echo THEMES_PATH; ?>calendar/calendar.css" type="text/css">
   ```
4. Enable the plugin in the Administration Panel.
5. Works!
