<?php
// Enable error reporting
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
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $condition = filter_input(INPUT_POST, 'condition', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $donationUntil = filter_input(INPUT_POST, 'donationUntilDate', FILTER_DEFAULT); 
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate required fields for book donations table
    if ($name && $phone && $email && !empty($condition) && $donationUntil && $location) {

        // Prepare SQL statement for book donations table
        $stmtDonation = $pdo->prepare("INSERT INTO clothdonations (name, phone, email, `condition`, donation_until, location, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        
        // Execute the statement for book donations table
        if ($stmtDonation->execute([$name, $phone, $email, $condition, $donationUntil, $location])) {

            // Get the last inserted donation ID to associate clothing details with this donation.
            $donationId = $pdo->lastInsertId();

            // If clothing details were provided in the table format:
            if (!empty($_POST['clothingType'])) {
                foreach ($_POST['clothingType'] as $index => $type) { 
                    if (!empty($_POST['ageGroup'][$index]) && !empty($_POST['quantity'][$index]) && !empty($_POST['gender'][$index])) { 
                        $ageGroupValue = $_POST['ageGroup'][$index];
                        $quantityValue = $_POST['quantity'][$index];
                        $genderValue = $_POST['gender'][$index];

                        // Prepare SQL statement for clothing details table.
                        try {
                            $stmtClothingDetails = $pdo->prepare("INSERT INTO clothing_details (donation_id, age_group, clothing_type, quantity, gender) VALUES (?, ?, ?, ?, ?)");
                            if (!$stmtClothingDetails->execute([$donationId, $ageGroupValue, $type, $quantityValue, $genderValue])) {
                                throw new Exception("Failed to insert clothing details.");
                            }
                        } catch (Exception $e) {
                            error_log("Failed to insert clothing details: " . print_r($stmtClothingDetails->errorInfo(), true));
                            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                            exit;
                        }
                    }
                }
            }

            // If everything was successful:
            $response['success'] = true;

        } else { 
            $response['message'] = 'Error saving the donation.';
        }
    } else { 
        if (!$name) { 
            $response['message'] = "Name is required."; 
        } elseif (!$phone) { 
            $response['message'] = "Phone number is required."; 
        } elseif (!$email) { 
            $response['message'] = "Email is required."; 
        } elseif (empty($condition)) { 
            $response['message'] = "Condition of clothing must be selected."; 
        } elseif (!$donationUntil) { 
            $response['message'] = "Donation available until date is required."; 
        } elseif (!$location) { 
            $response['message'] = "Location is required."; 
        } 
    }
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>