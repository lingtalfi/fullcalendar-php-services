<?php




//------------------------------------------------------------------------------/
// UPDATE EVENT DURATION
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
        isset($_POST['delta'])
    ) {
        $delta = (int)$_POST['delta'];
        $id = (int)$_POST['id'];
        
        $stmt = "update the_events set
            end_date = DATE_ADD(end_date,INTERVAL $delta SECOND)
            where id=$id
        ";
        if (false !== QuickPdo::freeStmt($stmt)) {
            $server->success('ok');
        }
        else {
            appLog("[app]/www/service/update-event-duration: pdo error: {pdoError}", [
                'pdoError' => QuickPdo::getLastError(),
            ]);
            $server->error('An error occurred with the database, please retry later.');
        }

    }
})->output();





