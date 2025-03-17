<?php

namespace App;

class AppSettings
{
    private static $instance = null;
    private $settings = [];
    private $defaultSettings = [
        'app_name' => 'Event Organizer',
        'max_event_images' => 10,
        'default_fine_days' => 7,
        'events_per_page' => 12,
        'allow_event_cancellation' => true,
        'notification_enabled' => true,
        'notification_default_type' => 'email',
    ];

    private function __construct()
    {
        $this->settings = $this->defaultSettings;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    public function set($key, $value)
    {
        $this->settings[$key] = $value;

        // TODO: sozlamani databaseda saqlaydigan qilish
        // Misol uchun: DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value]);

        return $this;
    }

    public function all()
    {
        return $this->settings;
    }

    public function reset()
    {
        $this->settings = $this->defaultSettings;
        return $this;
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception("Singletonni deserializatsiya qilib bo\'lmaydi.");
    }
}
