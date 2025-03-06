<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$host = 'localhost'; // Change if your DB is hosted elsewhere
$db = 'sustainfeed'; // Your database name
$user = 'root';      // Your MySQL username
$pass = '';          // Your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get current date in YYYY-MM-DD format
    $currentDate = date('Y-m-d');

    // Fetch donations and their details from the database
    $stmt = $pdo->prepare("
        SELECT 
            bd.id AS donation_id, 
            bd.name AS contactName, 
            bd.phone AS contactNumber, 
            bd.email AS contactEmail, 
            bd.condition AS donationCondition, 
            bd.donation_until AS donationUntil, 
            bd.location AS donationLocation,
            bd.delivery_pickup,
            GROUP_CONCAT(b.book_type SEPARATOR ', ') AS bookTypes,
            GROUP_CONCAT(b.subject_category SEPARATOR ', ') AS subjectCategories,
            GROUP_CONCAT(b.quantity SEPARATOR ', ') AS quantities,
            GROUP_CONCAT(b.author_name SEPARATOR ', ') AS authorNames
        FROM 
            bookdonations bd
        JOIN 
            book_details b ON bd.id = b.donation_id
        WHERE 
            bd.donation_until >= :currentDate
        GROUP BY 
            bd.id
    ");
    
    $stmt->execute(['currentDate' => $currentDate]);
    
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($donations)) {
        echo json_encode(['message' => 'There are currently no available donations.']);
    } else {
        echo json_encode($donations); // Return as JSON
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>