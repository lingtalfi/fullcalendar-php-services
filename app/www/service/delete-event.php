<?php




//------------------------------------------------------------------------------/
// DELETE EVENT
//------------------------------------------------------------------------------/
/**
 * Tim service, it's a tim success if the delete is done,
 * and a tim failure otherwise.
 */
use QuickPdo\QuickPdo;
use Tim\TimServer\TimServer;
use Tim\TimServer\TimServerInterface;

require_once __DIR__ . "/../../init.php";

TimServer::create()->start(function (TimServerInterface $server) {
    if (
    isset($_POST['id'])
    ) {
        if (false !== $id = QuickPdo::delete('the_events', [
                ['id', '=', $_POST['id']],
            ])
        ) {
            $server->success($id);
        }
        else {
            appLog("[app]/www/service/delete-event: pdo error: {pdoError}", [
                'pdoError' => QuickPdo::getLastError(),
            ]);
            $server->error('An error occurred with the database, please retry later.');
        }

    }
})->output();





