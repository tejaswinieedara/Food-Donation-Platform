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
            cd.id AS donation_id, 
            cd.name AS contactName, 
            cd.phone AS contactNumber, 
            cd.email AS contactEmail, 
            cd.condition AS donationCondition, 
            cd.donation_until AS donationUntil, 
            cd.location AS donationLocation,
            cd.delivery_option AS delivery_pickup,
			cd.additional_info AS additionalInfo,
            GROUP_CONCAT(cl.age_group SEPARATOR ', ') AS ageGroups,
            GROUP_CONCAT(cl.clothing_type SEPARATOR ', ') AS clothingTypes,
            GROUP_CONCAT(cl.quantity SEPARATOR ', ') AS quantities,
            GROUP_CONCAT(cl.gender SEPARATOR ', ') AS genders
        FROM 
            clothdonations cd
        JOIN 
            clothing_details cl ON cd.id = cl.donation_id
        WHERE 
            cd.donation_until >= :currentDate
        GROUP BY 
            cd.id
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