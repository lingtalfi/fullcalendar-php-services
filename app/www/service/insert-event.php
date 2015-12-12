<?php




//------------------------------------------------------------------------------/
// INSERT EVENT
//------------------------------------------------------------------------------/
/**
 * Tim service, it's a tim success if the insert is done,
 * and a tim failure otherwise.
 */
use QuickPdo\QuickPdo;
use Tim\TimServer\TimServer;
use Tim\TimServer\TimServerInterface;

require_once __DIR__ . "/../../init.php";

TimServer::create()->start(function (TimServerInterface $server) {
    if (
        isset($_POST['title']) &&
        isset($_POST['description']) &&
        isset($_POST['start_date']) &&
        isset($_POST['end_date'])
    ) {
        if (false !== $id = QuickPdo::insert('the_events', [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
            ])
        ) {
            $server->success($id);
        }
        else {
            appLog("[app]/www/service/insert-event: pdo error: {pdoError}", [
                'pdoError' => QuickPdo::getLastError(),
            ]);
            $server->error('An error occurred with the database, please retry later.');
        }

    }
})->output();





