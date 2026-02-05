interface State {
    public function render(): void;
    public function publish(): void;
    public function reject(): void;
}
class Document {
    private State $state;

    public function __construct() {
        // Initial state is always Draft
        $this->transitionTo(new DraftState($this));
    }

    public function transitionTo(State $state): void {
        echo "Context: Transitioning to " . get_class($state) . ".\n";
        $this->state = $state;
    }

    public function render(): void {
        $this->state->render();
    }

    public function publish(): void {
        $this->state->publish();
    }

    public function reject(): void {
        $this->state->reject();
    }
}
class DraftState implements State {
    private Document $document;

    public function __construct(Document $document) {
        $this->document = $document;
    }

    public function render(): void {
        echo "Draft: Only the author can see this document.\n";
    }

    public function publish(): void {
        echo "Draft: Sending document to moderation...\n";
        $this->document->transitionTo(new ModerationState($this->document));
    }

    public function reject(): void {
        echo "Draft: Cannot reject a draft; it is already at the start.\n";
    }
}

class ModerationState implements State {
    private Document $document;

    public function __construct(Document $document) {
        $this->document = $document;
    }

    public function render(): void {
        echo "Moderation: Admins are reviewing the document.\n";
    }

    public function publish(): void {
        echo "Moderation: Document approved! Making it public.\n";
        $this->document->transitionTo(new PublishedState($this->document));
    }

    public function reject(): void {
        echo "Moderation: Document rejected. Moving back to draft.\n";
        $this->document->transitionTo(new DraftState($this->document));
    }
}

class PublishedState implements State {
    private Document $document;

    public function __construct(Document $document) {
        $this->document = $document;
    }

    public function render(): void {
        echo "Published: Everyone can see this document on the website.\n";
    }

    public function publish(): void {
        echo "Published: Document is already public.\n";
    }

    public function reject(): void {
        echo "Published: Taking document down for edits...\n";
        $this->document->transitionTo(new DraftState($this->document));
    }
}
// --- Usage ---

$doc = new Document();

echo "--- Current Status ---\n";
$doc->render();

echo "\n--- Attempting to Publish ---\n";
$doc->publish(); // Moves to Moderation

echo "\n--- Current Status ---\n";
$doc->render();

echo "\n--- Admin Approves ---\n";
$doc->publish(); // Moves to Published

echo "\n--- Final Status ---\n";
$doc->render();

echo "\n--- Emergency Takedown ---\n";
$doc->reject(); // Moves back to Draft