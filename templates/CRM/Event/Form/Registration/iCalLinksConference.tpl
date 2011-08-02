{* Display icons / links for ical download and feed for EventInfo.tpl and ThankYou.tpl *}
{capture assign=icalFile}{crmURL p='civicrm/event/ical' q="reset=1&id=`$event.id`" fe=1 a=1}{/capture}
<div class="action-link">
    <a href="{$icalFile}" title="{ts}Download iCalendar entry for this event.{/ts}"><img src="{$config->resourceBase}i/office-calendar.png" alt="{ts}Download iCalendar entry for this event.{/ts}"></a>
</div>
