<?php

namespace App;

use Illuminate\Support\Facades\Log;

class LogManager
{
    private static $instance = null;
    private $logs = [];
    private $maxLogs = 100;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function log($message, $type = 'info')
    {
        $log = [
            'timestamp' => now(),
            'message' => $message,
            'type' => $type
        ];

        array_unshift($this->logs, $log);

        if (count($this->logs) > $this->maxLogs) {
            array_pop($this->logs);
        }

        switch ($type) {
            case 'error':
                Log::error($message);
                break;
            case 'warning':
                Log::warning($message);
                break;
            default:
                Log::info($message);
                break;
        }

        return $this;
    }

    public function info($message)
    {
        return $this->log($message, 'info');
    }

    public function warning($message)
    {
        return $this->log($message, 'warning');
    }

    public function error($message)
    {
        return $this->log($message, 'error');
    }

    public function getLogs($limit = null)
    {
        if ($limit === null) {
            return $this->logs;
        }

        return array_slice($this->logs, 0, $limit);
    }

    public function clearLogs()
    {
        $this->logs = [];
        return $this;
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception("Singletonni deserializatsiya qilib bo'lmaydi.");
    }
}
