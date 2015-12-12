<?php


//------------------------------------------------------------------------------/
// CREATE TODAY'S EVENTS
//------------------------------------------------------------------------------/
/**
 * Launch this script to clear and refresh today's event.
 */
use QuickPdo\QuickPdo;

require_once __DIR__ . "/../init.php";


//------------------------------------------------------------------------------/
// SCRIPT
//------------------------------------------------------------------------------/
$lorem = "Lorem ipsum dolor sit amet, id nam munere fabulas. Ei soluta scribentur signiferumque mel. Ea vel prima albucius deleniti, te semper principes pro, cu duis illud nobis cum. Sensibus tincidunt ne duo. Vel ut porro essent.";
$day = date('Y-m-d ');


$events = [
    [
        'title' => 'event 1',
        'description' => $lorem,
        'start_date' => $day . '10:00:00',
        'end_date' => $day . '10:35:00',
    ],
    [
        'title' => 'event 2',
        'description' => $lorem,
        'start_date' => $day . '10:05:36',
        'end_date' => $day . '10:05:56',
    ],
    [
        'title' => 'event 3',
        'description' => $lorem,
        'start_date' => $day . '11:02:00',
        'end_date' => $day . '11:12:30',
    ],
    [
        'title' => 'event 4',
        'description' => $lorem,
        'start_date' => $day . '13:15:00',
        'end_date' => $day . '15:15:00',
    ],
];



a("clear the the_events table");
a(QuickPdo::delete('the_events'));


a("inserting events");
foreach ($events as $event) {
    a(QuickPdo::insert('the_events', $event));
}



