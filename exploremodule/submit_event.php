<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (leave blank)
$dbname = "sustainfeed"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO events (event_name, event_description, event_date, event_time, location, organizer_name, organization_name, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $event_name, $event_description, $event_date, $event_time, $location, $organizer_name, $organization_name, $email, $phone);

// Get the form data
$event_name = $_POST['event-name'];
$event_description = $_POST['event-description'];
$event_date = $_POST['event-date'];
$event_time = $_POST['event-time'];
$location = $_POST['location'];
$organizer_name = $_POST['organizer-name'];
$organization_name = $_POST['organization-name'];
$email = $_POST['email'];
$phone = $_POST['phone'];

// Execute the statement
if ($stmt->execute()) {
    header("Location: thankyou.html");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>