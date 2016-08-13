<?php

if (!defined('IN_CMS')) { exit(); }

?>
<style type="text/css">
ol {
  margin-left: 30px;
}
ol li {
  margin-bottom: 10px;
}
ol li pre {
  margin: 10px;
}
</style>

<h1><?php echo __('Documentation'); ?></h1>

<h2>Quick How-To</h2>

<h3>Setting up CSS</h3>
<ol>
    <li>Copy <b>css/calendar.css</b> to your theme folder, for example <b>public/themes/wolf/</b></li>
    <li>Insert to your layout (change the path, if needed):
        <pre>&lt;link rel=&quot;stylesheet&quot; href=&quot;&lt;?php echo THEMES_PATH; ?&gt;wolf/calendar.css&quot; type=&quot;text/css&quot;&gt;</pre>
    </li>
</ol>
<br>

<h3>Adding events</h3>
<ul>
    <li>Events can be added from the Calendar tab in the admin backend.</li>
    <li>If you don't enter the "date to", the event is considered to be one day long.</li>
    <li>In "description" you can use HTML and insert a code in &lt;?php ?&gt;.</li>
</ul>
<br>

<h3>Setting up the frontend</h3>
<ol>
    <li>Make a subpage of your home page.</li>
    <li>Name the subpage Events, Calendar, whatever you want.</li>
    <li>Change the behaviour to "<i>calendar</i>".</li>
    <li>Insert in "body" of the page:
        <pre>&lt;?php showCalendar($this-&gt;slug); ?&gt;</pre>
    </li>
    <li>Works!</li>
</ol>
<br>

<h3>Customizing "the day" page.</h3>
<ol>
    <li>Create an event.</li>
    <li>Create a hidden subpage of the calendar page and set a slug (alias) to event's "date_from".</li>
    <li>The created subpage will be shown if you click on the date.</li>
</ol>
<br>

<h2>Copyright and Licence</h2>
<ul>
    <li>Jacek Ciach, <a href="mailto:jacek.ciach@wp.eu">jacek.ciach@wp.eu</a></li>
    <li>Licence: <a href="http://www.gnu.org/licenses/gpl.html">GPL</a></li>
<ul>
