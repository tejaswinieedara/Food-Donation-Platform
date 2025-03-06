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

// Get today's date for comparison
$today = date('Y-m-d');

// Determine which toggle is selected
$view = isset($_GET['view']) ? $_GET['view'] : 'top_stories'; // Default to 'top_stories'

// Fetch events based on the selected view
if ($view === 'upcoming_events') {
    $sql = "SELECT * FROM events WHERE event_date > '$today' ORDER BY event_date ASC";
} else {
    $sql = "SELECT * FROM events WHERE event_date = '$today' ORDER BY event_time ASC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Events</title>
    <style>
body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f0f4c3, #c8e6c9);
        }

        .header {
            background: linear-gradient(135deg, #66b669, #b1b67c);
            color: rgb(4, 20, 9);
            padding: 10px 0;
            text-align: center;
        }

.header h1 {
    font-size: 3em; /* Larger font size for the header */
    color:darkgreen;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
}

.toggle-container {
    text-align: center;
    margin: 20px 0;
}

.toggle-button {
    display: inline-block;
    padding: 15px 30px;
    background-color: #4CAF50; /* Green background */
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 25px; /* Rounded buttons */
    font-size: 1.2em;
    transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth transitions */
    box-shadow: 0 4px 10px rgba(0,0,0,0.2); /* Shadow for button */
}

.toggle-button.active {
    background-color: #388E3C; /* Darker green for active button */
}

.toggle-button:hover {
    background-color: #45a049; /* Lighter green on hover */
    transform: scale(1.05); /* Slightly enlarge on hover */
}

.event-container {
    background-color: white; /* White background for events */
    padding: 20px;
    margin-bottom: 10px;
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 15px rgba(0,0,0,0.1); /* Shadow for depth */
    transition: transform 0.3s ease; /* Smooth transform effect */
    max-width: 1000px; /* Set a maximum width for the container */
    width: 100%; /* Full width within its parent element */
    margin-left: auto; /* Center align horizontally */
    margin-right: auto; 
}

.event-container:hover {
    transform: translateY(-5px); /* Lift effect on hover */
}

.event-container h2 {
    margin-top: 10;
    color: #333; /* Darker color for event title */
}

.event-details {
    margin-bottom: 10px;
}

.register-link {
    display: block;
    text-align: center;
    margin-top: 30px;
    font-weight: bold;
    font-size: 1.1em;
    color: #4CAF50; /* Link color */
}

.register-link:hover {
    text-decoration: underline; /* Underline on hover */
}

/* Responsive Design */
@media (max-width: 600px) {
    .toggle-button {
        padding: 10px 20px; /* Smaller padding on mobile */
        font-size: 1em; /* Smaller font size on mobile */
        margin-bottom: 10px; /* Space between buttons on mobile */
        width: calc(100% - 40px); /* Full width minus padding */
        box-shadow: none; /* No shadow on mobile for simplicity */
        border-radius: 5px; /* Less rounded corners on mobile */
    }
}
footer {
            text-align: center;
            padding: 10px 0;
            background: #fcfdfd;
            color: rgb(20, 1, 1);
            position: visible;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Explorer</h1>
</div>

<div class="toggle-container">
    <button class="toggle-button <?php echo $view === 'top_stories' ? 'active' : ''; ?>" onclick="location.href='explore.php?view=top_stories'">Top Stories</button>
    <button class="toggle-button <?php echo $view === 'upcoming_events' ? 'active' : ''; ?>" onclick="location.href='explore.php?view=upcoming_events'">Upcoming Events</button>
</div>

<p style="text-align:center;">Help us amplify your impact!<br> Share your donation event with us, and we'll help you spread the word! 
<a class="register-link" href="register_event.html">Register your event here.</a></p>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="event-container">';
        echo '<h2>' . htmlspecialchars($row['event_name']) . '</h2>';
        echo '<div class="event-details"><strong>Description:</strong> ' . htmlspecialchars($row['event_description']) . '</div>';
        echo '<div class="event-details"><strong>Date:</strong> ' . htmlspecialchars($row['event_date']) . '</div>';
        echo '<div class="event-details"><strong>Time:</strong> ' . htmlspecialchars($row['event_time']) . '</div>';
        echo '<div class="event-details"><strong>Location:</strong> ' . htmlspecialchars($row['location']) . '</div>';
        echo '<div class="event-details"><strong>Organizer Name:</strong> ' . htmlspecialchars($row['organizer_name']) . '</div>';
        echo '<div class="event-details"><strong>Organization Name:</strong> ' . htmlspecialchars($row['organization_name']) . '</div>';
        echo '<div class="event-details"><strong>Email:</strong> ' . htmlspecialchars($row['email']) . '</div>';
        echo '<div class="event-details"><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</div>';
        echo '</div>';
    }
} else {
    if ($view === 'upcoming_events') {
        echo "<p style='text-align:center;'>There are no upcoming events.</p>";
    } else {
        echo "<p style='text-align:center;'>There are no ongoing events.</p>";
    }
}

// Close connection
$conn->close();
?>
<footer>
    <p>For event cancellations, please reach out to us via email at
         <a href="mailto:sustainfeed@gmail.com" style="color:blue;">sustainfeed@gmail.com</a>. 
         Users cannot delete events directly from the site</p>
</footer>
</body>
</html>