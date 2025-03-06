<?php
session_start();
require 'database.php'; // Include your database connection script
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_SESSION['username']; // Use the session username
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];

    // Set registration date to the current date
    $registrationDate = date('Y-m-d'); // Get the current date in YYYY-MM-DD format
    $donationUntil = $_POST['expiryDate'];
    $location = $_POST['location'];
    $deliveryOption = $_POST['deliveryOption'];
    $additionalNotes = $_POST['additionalNotes'];

    // Prepare food items
    $foodTypes = $_POST['foodType'];
    $quantities = $_POST['quantity'];
    $expiryDates = $_POST['expiryDate']; // Get expiry dates

    // Validate input
    if (empty($contactNumber) || empty($location) || empty($donationUntil)) {
        echo json_encode(['success' => false, 'message' => 'Required fields cannot be empty.']);
        exit();
    }

    // Validate phone number format
    if (!preg_match('/^[0-9]{10}$/', $contactNumber)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid phone number.']);
        exit();
    }

    // Insert donation details into the database
    try {
        $stmt = $db->prepare("INSERT INTO donations (name, contact_number, email, registration_date, donation_until, location, delivery_option, additional_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $contactNumber, $email, $registrationDate, $donationUntil, $location, $deliveryOption, $additionalNotes]);

        // Get the last inserted ID for food items
        $donationId = $db->lastInsertId();

        // Insert food items into the database
        foreach ($foodTypes as $index => $foodType) {
            $quantity = $quantities[$index];
            $expiryDate = !empty($expiryDates[$index]) ? $expiryDates[$index] : null; // Handle empty expiry dates
            
            // Debugging - Log the values being inserted
            error_log("Inserting Food Type: $foodType, Quantity: $quantity, Expiry Date: $expiryDate");

            // Only insert if foodType and quantity are not empty
            if (!empty($foodType) && !empty($quantity)) {
                $stmt = $db->prepare("INSERT INTO food_items (donation_id, food_type, quantity, expiry_date) VALUES (?, ?, ?, ?)");
                $stmt->execute([$donationId, $foodType, $quantity, $expiryDate]);
            } else {
                error_log("Skipping empty food type or quantity for index $index");
            }
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>