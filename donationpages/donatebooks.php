<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$host = 'localhost'; // Change if your database is hosted elsewhere
$db = 'sustainfeed'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

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

    // Validate required fields
    if ($name && $phone && $email && !empty($condition) && $donationUntil && $location) {

        // Prepare SQL statement for book donations table
        $stmtDonation = $pdo->prepare("INSERT INTO bookdonations (name, phone, email, `condition`, donation_until, location) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Execute the statement for book donations table
        if ($stmtDonation->execute([$name, $phone, $email, $condition, $donationUntil, $location])) {

            // Get the last inserted donation ID to associate books with this donation.
            $donationId = $pdo->lastInsertId();

            // If book details were provided in the table format:
            if (!empty($_POST['bookType'])) {
                foreach ($_POST['bookType'] as $index => $type) { 
                    if (!empty($_POST['subject'][$index]) && !empty($_POST['author'][$index]) && !empty($_POST['quantity'][$index])) { 
                        $subjectValue = $_POST['subject'][$index];
                        $authorValue = $_POST['author'][$index];
                        $quantityValue = $_POST['quantity'][$index];

                        // Prepare SQL statement for book details table.
                        try {
                            $stmtBookDetails = $pdo->prepare("INSERT INTO book_details (donation_id, book_type, subject_category, quantity, author_name) VALUES (?, ?, ?, ?, ?)");
                            if (!$stmtBookDetails->execute([$donationId, $type, $subjectValue, $quantityValue, $authorValue])) {
                                throw new Exception("Failed to insert book details.");
                            }
                        } catch (Exception $e) {
                            error_log("Failed to insert book details: " . print_r($stmtBookDetails->errorInfo(), true));
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
            $response['message'] = "Condition of books must be selected."; 
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