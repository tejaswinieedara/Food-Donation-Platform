<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$host = 'localhost'; // Change if your database is hosted elsewhere
$db = 'sustainfeed'; // Your database name
$user = 'root'; // Your MySQL username
$pass = ''; // Your MySQL password

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Prepare the response array
$response = ['success' => false];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $contactNumber = filter_input(INPUT_POST, 'contactNumber', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $additionalNotes = filter_input(INPUT_POST, 'additionalNotes', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $donationUntil = filter_input(INPUT_POST, 'donationUntil', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Change to FULL_SPECIAL_CHARS
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $deliveryPickup = filter_input(INPUT_POST, 'deliveryOption', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Debugging: Log received values
    error_log("Received donation until date: $donationUntil");

    // Validate required fields for food donations table
    if ($name && $contactNumber && !empty($donationUntil) && $location && !empty($deliveryPickup)) {
        // Prepare SQL statement for food donations table
        $stmtDonation = $pdo->prepare("INSERT INTO food_donations (name, contact_number, email, additional_notes, donation_until, location, delivery_pickup, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        
        // Execute the statement for food donations table
        if ($stmtDonation->execute([$name, $contactNumber, $email, $additionalNotes, $donationUntil, $location, $deliveryPickup])) {
            // Get the last inserted donation ID to associate food types with this donation.
            $donationId = $pdo->lastInsertId();

            // If food types were provided in the table format:
            if (!empty($_POST['foodType'])) {
                foreach ($_POST['foodType'] as $index => $type) { 
                    if (!empty($_POST['quantity'][$index])) { 
                        $quantityValue = $_POST['quantity'][$index];
                        $expiryValue = $_POST['expiryDate'][$index] ?? null; // Optional expiry date

                        // Prepare SQL statement for food types table
                        try {
                            $stmtFoodTypes = $pdo->prepare("INSERT INTO food_types (donation_id, food_type, quantity, expiry_date) VALUES (?, ?, ?, ?)");
                            if (!$stmtFoodTypes->execute([$donationId, $type, $quantityValue, $expiryValue])) {
                                throw new Exception("Failed to insert food details.");
                            }
                        } catch (Exception $e) {
                            error_log("Failed to insert food details: " . print_r($stmtFoodTypes->errorInfo(), true));
                            echo json_encode(['success' => false, 'message' => 'Database error while inserting food types.']);
                            exit;
                        }
                    }
                }
            }

            // If everything was successful:
            echo json_encode(['success' => true]);
            exit; // Exit after sending response
        } else { 
            echo json_encode(['success' => false, 'message' => 'Error saving the donation.']);
            exit; // Exit after sending response
        }
    } else {
        // Determine which field is missing and set the appropriate message
        if (!$name) { 
            $response['message'] = "Name is required."; 
        } elseif (!$contactNumber) { 
            $response['message'] = "Contact number is required."; 
        } elseif (empty($donationUntil)) { 
            $response['message'] = "Donation available until date is required."; 
        } elseif (!$location) { 
            $response['message'] = "Location is required."; 
        } elseif (empty($deliveryPickup)) { 
            $response['message'] = "Delivery/Pickup option must be selected."; 
        }
    }
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>