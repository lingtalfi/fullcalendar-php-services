Tim
===========
2015-12-11




Tim is a simple protocol to help with communication between a client and a server.
Upon a server's response, the client knows whether or not the server's response was a success or a failure.



Tim protocol
--------------

This is actually just an idea:

the client sends its request,
the server must respond with a json array containing two keys:

- t: string(e|s), the message type, e means error, s means success 
- m: mixed, the server's answer. The data can be a string or an array, or a bool, anything... 



Note: The name tim comes from the letters t and m.



Tools
----------

### TimServer

TimServer is a php implementation of a tim server.
It helps you creating a php service.


#### Example code

The code below showcases the TimServer features.
It uses the [bigbang technique](https://github.com/lingtalfi/TheScientist/blob/master/convention.portableAutoloader.eng.md) to autoload 
the classes.


 
```php  
<?php

require_once "bigbang.php";


use Tim\TimServer\TimServer;
use Tim\TimServer\TimServerInterface;


TimServer::create()
    ->start(function (TimServerInterface $server) {
        if (isset($_POST['id'])) {
            // ...
            if ('valid') {
                $server->success("Congrats!");
            }
            else {
                throw new \Exception("division by zero!");
            }
        }
        else {
            $server->error("Oops");
        }
    })
    ->output();

```



