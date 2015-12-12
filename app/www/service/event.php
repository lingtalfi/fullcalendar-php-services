<?php




//------------------------------------------------------------------------------/
// DISPLAY EVENT INFO FROM THE DATABASE IN JSON FORMAT
//------------------------------------------------------------------------------/
use QuickPdo\QuickPdo;
use Tim\TimServer\TimServer;
use Tim\TimServer\TimServerInterface;

require_once __DIR__ . "/../../init.php";
require_once __DIR__ . "/../../functions/db.php";
TimServer::create()->start(function (TimServerInterface $server) {
    if (isset($_POST['id'])) {
        $stmt = 'select * from the_events where id=:id';
        $eventInfo = QuickPdo::fetch($stmt, [
            'id' => $_POST['id'],
        ]);
        $server->success($eventInfo);
    }
})->output();





