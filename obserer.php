<?php
/**
 * Observer Design Pattern in PHP
 * 
 * This example demonstrates a simple weather station system
 * where observers (displays) get notified when the temperature changes.
 */

/**
 * Observer Interface
 */
interface Observer {
    public function update(float $temperature): void;
}

/**
 * Subject Interface
 */
interface Subject {
    public function attach(Observer $observer): void;
    public function detach(Observer $observer): void;
    public function notify(): void;
}

/**
 * Concrete Subject (WeatherStation)
 */
class WeatherStation implements Subject {
    private array $observers = [];
    private float $temperature;

    public function setTemperature(float $temperature): void {
        // Validate temperature range
        if ($temperature < -100 || $temperature > 100) {
            throw new InvalidArgumentException("Temperature out of realistic range.");
        }
        $this->temperature = $temperature;
        $this->notify(); // Notify observers of the change
    }

    public function attach(Observer $observer): void {
        $this->observers[spl_object_hash($observer)] = $observer;
    }

    public function detach(Observer $observer): void {
        unset($this->observers[spl_object_hash($observer)]);
    }

    public function notify(): void {
        foreach ($this->observers as $observer) {
            $observer->update($this->temperature);
        }
    }
}

/**
 * Concrete Observer (TemperatureDisplay)
 */
class TemperatureDisplay implements Observer {
    private string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function update(float $temperature): void {
        echo "{$this->name} Display: Current temperature is {$temperature}°C\n";
    }
}

/**
 * Concrete Observer (AlertSystem)
 */
class AlertSystem implements Observer {
    public function update(float $temperature): void {
        if ($temperature > 35) {
            echo "⚠️ ALERT: High temperature detected ({$temperature}°C)!\n";
        } elseif ($temperature < 0) {
            echo "❄️ ALERT: Freezing temperature detected ({$temperature}°C)!\n";
        }
    }
}

// -------------------
// Example Usage
// -------------------
try {
    $weatherStation = new WeatherStation();

    $display1 = new TemperatureDisplay("Main Hall");
    $display2 = new TemperatureDisplay("Lobby");
    $alertSystem = new AlertSystem();

    // Attach observers
    $weatherStation->attach($display1);
    $weatherStation->attach($display2);
    $weatherStation->attach($alertSystem);

    // Change temperature
    $weatherStation->setTemperature(25);
    $weatherStation->setTemperature(38);
    $weatherStation->setTemperature(-5);

    // Detach one display
    $weatherStation->detach($display2);

    // Change temperature again
    $weatherStation->setTemperature(15);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}