<?php


namespace Tim\TimServer;


/**
 * TimServerInterface
 * @author Lingtalfi
 * 2015-12-11
 *
 */
interface TimServerInterface
{


    public function output();

    /**
     * @param mixed $msg
     * @return TimServerInterface
     */
    public function error($msg);

    /**
     * @param mixed $msg
     * @return TimServerInterface
     */
    public function success($msg);


}
