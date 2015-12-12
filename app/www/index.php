<!DOCTYPE html>
<html>
<head>

    <link rel='stylesheet' href='/js/fullcalendar-2.5.0/fullcalendar.css'/>
    <link rel='stylesheet' href='/js/jquery-ui/jquery-ui.min.css'/>
    <link rel='stylesheet' href='/css/fullcalendar-php.css'/>
    <link rel='stylesheet' href='/js/timepicker/jquery-ui-timepicker-addon.css'/>


    <script src='js/fullcalendar-2.5.0/lib/jquery.min.js'></script>
    <script src='js/jquery-ui/jquery-ui.min.js'></script>
    <!--    If you don't use english-->
    <!--    <script src='/js/jquery-ui/i18n/datepicker-fr.js'></script>-->

    <script src='js/timepicker/jquery-ui-timepicker-addon.js'></script>
    <!--    If you don't use english-->
    <!--    <script src='/js/timepicker/i18n/jquery-ui-timepicker-fr.js'></script>-->


    <script src='js/fullcalendar-2.5.0/lib/moment.min.js'></script>
    <script src='js/fullcalendar-2.5.0/fullcalendar.js'></script>

    <!--    If you don't use english-->
    <!--    <script src='/js/fullcalendar-2.5.0/lang/fr.js'></script>-->


    <script src='js/tim-functions/tim-functions.js'></script>
</head>


<body>

<div id='calendar'></div>


<div style="display:none" id="jquery-modal-form">
    <div id="event-dialog-form" title="Create new event">

        <p class="validateTips"></p>

        <form>
            <fieldset>
                <label for="title">Title</label>
                <input type="text" name="title" id="title" value="Event 1"
                       class="text ui-widget-content ui-corner-all">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="text ui-widget-content ui-corner-all"></textarea>
                <label for="start_date">Start</label>
                <input type="text" name="start_date" id="start_date" value="<?php echo date("Y-m-d H:i:s"); ?>"
                       class="text ui-widget-content ui-corner-all">
                <label for="end_date">End</label>
                <input type="text" name="end_date" id="end_date"
                       value="<?php echo date("Y-m-d H:i:s", time() + 3600); ?>"
                       class="text ui-widget-content ui-corner-all">

                <!-- Allow form submission with keyboard without duplicating the dialog button -->
                <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
            </fieldset>
        </form>
    </div>
</div>


