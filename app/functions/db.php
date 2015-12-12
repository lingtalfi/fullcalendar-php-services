<?php


use QuickPdo\QuickPdo;


//------------------------------------------------------------------------------/
// DB 
//------------------------------------------------------------------------------/
/**
 * I try to put any db interaction in this file,
 * so that you have only one file to update to adapt the database to your needs.
 */


function fetchAllEventsByDateRange($start, $end)
{
    $stmt = 'select * from the_events where start_date >= :start and end_date <= :end';
    return QuickPdo::fetchAll($stmt, [
        'start' => $start,
        'end' => $end,
    ]);
}