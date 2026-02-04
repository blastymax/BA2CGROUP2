<?php
/**
 * Builder Design Pattern Example in PHP 8
 * ----------------------------------------
 * This example demonstrates how to use the Builder pattern
 * to construct complex objects step-by-step.
 */

/**
 * Product Class: Car
 */
class Car
{
    private string $engine;
    private int $seats;
    private bool $gps;

    public function setEngine(string $engine): void
    {
        if (empty($engine)) {
            throw new InvalidArgumentException("Engine type cannot be empty.");
        }
        $this->engine = $engine;
    }

    public function setSeats(int $seats): void
    {
        if ($seats <= 0) {
            throw new InvalidArgumentException("Seats must be greater than zero.");
        }
        $this->seats = $seats;
    }

    public function setGPS(bool $gps): void
    {
        $this->gps = $gps;
    }

    public function showSpecifications(): void
    {
        echo "Car Specifications:\n";
        echo "Engine: {$this->engine}\n";
        echo "Seats: {$this->seats}\n";
        echo "GPS: " . ($this->gps ? "Yes" : "No") . "\n";
    }
}

/**
 * Builder Interface
 */
interface CarBuilderInterface
{
    public function reset(): void;
    public function setEngine(string $engine): void;
    public function setSeats(int $seats): void;
    public function setGPS(bool $gps): void;
    public function getResult(): Car;
}

/**
 * Concrete Builder
 */
class CarBuilder implements CarBuilderInterface
{
    private Car $car;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->car = new Car();
    }

    public function setEngine(string $engine): void
    {
        $this->car->setEngine($engine);
    }

    public function setSeats(int $seats): void
    {
        $this->car->setSeats($seats);
    }

    public function setGPS(bool $gps): void
    {
        $this->car->setGPS($gps);
    }

    public function getResult(): Car
    {
        $result = $this->car;
        $this->reset(); // Prepare for next build
        return $result;
    }
}

/**
 * Director Class
 * Controls the building process
 */
class Director
{
    public function buildSportsCar(CarBuilderInterface $builder): void
    {
        $builder->setEngine("V8 Turbo");
        $builder->setSeats(2);
        $builder->setGPS(true);
    }

    public function buildSUV(CarBuilderInterface $builder): void
    {
        $builder->setEngine("V6 Diesel");
        $builder->setSeats(7);
        $builder->setGPS(true);
    }
}

/**
 * Client Code
 */
try {
    $director = new Director();
    $builder = new CarBuilder();

    // Build a sports car
    $director->buildSportsCar($builder);
    $sportsCar = $builder->getResult();
    $sportsCar->showSpecifications();

    echo "\n";

    // Build an SUV
    $director->buildSUV($builder);
    $suv = $builder->getResult();
    $suv->showSpecifications();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}