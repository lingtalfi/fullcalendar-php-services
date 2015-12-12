<?php




//------------------------------------------------------------------------------/
// DISPLAY EVENTS FROM THE DATABASE IN JSON FORMAT
//------------------------------------------------------------------------------/
use QuickPdo\QuickPdo;

require_once __DIR__ . "/../../init.php";
require_once __DIR__ . "/../../functions/db.php";

if (isset($_GET['start']) && isset($_GET['end'])) {
    $stmt = 'select * from the_events where start_date >= :start and end_date <= :end';
    $_events = QuickPdo::fetchAll($stmt, [
        'start' => $_GET['start'],
        'end' => $_GET['end'],
    ]);

    
    
    $events = [];
    foreach ($_events as $e) {
        $events[] = [
            'id' => $e['id'],
            'title' => $e['title'],
            'start' => dateMysqlTime2Iso8601($e['start_date']),
            'end' => dateMysqlTime2Iso8601($e['end_date']),
        ];
    }
    echo json_encode($events);
}



