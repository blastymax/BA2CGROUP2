<?php 

interface PaymentGateway {
    public function pay(float $amount): string;
}

class PayPalGateway implements PaymentGateway {

    public function pay(float $amount): string {
        return "Paid $amount using PayPal.";
    }
}

class StripeAPI {
    public function makePayment(int $cents): string {
        return "Paid " . ($cents / 100) . " using Stripe.";
    }
}

class StripeAdapter implements PaymentGateway {
    private StripeAPI $stripe;

    public function __construct(StripeAPI $stripe) {
        $this->stripe = $stripe;
    }

    public function pay(float $amount): string {
        $cents = (int)($amount * 100);
        return $this->stripe->makePayment($cents);
    }
}

function checkout(PaymentGateway $gateway, float $amount) {
    echo $gateway->pay($amount);
}

// Existing PayPal usage
$paypal = new PayPalGateway();
checkout($paypal, 50.75);

echo "\n";

// New Stripe usage through Adapter
$stripe = new StripeAPI();

$stripeAdapter = new StripeAdapter($stripe);

checkout($stripeAdapter, 50.75);
