<?php

/*
 * zamezeni pristupu ruskym spamerum, nici statistiky v google analytics
 */

class Plugin_DenySpam extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $deny = array(
            "78.110.60.230",
            "92.255.241.1",
            "78.110.50.108",
            "5.10.83.0/25",
            "204.145.80.58",
            "76.187.211.31",
            "213.111.225.146",
            "188.92.75.105",
            "91.201.66.33"
        );

        if (in_array($_SERVER['REMOTE_ADDR'], $deny)) {
            header("location: http://www.google.com/");
            exit();
        }
    }

}
