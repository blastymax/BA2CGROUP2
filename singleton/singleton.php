<?php
/**
 * Singleton Design Pattern in PHP
 * Ensures only one instance of the class exists during runtime.
 */
class Singleton
{
    /**
     * Holds the single instance of the class
     * @var Singleton|null
     */
    private static ?Singleton $instance = null;

    /**
     * Example property
     */
    private string $configValue;

    /**
     * Private constructor to prevent direct object creation
     */
    private function __construct()
    {
        // Initialize configuration or resources
        $this->configValue = "Default Config";
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone()
    {
        // Disallow cloning
    }

    /**
     * Prevent unserialization of the instance
     */
    private function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * Returns the single instance of the class
     * @return Singleton
     */
    public static function getInstance(): Singleton
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Example method to demonstrate functionality
     */
    public function setConfigValue(string $value): void
    {
        $this->configValue = $value;
    }

    public function getConfigValue(): string
    {
        return $this->configValue;
    }
}

// --------------------
// Example Usage
// --------------------
try {
    $singleton1 = Singleton::getInstance();
    $singleton1->setConfigValue("Custom Config");

    $singleton2 = Singleton::getInstance();

    echo "Singleton 1 Config: " . $singleton1->getConfigValue() . PHP_EOL;
    echo "Singleton 2 Config: " . $singleton2->getConfigValue() . PHP_EOL;

    // Both will output the same value because they are the same instance
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}