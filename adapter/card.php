<?php
interface LocalATMCard {
    public function insertToLocalATM(): string;
}

class InternationalCard {
    public function insertToInternationalSlot(): string {
        return "International card inserted using global slot.";
    }
}
class CardAdapter implements LocalATMCard {

    private InternationalCard $internationalCard;

    public function __construct(InternationalCard $internationalCard) {
        $this->internationalCard = $internationalCard;
    }

    public function insertToLocalATM(): string {
        // Convert international action to local ATM compatibility
        return $this->internationalCard->insertToInternationalSlot() . " Adapted for Local ATM!";
    }
}

class LocalATM {
    public function readCard(LocalATMCard $card): void {
        echo $card->insertToLocalATM();
    }
}

$atm = new LocalATM();
$foreignCard = new InternationalCard();
$adapter = new CardAdapter($foreignCard);

$atm->readCard($adapter);
