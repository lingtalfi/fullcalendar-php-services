<?php


namespace Tim\TimServer;


/**
 * TimServer
 * @author Lingtalfi
 * 2014-10-24
 *
 */
class TimServer implements TimServerInterface
{

    private $type;
    private $message;

    public function __construct()
    {
        $this->message = 'server not started yet';
        $this->type = 'e';
    }

    public static function create()
    {
        return new static();
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS TimServerInterface
    //------------------------------------------------------------------------------/
    public function output()
    {
        echo json_encode([
            't' => $this->type,
            'm' => $this->message,
        ]);
    }


    public function error($msg)
    {
        $this->type = 'e';
        $this->message = $msg;
        return $this;
    }

    public function success($msg)
    {
        $this->type = 's';
        $this->message = $msg;
        return $this;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function start($callable)
    {
        if (is_callable($callable)) {
            try {
                call_user_func($callable, $this);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                $this->log($e);
            }
        }
        else {
            throw new \InvalidArgumentException("callable must be a callable");
        }
        return $this;
    }


    protected function log(\Exception $e)
    {

    }


}
