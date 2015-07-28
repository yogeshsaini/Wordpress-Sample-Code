<?php

class ErrorReporting
{
    public $error_msg = array();
    public function log_error($message = null, $formate = 'json', $lineno = null, $filename = null)
    {
        $this->error_msg['error']['message']  = 'Error Msg : ' . $message;
        $this->error_msg['error']['lineno']   = 'Line no. : ' . $lineno;
        $this->error_msg['error']['filename'] = 'File name : ' . $filename;

        $message = implode("\r\n", $this->error_msg['error']);

        error_log($message, 0);
        error_log($message, 1, "anurag.bhargava@daffodilsw.com");
        switch ($formate) {
            case 'json':
                die(json_encode($this->error_msg));
                break;

            case 'phparray':
                return $this->error_msg;
                break;
        }
    }
}

?>