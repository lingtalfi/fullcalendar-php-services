<?php



function a()
{
    foreach (func_get_args() as $arg) {
        ob_start();
        var_dump($arg);
        $output = ob_get_clean();
        if ('1' !== ini_get('xdebug.default_enable')) {
            $output = preg_replace("!\]\=\>\n(\s+)!m", "] => ", $output);
        }
        if ('cli' === PHP_SAPI) {
            echo $output;
        }
        else {
            echo '<pre>' . $output . '</pre>';
        }
    }
}


function az()
{
    call_user_func_array('a', func_get_args());
    exit;
}
