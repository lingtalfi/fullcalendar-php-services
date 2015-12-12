<?php




//------------------------------------------------------------------------------/
// UPDATE EVENT
//------------------------------------------------------------------------------/
/**
 * Tim service, it's a tim success if the update is done,
 * and a tim failure otherwise.
 */
use QuickPdo\QuickPdo;
use Tim\TimServer\TimServer;
use Tim\TimServer\TimServerInterface;

require_once __DIR__ . "/../../init.php";

TimServer::create()->start(function (TimServerInterface $server) {
    if (
        isset($_POST['id']) &&
        isset($_POST['title']) &&
        isset($_POST['description']) &&
        isset($_POST['start_date']) &&
        isset($_POST['end_date'])
    ) {
        if (true === QuickPdo::update('the_events', [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
            ], [
                ['id', '=', $_POST['id']]
            ])
        ) {
            $server->success('ok');
        }
        else {
            appLog("[app]/www/service/update-event: pdo error: {pdoError}", [
                'pdoError' => QuickPdo::getLastError(),
            ]);
            $server->error('An error occurred with the database, please retry later.');
        }

    }
})->output();





