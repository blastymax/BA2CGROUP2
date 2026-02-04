<?php

// ==========================================
// 1. THE IMPLEMENTATION LAYER
// ==========================================

/**
 * The Implementation Interface.
 * This defines the low-level operations that all concrete implementations
 * (renderers) must support.
 */
interface Renderer {
    public function renderHeader(string $title): string;
    public function renderBody(string $content, array $data = []): string;
    public function renderFooter(): string;
    public function renderImage(string $url): string;
}

/**
 * Concrete Implementation A: HTML
 * Renders the content as standard Web HTML.
 */
class HtmlRenderer implements Renderer {
    public function renderHeader(string $title): string {
        return "<html><body>\n<h1>$title</h1>\n<hr>";
    }

    public function renderBody(string $content, array $data = []): string {
        $html = "<div class='content'>\n  <p>$content</p>\n";
        if (!empty($data)) {
            $html .= "  <ul>\n";
            foreach ($data as $label => $value) {
                $html .= "    <li><strong>$label:</strong> $value</li>\n";
            }
            $html .= "  </ul>\n";
        }
        $html .= "</div>";
        return $html;
    }

    public function renderFooter(): string {
        return "<hr>\n<footer>&copy; " . date('Y') . " MyCompany</footer>\n</body></html>";
    }

    public function renderImage(string $url): string {
        return "<img src='$url' alt='Image' />";
    }
}

/**
 * Concrete Implementation B: JSON
 * Renders the content as a JSON string (e.g., for a Mobile App API).
 */
class JsonRenderer implements Renderer {
    public function renderHeader(string $title): string {
        // JSON doesn't strictly have headers in the visual sense, 
        // but we can start a data structure.
        return sprintf('{"title": "%s",', $title);
    }

    public function renderBody(string $content, array $data = []): string {
        // We format the body content as JSON properties
        return sprintf(
            ' "body": "%s", "metadata": %s,',
            $content,
            json_encode($data)
        );
    }

    public function renderFooter(): string {
        // Close the JSON object
        return sprintf(' "copyright": "%s" }', date('Y'));
    }

    public function renderImage(string $url): string {
        // In JSON, an image is usually just a URL string field
        return ''; // Handled differently in JSON structure usually, simplified here.
    }
}

// ==========================================
// 2. THE ABSTRACTION LAYER
// ==========================================

/**
 * The Abstraction.
 * This class defines the high-level control logic. It maintains a reference
 * (the "Bridge") to the Implementation object.
 */
abstract class Page {
    // THE BRIDGE: usage of the interface, not a concrete class
    protected Renderer $renderer;

    public function __construct(Renderer $renderer) {
        $this->renderer = $renderer;
    }

    /**
     * This method delegates the actual rendering work to the implementation object.
     * The Page class doesn't care IF it's HTML or JSON, it just says "Render this."
     */
    abstract public function view(): string;
    
    // You can also allow switching the implementation at runtime
    public function changeRenderer(Renderer $renderer): void {
        $this->renderer = $renderer;
    }
}

/**
 * Refined Abstraction 1: A Standard Simple Page
 */
class SimplePage extends Page {
    protected string $title;
    protected string $content;

    public function __construct(Renderer $renderer, string $title, string $content) {
        parent::__construct($renderer);
        $this->title = $title;
        $this->content = $content;
    }

    public function view(): string {
        return implode("\n", [
            $this->renderer->renderHeader($this->title),
            $this->renderer->renderBody($this->content),
            $this->renderer->renderFooter()
        ]);
    }
}

/**
 * Refined Abstraction 2: A Product Page
 * This page has specific logic for displaying product specs (price, sku).
 */
class ProductPage extends Page {
    protected string $productName;
    protected string $description;
    protected float $price;
    protected string $sku;
    protected string $imageUrl;

    public function __construct(
        Renderer $renderer, 
        string $productName, 
        string $description,
        float $price,
        string $sku,
        string $imageUrl
    ) {
        parent::__construct($renderer);
        $this->productName = $productName;
        $this->description = $description;
        $this->price = $price;
        $this->sku = $sku;
        $this->imageUrl = $imageUrl;
    }

    public function view(): string {
        // Logic specific to products: assemble the data array
        $productData = [
            'Price' => '$' . number_format($this->price, 2),
            'SKU' => $this->sku
        ];

        // Notice we mix specific renderer calls (renderImage) with general ones
        $parts = [
            $this->renderer->renderHeader($this->productName),
            $this->renderer->renderImage($this->imageUrl),
            $this->renderer->renderBody($this->description, $productData),
            $this->renderer->renderFooter()
        ];

        return implode("\n", array_filter($parts));
    }
}

// ==========================================
// 3. THE CLIENT CODE
// ==========================================

// 1. Setup Renderers (The Implementations)
$html = new HtmlRenderer();
$json = new JsonRenderer();

echo "--- SCENARIO 1: Simple Page in HTML ---\n";
$page1 = new SimplePage($html, "Home", "Welcome to our website.");
echo $page1->view();
echo "\n\n";

echo "--- SCENARIO 2: Same Simple Page switched to JSON ---\n";
// The Bridge allows us to swap the implementation dynamically!
$page1->changeRenderer($json);
echo $page1->view();
echo "\n\n";

echo "--- SCENARIO 3: Product Page in HTML ---\n";
$product = new ProductPage(
    $html, 
    "Super Laptop", 
    "The fastest laptop ever.", 
    1999.99, 
    "LPT-9000",
    "http://imgs.com/laptop.jpg"
);
echo $product->view();
echo "\n\n";

echo "--- SCENARIO 4: Product Page in JSON ---\n";
// We can create a new object with the JSON renderer injected
$productJson = new ProductPage(
    $json, 
    "Super Laptop", 
    "The fastest laptop ever.", 
    1999.99, 
    "LPT-9000",
    "http://imgs.com/laptop.jpg"
);
echo $productJson->view();