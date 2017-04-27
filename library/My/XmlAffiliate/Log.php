<?php

class My_XmlAffiliate_Log {

    public function setLog($logText, $logType = 'INFO', $affiliateProgramName = '') {
        $this->_log[][$logType] = $affiliateProgramName != '' ? (date('j.n.Y G:i:s', time()) . " | ". $affiliateProgramName . " | ".$logText) : (date('j.n.Y G:i:s', time()) . " | ".$logText);
    }

    public function getLog() {
        return $this->_log;
    }

    public function getLogStringMessage($log) {

        $logMessage = "";

        if (!empty($log)) {
            foreach ($log as $logItem) {
                foreach ($logItem as $lType => $l) {
                    $logMessage .= $lType . ": " . $l . "\n";
                }
            }
        }

        return $logMessage;
    }

}