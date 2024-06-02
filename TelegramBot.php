<?php

class TelegramBot {
    private $botToken;

    public function __construct() {
        $this->loadEnv();
        $this->botToken = getenv('Token');
    }

    private function loadEnv() {
        if (file_exists(__DIR__ . '/.env')) {
            $lines = file(__DIR__ . '/.env');
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    putenv("$key=$value");
                }
            }
        }
    }

    public function send($text, $imagePath) {
        $chatId = '@linuxtipsdaily';
        
        // Send image with caption
        $photoMessageUrl = "https://api.telegram.org/bot{$this->botToken}/sendPhoto";
        $photoPostFields = [
            'chat_id' => $chatId,
            'photo' => new CURLFile(realpath($imagePath)),
            'caption' => $text
        ];
    
        $this->executeCurl($photoMessageUrl, $photoPostFields);
    }
    

    private function executeCurl($url, $postFields) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $response;
    }
}

?>
