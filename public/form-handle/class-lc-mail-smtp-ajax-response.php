<?php

class Lc_Mail_Smtp_Public_Ajax_Response {

    private $message;
    private $data;
    private $status;

    /**
     * Lc_Mail_Smtp_Public_Ajax_Response constructor.
     * @param $status
     * @param $message
     * @param $data
     */
    public function __construct($status, $message, $data)
    {
        if(!in_array($status, [0, 1])) {
            wp_die('Invalid status, please set it to 0 = Error or 1 = Success');
        }

        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * @return false|string
     */
    public function send() {
        return json_encode([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
        ]);
    }
}