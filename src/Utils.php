<?php

function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}


function render(string $view, array $data = []): void
{
    extract($data);
    // Check for flash messages and make them available to the view
    $flashMessage = getFlashMessage();
    require PROJECT_ROOT . "/templates/layout.php";
}

function generateSlug(string $text): string
{
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

/**
 * Sets a message that will be displayed on the next page load.
 */
function setFlashMessage(string $type, string $message): void
{
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

/**
 * Retrieves and clears the flash message from the session.
 */
function getFlashMessage(): ?array
{
    $message = $_SESSION['flash_message'] ?? null;
    unset($_SESSION['flash_message']);
    return $message;
}


class WeaponPDFGenerator extends TCPDF
{
    private $companyName = 'Weapons Management System';
    private $companyAddress = '123 Main Street, City, State 12345';
    private $companyPhone = '(555) 123-4567';
    private $companyEmail = 'info@weaponsystem.com';

    public function __construct()
    {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $this->SetCreator('Weapons Management System');
        $this->SetAuthor('Weapons Management System');
        $this->SetTitle('Weapon Details');
        $this->SetSubject('Weapon Information Export');
        
        // Set margins
        $this->SetMargins(20, 30, 20);
        $this->SetHeaderMargin(10);
        $this->SetFooterMargin(15);
        
        // Set auto page breaks
        $this->SetAutoPageBreak(TRUE, 25);
        
        // Set font
        $this->SetFont('helvetica', '', 12);
    }

    /**
     * Generate PDF for a weapon with store information
     */
    public function generateWeaponPDF(array $weapon, array $store): void
    {
        $this->AddPage();
        
        // Add weapon content
        $this->addWeaponContent($weapon, $store);
        
        // Set headers for PDF download
        $filename = 'weapon_' . $weapon['serial_number'] . '_' . date('Y-m-d') . '.pdf';
        
        // Output PDF
        $this->Output($filename, 'I'); // 'I' = display in browser, 'D' = force download
    }

    /**
     * Generate PDF content and return as string (for bulk export)
     */
    public function generateWeaponPDFContent($weapon, $store): string
    {
        // Set document information
        $this->SetCreator('Weapon Management System');
        $this->SetAuthor($store['name']);
        $this->SetTitle('Weapon Details - ' . $weapon['name']);
        $this->SetSubject('Weapon Information');
        
        // Set margins
        $this->SetMargins(15, 27, 15);
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(10);
        
        // Set auto page breaks
        $this->SetAutoPageBreak(TRUE, 25);
        
        // Add a page
        $this->AddPage();
        
        // Set font
        $this->SetFont('helvetica', '', 12);
        
        // Create HTML content
        $html = $this->generateWeaponHTML($weapon, $store);
        
        // Print the HTML content
        $this->writeHTML($html, true, false, true, false, '');
        
        // Return this as string
        return $this->Output('', 'S');
    }

    /**
     * Generate HTML content for the weapon PDF
     */
    private function generateWeaponHTML($weapon, $store): string
    {
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            .header { text-align: center; margin-bottom: 30px; }
            .store-info { background-color: #f5f5f5; padding: 15px; margin-bottom: 20px; }
            .weapon-details { margin-bottom: 20px; }
            .detail-row { margin-bottom: 10px; }
            .label { font-weight: bold; color: #333; }
            .value { color: #666; }
            .status-badge { 
                padding: 5px 10px; 
                border-radius: 3px; 
                color: white; 
                font-weight: bold;
                display: inline-block;
            }
            .status-active { background-color: #28a745; }
            .status-sold { background-color: #dc3545; }
            .status-pending { background-color: #ffc107; color: #000; }
            .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
        </style>
        
        <div class="header">
            <h1>Weapon Details</h1>
            <p>Generated on ' . date('F j, Y \a\t g:i A') . '</p>
        </div>
        
        <div class="store-info">
            <h2>Store Information</h2>
            <div class="detail-row">
                <span class="label">Store Name:</span> 
                <span class="value">' . htmlspecialchars($store['name']) . '</span>
            </div>';
            
        if (!empty($store['address'])) {
            $html .= '
            <div class="detail-row">
                <span class="label">Address:</span> 
                <span class="value">' . htmlspecialchars($store['address']) . '</span>
            </div>';
        }
        
        if (!empty($store['phone'])) {
            $html .= '
            <div class="detail-row">
                <span class="label">Phone:</span> 
                <span class="value">' . htmlspecialchars($store['phone']) . '</span>
            </div>';
        }
        
        if (!empty($store['email'])) {
            $html .= '
            <div class="detail-row">
                <span class="label">Email:</span> 
                <span class="value">' . htmlspecialchars($store['email']) . '</span>
            </div>';
        }
        
        $html .= '
        </div>
        
        <div class="weapon-details">
            <h2>Weapon Information</h2>
            <div class="detail-row">
                <span class="label">Name:</span> 
                <span class="value">' . htmlspecialchars($weapon['name']) . '</span>
            </div>
            <div class="detail-row">
                <span class="label">Type:</span> 
                <span class="value">' . htmlspecialchars($weapon['type']) . '</span>
            </div>
            <div class="detail-row">
                <span class="label">Caliber:</span> 
                <span class="value">' . htmlspecialchars($weapon['caliber']) . '</span>
            </div>
            <div class="detail-row">
                <span class="label">Serial Number:</span> 
                <span class="value">' . htmlspecialchars($weapon['serial_number']) . '</span>
            </div>
            <div class="detail-row">
                <span class="label">Price:</span> 
                <span class="value">$' . number_format($weapon['price'], 2) . '</span>
            </div>
            <div class="detail-row">
                <span class="label">In Stock:</span> 
                <span class="value">' . ($weapon['in_stock'] ? 'Yes' : 'No') . '</span>
            </div>
            <div class="detail-row">
                <span class="label">Status:</span> 
                <span class="status-badge status-' . strtolower($weapon['status']) . '">' . 
                    ucfirst(htmlspecialchars($weapon['status'])) . '</span>
            </div>';
            
        if (!empty($weapon['description'])) {
            $html .= '
            <div class="detail-row">
                <span class="label">Description:</span><br>
                <span class="value">' . nl2br(htmlspecialchars($weapon['description'])) . '</span>
            </div>';
        }
        
        $html .= '
        </div>
        
        <div class="footer">
            <p>This document was generated by the Weapon Management System</p>
            <p>© ' . date('Y') . ' ' . htmlspecialchars($store['name']) . '</p>
        </div>';
        
        return $html;
    }
    /**
     * Custom header
     */
    public function Header(): void
    {
        // Company logo placeholder (you can add actual logo here)
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(0, 51, 102); // Dark blue
        $this->Cell(0, 15, $this->companyName, 0, 1, 'C');
        
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(128, 128, 128); // Gray
        $this->Cell(0, 5, $this->companyAddress, 0, 1, 'C');
        $this->Cell(0, 5, 'Phone: ' . $this->companyPhone . ' | Email: ' . $this->companyEmail, 0, 1, 'C');
        
        // Add a line
        $this->SetDrawColor(200, 200, 200);
        $this->Line(20, 35, 190, 35);
        
        $this->Ln(10);
    }

    /**
     * Custom footer
     */
    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        
        // Add a line
        $this->SetDrawColor(200, 200, 200);
        $this->Line(20, $this->GetY() - 5, 190, $this->GetY() - 5);
        
        $this->Cell(0, 10, 'Generated on ' . date('F j, Y \a\t g:i A'), 0, 0, 'L');
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'R');
    }

    /**
     * Add weapon content to PDF
     */
    private function addWeaponContent(array $weapon, array $store): void
    {
        $this->Ln(15);
        // Title
        $this->SetFont('helvetica', 'B', 18);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(0, 12, 'WEAPON DETAILS', 0, 1, 'C');
        $this->Ln(8);

        // Weapon Information Section
        $this->addSectionHeader('Weapon Information');
        
        $weaponData = [
            'Name' => $weapon['name'],
            'Type' => $weapon['type'],
            'Caliber' => $weapon['caliber'],
            'Serial Number' => $weapon['serial_number'],
            'Price' => '$' . number_format($weapon['price'], 2),
            'In Stock' => $weapon['in_stock'] ? 'Yes' : 'No',
            'Status' => ucfirst($weapon['status'])
        ];
        
        $this->addDataTable($weaponData);
        $this->Ln(10);

        // Store Information Section
        $this->addSectionHeader('Store Information');
        
        $storeData = [
            'Store Name' => $store['name'],
            'Address' => $store['address_line1'] . ($store['address_line2'] ? ', ' . $store['address_line2'] : ''),
            'City' => $store['city'],
            'State/Region' => $store['state_region'],
            'Country' => $store['country'],
            'Phone' => $store['phone'] ?: 'Not provided',
            'Email' => $store['email'] ?: 'Not provided'
        ];
        
        $this->addDataTable($storeData);
        $this->Ln(10);

        // Additional Information
        $this->addSectionHeader('Additional Information');
        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(60, 60, 60);
        
        $additionalInfo = "This document contains detailed information about the weapon listed above, ";
        $additionalInfo .= "including its current status and the store where it is located. ";
        $additionalInfo .= "For any inquiries regarding this weapon, please contact the store directly ";
        $additionalInfo .= "using the contact information provided above.";
        
        $this->MultiCell(0, 6, $additionalInfo, 0, 'J');
        
        // Status box
        $this->Ln(5);
        $this->addStatusBox($weapon);
    }

    /**
     * Add section header
     */
    private function addSectionHeader(string $title): void
    {
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 51, 102);
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 8, $title, 0, 1, 'L', true);
        $this->Ln(3);
    }

    /**
     * Add data table
     */
    private function addDataTable(array $data): void
    {
        $this->SetFont('helvetica', '', 11);
        
        foreach ($data as $label => $value) {
            // Label column
            $this->SetTextColor(80, 80, 80);
            $this->SetFont('helvetica', 'B', 11);
            $this->Cell(50, 7, $label . ':', 0, 0, 'L');
            
            // Value column
            $this->SetTextColor(40, 40, 40);
            $this->SetFont('helvetica', '', 11);
            $this->Cell(120, 7, $value, 0, 1, 'L');
        }
    }

    /**
     * Add status box
     */
    private function addStatusBox(array $weapon): void
    {
        // Determine status color
        $statusColor = [200, 200, 200]; // Default gray
        switch ($weapon['status']) {
            case 'active':
                $statusColor = [76, 175, 80]; // Green
                break;
            case 'sold':
                $statusColor = [244, 67, 54]; // Red
                break;
            case 'pending':
                $statusColor = [255, 193, 7]; // Yellow
                break;
        }

        // Create status box
        $this->SetFillColor($statusColor[0], $statusColor[1], $statusColor[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 12);
        
        $statusText = 'STATUS: ' . strtoupper($weapon['status']);
        if (!$weapon['in_stock']) {
            $statusText .= ' | OUT OF STOCK';
        }
        
        $this->Cell(0, 10, $statusText, 0, 1, 'C', true);
    }
}


class StoreValidator
{
    private $errors = [];
    private $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate all store data
     */
    public function validate(): bool
    {
        $this->validateName();
        $this->validateAddressLine1();
        $this->validateCity();
        $this->validateStateRegion();
        $this->validateCountry();
        $this->validatePhone();
        $this->validateEmail();

        return empty($this->errors);
    }

    /**
     * Get all validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    private function validateName(): void
    {
        $name = trim($this->data['name'] ?? '');
        
        if (empty($name)) {
            $this->errors['name'] = 'Store name is required.';
            return;
        }

        if (strlen($name) < 2) {
            $this->errors['name'] = 'Store name must be at least 2 characters long.';
            return;
        }

        if (strlen($name) > 255) {
            $this->errors['name'] = 'Store name cannot exceed 255 characters.';
            return;
        }

        // Check for valid characters (letters, numbers, spaces, basic punctuation)
        if (!preg_match('/^[a-zA-Z0-9\s\-\'\.\&\,]+$/', $name)) {
            $this->errors['name'] = 'Store name contains invalid characters.';
        }
    }

    private function validateAddressLine1(): void
    {
        $address = trim($this->data['address_line1'] ?? '');
        
        if (empty($address)) {
            $this->errors['address_line1'] = 'Address Line 1 is required.';
            return;
        }

        if (strlen($address) < 5) {
            $this->errors['address_line1'] = 'Address Line 1 must be at least 5 characters long.';
            return;
        }

        if (strlen($address) > 255) {
            $this->errors['address_line1'] = 'Address Line 1 cannot exceed 255 characters.';
        }
    }

    private function validateCity(): void
    {
        $city = trim($this->data['city'] ?? '');
        
        if (empty($city)) {
            $this->errors['city'] = 'City is required.';
            return;
        }

        if (strlen($city) < 2) {
            $this->errors['city'] = 'City must be at least 2 characters long.';
            return;
        }

        if (strlen($city) > 100) {
            $this->errors['city'] = 'City cannot exceed 100 characters.';
            return;
        }

        // Cities should only contain letters, spaces, apostrophes, and hyphens
        if (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $city)) {
            $this->errors['city'] = 'City contains invalid characters.';
        }
    }

    private function validateStateRegion(): void
    {
        $state = trim($this->data['state_region'] ?? '');
        
        if (empty($state)) {
            $this->errors['state_region'] = 'State/Region is required.';
            return;
        }

        if (strlen($state) < 2) {
            $this->errors['state_region'] = 'State/Region must be at least 2 characters long.';
            return;
        }

        if (strlen($state) > 100) {
            $this->errors['state_region'] = 'State/Region cannot exceed 100 characters.';
            return;
        }

        // States/regions should only contain letters, spaces, apostrophes, and hyphens
        if (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $state)) {
            $this->errors['state_region'] = 'State/Region contains invalid characters.';
        }
    }

    private function validateCountry(): void
    {
        $country = trim($this->data['country'] ?? '');
        
        if (empty($country)) {
            $this->errors['country'] = 'Country is required.';
            return;
        }

        if (strlen($country) < 2) {
            $this->errors['country'] = 'Country must be at least 2 characters long.';
            return;
        }

        if (strlen($country) > 100) {
            $this->errors['country'] = 'Country cannot exceed 100 characters.';
            return;
        }

        // Countries should only contain letters, spaces, apostrophes, and hyphens
        if (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $country)) {
            $this->errors['country'] = 'Country contains invalid characters.';
        }
    }

    private function validatePhone(): void
    {
        $phone = trim($this->data['phone'] ?? '');
        
        // Phone is optional, but if provided, validate it
        if (!empty($phone)) {
            if (strlen($phone) > 50) {
                $this->errors['phone'] = 'Phone number cannot exceed 50 characters.';
                return;
            }

            // Allow digits, spaces, hyphens, parentheses, plus sign
            if (!preg_match('/^[\d\s\-\(\)\+\.]+$/', $phone)) {
                $this->errors['phone'] = 'Phone number contains invalid characters.';
                return;
            }

            // Must contain at least 7 digits (minimum for a valid phone number)
            if (preg_match_all('/\d/', $phone) < 7) {
                $this->errors['phone'] = 'Phone number must contain at least 7 digits.';
            }
        }
    }

    private function validateEmail(): void
    {
        $email = trim($this->data['email'] ?? '');
        
        // Email is optional, but if provided, validate it
        if (!empty($email)) {
            if (strlen($email) > 255) {
                $this->errors['email'] = 'Email cannot exceed 255 characters.';
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = 'Please enter a valid email address.';
            }
        }
    }

}


class WeaponValidator
{
    private $errors = [];
    private $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate all weapon data
     */
    public function validate(): bool
    {
        $this->validateName();
        $this->validateStoreId();
        $this->validateType();
        $this->validateCaliber();
        $this->validateSerialNumber();
        $this->validatePrice();
        $this->validateInStock();
        $this->validateStatus();

        return empty($this->errors);
    }

    /**
     * Get all validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    private function validateName(): void
    {
        $name = trim($this->data['name'] ?? '');
        
        if (empty($name)) {
            $this->errors['name'] = 'Weapon name is required.';
            return;
        }

        if (strlen($name) < 2) {
            $this->errors['name'] = 'Weapon name must be at least 2 characters long.';
            return;
        }

        if (strlen($name) > 255) {
            $this->errors['name'] = 'Weapon name cannot exceed 255 characters.';
            return;
        }

        // Allow letters, numbers, spaces, hyphens, apostrophes, periods, and common punctuation
        if (!preg_match('/^[a-zA-Z0-9\s\-\'\.\&\,\(\)\/]+$/', $name)) {
            $this->errors['name'] = 'Weapon name contains invalid characters.';
        }
    }

    private function validateStoreId(): void
    {
        $storeId = $this->data['store_id'] ?? '';
        
        if (empty($storeId) || !is_numeric($storeId)) {
            $this->errors['store_id'] = 'Please select a valid store.';
            return;
        }

        $storeId = (int)$storeId;
        if ($storeId <= 0) {
            $this->errors['store_id'] = 'Please select a valid store.';
            return;
        }

        // Check if store exists
        if (!$this->storeExists($storeId)) {
            $this->errors['store_id'] = 'Selected store does not exist.';
        }
    }

    private function validateType(): void
    {
        $type = trim($this->data['type'] ?? '');
        
        if (empty($type)) {
            $this->errors['type'] = 'Weapon type is required.';
            return;
        }

        if (strlen($type) < 2) {
            $this->errors['type'] = 'Weapon type must be at least 2 characters long.';
            return;
        }

        if (strlen($type) > 100) {
            $this->errors['type'] = 'Weapon type cannot exceed 100 characters.';
            return;
        }

        // Allow letters, spaces, hyphens, and periods
        if (!preg_match('/^[a-zA-Z\s\-\.]+$/', $type)) {
            $this->errors['type'] = 'Weapon type should only contain letters, spaces, hyphens, and periods.';
        }
    }

    private function validateCaliber(): void
    {
        $caliber = trim($this->data['caliber'] ?? '');
        
        if (empty($caliber)) {
            $this->errors['caliber'] = 'Caliber is required.';
            return;
        }

        if (strlen($caliber) > 50) {
            $this->errors['caliber'] = 'Caliber cannot exceed 50 characters.';
            return;
        }

        // Allow letters, numbers, periods, spaces, and common caliber symbols
        if (!preg_match('/^[a-zA-Z0-9\s\.\-\/\+x×]+$/', $caliber)) {
            $this->errors['caliber'] = 'Caliber contains invalid characters.';
        }
    }

    private function validateSerialNumber(): void
    {
        $serialNumber = trim($this->data['serial_number'] ?? '');
        
        if (empty($serialNumber)) {
            $this->errors['serial_number'] = 'Serial number is required.';
            return;
        }

        if (strlen($serialNumber) < 3) {
            $this->errors['serial_number'] = 'Serial number must be at least 3 characters long.';
            return;
        }

        if (strlen($serialNumber) > 255) {
            $this->errors['serial_number'] = 'Serial number cannot exceed 255 characters.';
            return;
        }

        // Allow alphanumeric characters, hyphens, and underscores
        if (!preg_match('/^[a-zA-Z0-9\-\_]+$/', $serialNumber)) {
            $this->errors['serial_number'] = 'Serial number should only contain letters, numbers, hyphens, and underscores.';
        }
    }

    private function validatePrice(): void
    {
        $price = $this->data['price'] ?? '';
        
        if (empty($price) && $price !== '0' && $price !== 0) {
            $this->errors['price'] = 'Price is required.';
            return;
        }

        if (!is_numeric($price)) {
            $this->errors['price'] = 'Price must be a valid number.';
            return;
        }

        $price = (float)$price;
        
        if ($price < 0) {
            $this->errors['price'] = 'Price cannot be negative.';
            return;
        }

        if ($price > 999999.99) {
            $this->errors['price'] = 'Price cannot exceed $999,999.99.';
        }

        // Check for reasonable decimal places (max 2)
        if (round($price, 2) != $price) {
            $this->errors['price'] = 'Price can have at most 2 decimal places.';
        }
    }

    private function validateInStock(): void
    {
        $inStock = $this->data['in_stock'] ?? '';
        
        if (empty($inStock) && $inStock !== '0' && $inStock !== 0) {
            $this->errors['in_stock'] = 'Stock quantity is required.';
            return;
        }

        if (!is_numeric($inStock)) {
            $this->errors['in_stock'] = 'Stock quantity must be a valid number.';
            return;
        }

        $inStock = (int)$inStock;
        
        if ($inStock < 0) {
            $this->errors['in_stock'] = 'Stock quantity cannot be negative.';
            return;
        }

        if ($inStock > 999999) {
            $this->errors['in_stock'] = 'Stock quantity cannot exceed 999,999.';
        }
    }

    private function validateStatus(): void
    {
        $status = trim($this->data['status'] ?? '');
        $validStatuses = ['active', 'discontinued', 'out_of_stock'];
        
        if (empty($status)) {
            $this->errors['status'] = 'Status is required.';
            return;
        }

        if (!in_array($status, $validStatuses)) {
            $this->errors['status'] = 'Invalid status selected.';
        }
    }

    /**
     * Check if a store exists
     */
    private function storeExists(int $storeId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM stores WHERE id = :id");
        $stmt->execute([':id' => $storeId]);
        
        return $stmt->fetchColumn() > 0;
    }


}

class CsvWeaponValidator
{
    private array $validStoreIds;
    private array $errors = [];
    
    public function __construct(array $validStoreIds)
    {
        $this->validStoreIds = $validStoreIds;
    }
    
    /**
     * Validate weapon data from CSV import
     */
    public function validateWeapon(array $data, int $lineNumber): array
    {
        $errors = [];

        // Required fields validation
        if (empty(trim($data['name'] ?? ''))) {
            $errors[] = 'Name is required';
        } elseif (strlen(trim($data['name'])) > 255) {
            $errors[] = 'Name must be less than 255 characters';
        }

        if (empty(trim($data['serial_number'] ?? ''))) {
            $errors[] = 'Serial number is required';
        } elseif (strlen(trim($data['serial_number'])) > 100) {
            $errors[] = 'Serial number must be less than 100 characters';
        } elseif (!preg_match('/^[A-Za-z0-9\-_]+$/', trim($data['serial_number']))) {
            $errors[] = 'Serial number can only contain letters, numbers, hyphens, and underscores';
        }

        // Type validation
        if (!empty($data['type']) && strlen(trim($data['type'])) > 100) {
            $errors[] = 'Type must be less than 100 characters';
        }

        // Caliber validation
        if (!empty($data['caliber']) && strlen(trim($data['caliber'])) > 50) {
            $errors[] = 'Caliber must be less than 50 characters';
        }

        // Price validation
        $price = $data['price'] ?? 0;
        if (!is_numeric($price)) {
            $errors[] = 'Price must be a valid number';
        } elseif ((float)$price < 0) {
            $errors[] = 'Price cannot be negative';
        } elseif ((float)$price > 999999.99) {
            $errors[] = 'Price cannot exceed $999,999.99';
        }

        // Store ID validation
        if (empty($data['store_id'])) {
            $errors[] = 'Store ID is required';
        } elseif (!is_numeric($data['store_id'])) {
            $errors[] = 'Store ID must be a valid number';
        } elseif (!in_array((int)$data['store_id'], $this->validStoreIds)) {
            $errors[] = 'Store ID does not exist in the system';
        }

        // In stock validation
        $inStock = $data['in_stock'] ?? 1;
        if (!in_array($inStock, [0, 1, '0', '1'])) {
            $errors[] = 'In stock must be 1 (yes) or 0 (no)';
        }

        // Status validation
        $validStatuses = ['active', 'sold', 'discontinued'];
        $status = strtolower(trim($data['status'] ?? 'active'));
        if (!in_array($status, $validStatuses)) {
            $errors[] = 'Status must be: ' . implode(', ', $validStatuses);
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'line' => $lineNumber
        ];
    }

    /**
     * Validate CSV file structure
     */
    public function validateCsvStructure(array $csvData, bool $hasHeaders = true): array
    {
        $errors = [];
        
        if (empty($csvData)) {
            $errors[] = 'CSV file is empty';
            return ['valid' => false, 'errors' => $errors];
        }

        // Expected columns
        $expectedColumns = ['name', 'type', 'caliber', 'serial_number', 'price', 'store_id', 'in_stock', 'status'];
        $minColumns = 4; // name, serial_number, price, store_id are minimum required

        // Check first row (headers or data)
        $firstRow = $csvData[0];
        $columnCount = count($firstRow);

        if ($columnCount < $minColumns) {
            $errors[] = "CSV must have at least {$minColumns} columns";
        }

        if ($hasHeaders) {
            // Validate header names if present
            $headers = array_map('strtolower', array_map('trim', $firstRow));
            $missingHeaders = [];
            
            foreach (['name', 'serial_number', 'price', 'store_id'] as $required) {
                if (!in_array($required, $headers)) {
                    $missingHeaders[] = $required;
                }
            }
            
            if (!empty($missingHeaders)) {
                $errors[] = 'Missing required headers: ' . implode(', ', $missingHeaders);
            }
        }

        // Check for consistent column count across rows
        $inconsistentRows = [];
        foreach ($csvData as $index => $row) {
            if (count($row) !== $columnCount) {
                $inconsistentRows[] = $index + 1;
            }
        }

        if (!empty($inconsistentRows)) {
            $errors[] = 'Inconsistent column count on lines: ' . implode(', ', array_slice($inconsistentRows, 0, 10));
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'column_count' => $columnCount,
            'expected_columns' => $expectedColumns
        ];
    }

    /**
     * Clean and normalize weapon data
     */
    public function cleanWeaponData(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'type' => trim($data['type'] ?? ''),
            'caliber' => trim($data['caliber'] ?? ''),
            'serial_number' => strtoupper(trim($data['serial_number'] ?? '')),
            'price' => round((float)($data['price'] ?? 0), 2),
            'store_id' => (int)($data['store_id'] ?? 0),
            'in_stock' => (int)($data['in_stock'] ?? 1),
            'status' => strtolower(trim($data['status'] ?? 'active'))
        ];
    }

    /**
     * Check for duplicate serial numbers within the CSV
     */
    public function findDuplicateSerialNumbers(array $weaponDataArray): array
    {
        $serialNumbers = [];
        $duplicates = [];

        foreach ($weaponDataArray as $index => $weapon) {
            $serial = strtoupper(trim($weapon['serial_number'] ?? ''));
            
            if (empty($serial)) {
                continue;
            }

            if (isset($serialNumbers[$serial])) {
                if (!isset($duplicates[$serial])) {
                    $duplicates[$serial] = [$serialNumbers[$serial]];
                }
                $duplicates[$serial][] = $index + 1;
            } else {
                $serialNumbers[$serial] = $index + 1;
            }
        }

        return $duplicates;
    }

    /**
     * Generate validation summary
     */
    public function generateValidationSummary(array $results): array
    {
        $summary = [
            'total_rows' => count($results),
            'valid_rows' => 0,
            'invalid_rows' => 0,
            'error_details' => []
        ];

        foreach ($results as $result) {
            if ($result['valid']) {
                $summary['valid_rows']++;
            } else {
                $summary['invalid_rows']++;
                $summary['error_details'][] = "Line {$result['line']}: " . implode(', ', $result['errors']);
            }
        }

        return $summary;
    }
}