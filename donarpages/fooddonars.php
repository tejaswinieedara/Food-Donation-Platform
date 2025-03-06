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
            fd.id AS donation_id, 
            fd.name AS name, 
            fd.contact_number AS contact_number, 
            fd.email AS email, 
            fd.additional_notes AS additional_notes, 
            fd.donation_until AS donation_until, 
            fd.location AS location,
            fd.delivery_pickup AS delivery_pickup,
            GROUP_CONCAT(ft.food_type SEPARATOR ', ') AS food_types,
            GROUP_CONCAT(ft.quantity SEPARATOR ', ') AS quantities,
            GROUP_CONCAT(ft.expiry_date SEPARATOR ', ') AS expiry_dates
        FROM 
            food_donations fd
        JOIN 
            food_types ft ON fd.id = ft.donation_id
        WHERE 
            fd.donation_until >= :currentDate
        GROUP BY 
            fd.id
    ");
    
    $stmt->execute(['currentDate' => $currentDate]);
    
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($donations)) {
        echo json_encode(['message' => 'There are currently no available food donations.']);
    } else {
        echo json_encode($donations); // Return as JSON
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>