<script>
(function ($) {
    $(document).ready(function () {


        //------------------------------------------------------------------------------/
        // JQUERY UI FORM
        //------------------------------------------------------------------------------/
        var eventDialog, form;
        var jTitle = $("#title");
        var jDescription = $("#description");
        var jStartDate = $("#start_date");
        var jEndDate = $("#end_date");

        var allFields = $([])
            .add(jTitle)
            .add(jDescription)
            .add(jStartDate)
            .add(jEndDate);
        var tips = $(".validateTips");

        function updateTips(t) {
            tips
                .text(t)
                .addClass("ui-state-highlight");
            setTimeout(function () {
                tips.removeClass("ui-state-highlight", 1500);
            }, 500);
        }

        function checkLength(o, n, min, max) {
            if (o.val().length > max || o.val().length < min) {
                o.addClass("ui-state-error");
                updateTips("Length of " + n + " must be between " + min + " and " + max + ".");
                return false;
            } else {
                return true;
            }
        }

        function checkRegexp(o, regexp, n) {
            if (!( regexp.test(o.val()) )) {
                o.addClass("ui-state-error");
                updateTips(n);
                return false;
            } else {
                return true;
            }
        }

        function checkFields() {
            var valid = true;
            valid = allFields.removeClass("ui-state-error");
            valid = valid && checkLength(jTitle, "title", 3, 256);
            return valid;
        }

        function getFields() {
            return {
                title: jTitle.val(),
                description: jDescription.val(),
                start_date: jStartDate.val(),
                end_date: jEndDate.val()
            };
        }


        function deleteEvent(id) {
            timPost('service/delete-event.php', {
                id: id
            }, function (timMsg) {
                eventDialog.dialog("close");
                $('#calendar').fullCalendar('refetchEvents');
            });

        }

        function insertEvent() {
            var valid = checkFields();
            if (valid) {
                var fields = getFields();
                timPost('service/insert-event.php', fields, function (timMsg) {
                    eventDialog.dialog("close");
                    $('#calendar').fullCalendar('refetchEvents');
                });
            }
        }

        function updateEvent(id) {
            var valid = checkFields();
            if (valid) {
                var fields = getFields();
                fields["id"] = id;
                timPost('service/update-event.php', fields, function (timMsg) {
                    eventDialog.dialog("close");
                    $('#calendar').fullCalendar('refetchEvents');
                });
            }
        }

        function updateEventStart(id, deltaAsSeconds) {
            timPost('service/update-event-start.php', {
                id: id,
                delta: deltaAsSeconds
            }, function (timMsg) {
                // ;)
            });
        }

        function updateEventDuration(id, deltaAsSeconds) {
            timPost('service/update-event-duration.php', {
                id: id,
                delta: deltaAsSeconds
            }, function (timMsg) {
                // ;)
            });
        }


        eventDialog = $("#event-dialog-form").dialog({
            autoOpen: false,
            height: 450,
            width: 450,
            modal: true,
            dialogClass: "fullcalendar-dialog",
            buttons: {},
            close: function () {
                allFields.removeClass("ui-state-error");
            }
        });


        //------------------------------------------------------------------------------/
        // TIMEPICKER
        //------------------------------------------------------------------------------/
        var timepickerOptions = {
            dateFormat: "yy-mm-dd",
            timeFormat: "HH:mm:ss",
            controlType: "slider",
            timeInput: true,
            showMillisec: false,
            showMicrosec: false,
            showTimezone: false
        };
        $("#start_date").datetimepicker(timepickerOptions);
        $("#end_date").datetimepicker(timepickerOptions);
        $(".ui-datepicker").draggable();


        //------------------------------------------------------------------------------/
        // CALENDAR
        //------------------------------------------------------------------------------/
        $('#calendar').fullCalendar({
            defaultView: 'minute_30',
            header: {
                center: 'minute_1,minute_10,minute_30,month'
            },
            views: {
                second_30: {
                    type: 'agenda',
                    buttonText: '30 seconds',
                    duration: {days: 7},
                    slotDuration: '00:00:30'
                },
                minute_1: {
                    type: 'agenda',
                    buttonText: '1 minute',
                    duration: {days: 7},
                    slotDuration: '00:01:00'
                },
                minute_10: {
                    type: 'agenda',
                    buttonText: '10 minutes',
                    duration: {days: 7},
                    slotDuration: '00:10:00'
                },
                minute_30: {
                    type: 'agenda',
                    buttonText: '30 minutes',
                    duration: {days: 7},
                    slotDuration: '00:30:00'
                }
            },
            allDaySlot: false,
            columnFormat: 'ddd D',
            slotLabelFormat: 'HH:mm',
            timeFormat: 'HH:mm:ss',
            editable: true,
            dayClick: function (date, jsEvent, view) {

                var dateStart = date.format().replace("T", " ");
                var defaultTimedEventDuration = $('#calendar').fullCalendar('option', "defaultTimedEventDuration");
                var dateEnd = date.add(moment.duration(defaultTimedEventDuration)).format().replace("T", " ");


                jTitle.val("");
                jDescription.val("");
                jStartDate.val(dateStart);
                jEndDate.val(dateEnd);

                eventDialog.dialog("option", "title", "Insert Event");
                eventDialog.dialog("option", "buttons",
                    [
                        {
                            text: "Cancel",
                            click: function () {
                                $(this).dialog("close");
                            }
                        },
                        {
                            text: "Insert the event",
                            click: function () {
                                insertEvent();
                            }
                        }
                    ]
                );
                eventDialog.dialog("open");
            },
            eventResize: function (event, delta, revertFunc, jsEvent, ui, view) {
                updateEventDuration(event.id, delta.asSeconds());
            },
            eventDrop: function (event, delta, revertFunc, jsEvent, ui, view) {
                updateEventStart(event.id, delta.asSeconds());
            },
            eventClick: function (event, jsEvent, view) {
                var url = "service/event.php";
                timPost(url, {
                    id: event.id
                }, function (msg) {

                    //------------------------------------------------------------------------------/
                    // INJECT VALUES FROM THE DB INTO THE UPDATE DIALOG FORM
                    //------------------------------------------------------------------------------/
                    jTitle.val(msg.title);
                    jDescription.val(msg.description);
                    jStartDate.val(msg.start_date);
                    jEndDate.val(msg.end_date);

                    eventDialog.dialog("option", "title", "Update Event #" + msg.id);
                    eventDialog.dialog("option", "buttons",
                        [
                            {
                                text: "Cancel",
                                click: function () {
                                    $(this).dialog("close");
                                }
                            },
                            {
                                text: "Delete the event",
                                click: function () {
                                    var deleteEv = confirm("Are you sure you want to delete this event?");
                                    if (true === deleteEv) {
                                        deleteEvent(event.id);
                                    }
                                }
                            },
                            {
                                text: "Update the event",
                                click: function () {
                                    updateEvent(event.id);
                                }
                            }
                        ]
                    );
                    eventDialog.dialog("open");
                });
            },
            eventSources: [
                {
                    url: 'service/events.php',
                    color: 'black',
                    textColor: 'yellow'
                }
            ]
        })
    });
})(jQuery);
</script>

</body>
</html>
