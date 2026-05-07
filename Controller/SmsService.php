<?php
/**
 * APPOLIOS SMS Service
 * Handles SMS notifications via Twilio REST API
 */

class SmsService {
    private $sid;
    private $token;
    private $from;

    public function __construct() {
        $this->sid = defined('TWILIO_SID') ? TWILIO_SID : null;
        $this->token = defined('TWILIO_TOKEN') ? TWILIO_TOKEN : null;
        $this->from = defined('TWILIO_FROM_NUMBER') ? TWILIO_FROM_NUMBER : null;
    }

    /**
     * Send an SMS message
     * @param string $to Recipient phone number (e.g. +216...)
     * @param string $message The message content
     * @return bool Success or failure
     */
    public function sendSms($to, $message) {
        if (!$this->sid || !$this->token || !$this->from) {
            error_log("Twilio credentials not configured.");
            return false;
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json";
        
        $data = [
            'From' => $this->from,
            'To' => $to,
            'Body' => $message
        ];

        $postData = http_build_query($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->sid}:{$this->token}");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev environment

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Debug logging
        error_log("Twilio Response: " . $response);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        } else {
            error_log("Twilio SMS failed: HTTP $httpCode - Response: $response - Error: $error");
            return false;
        }
    }
}
