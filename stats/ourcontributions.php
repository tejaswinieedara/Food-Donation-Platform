<?php
// Database connection parameters
$servername = "localhost"; // Your server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "sustainfeed"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from each donation table
$foodDonations = $conn->query("SELECT name FROM food_donations");
$clothDonations = $conn->query("SELECT name FROM clothdonations");
$bookDonations = $conn->query("SELECT name FROM bookdonations");

// Prepare arrays to hold names
$foodNames = [];
$clothNames = [];
$bookNames = [];

// Fetch names into arrays
while ($row = $foodDonations->fetch_assoc()) {
    $foodNames[] = htmlspecialchars($row['name']);
}

while ($row = $clothDonations->fetch_assoc()) {
    $clothNames[] = htmlspecialchars($row['name']);
}

while ($row = $bookDonations->fetch_assoc()) {
    $bookNames[] = htmlspecialchars($row['name']);
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Contributions - Sustainfeed</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
    <style>
       body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f0f4c3, #c8e6c9);
        }

        header {
            background: linear-gradient(135deg, #66b669, #b1b67c);
            color: rgb(4, 20, 9);
            padding: 20px 0;
            text-align: center;
        }

        main {
            padding: 20px;
            max-width: 800px; /* Centering the content */
            margin: auto; /* Centering */
        }

        .accordion {
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .accordion-item {
            border-bottom: 1px solid #ddd;
        }

        .accordion-header {
            padding: 15px;
            cursor: pointer;
            font-weight: bold;
            background-color: #66b669;
            color: white;
            transition: background-color 0.3s ease;
            display: flex; /* Flexbox for alignment */
            justify-content: space-between; /* Space between title and icon */
            align-items: center; /* Center vertically */
        }

        .accordion-header:hover {
            background-color: #5a9e5a;
        }

        .accordion-content {
            display: none; /* Hidden by default */
            padding: 15px;
            background-color: #fff;
        }

        .button {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 20px;
            background-color:darkgreen;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease; /* Smooth transition */
        }

        .button:hover {
            background-color:green /* Darker blue on hover */
        }

        /* Accordion icon styles */
        .accordion-icon {
            width: 20px; 
            height: 20px; 
            background-color: white; 
            border-radius: 50%; 
            display:flex; 
            justify-content:center; 
            align-items:center; 
        }
        
        .accordion-icon::after {
          content:"+"; 
          font-size:.8em; 
          color:#66b669; 
          font-weight:bold; 
          transition:.3s ease all; 
      }
      
      .accordion-header.active .accordion-icon::after {
          content:"-"; 
      }
    </style>
</head>
<body>
    <header>
        <h1>Our Contributions to Society</h1>
    </header>

    <main>
        <section class="intro">
            <h2>Making a Difference</h2>
            <p>Here are the contributions made by our supporters:</p>
        </section>

        <section class="accordion">
            <!-- Accordion Item for Food Donations -->
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion('food')">
                    Food Donations Till Date By
                </div>
                <div class="accordion-content" id="food">
                    <ul>
                        <?php foreach ($foodNames as $name): ?>
                            <li><?php echo $name; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Accordion Item for Cloth Donations -->
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion('clothes')">
                    Cloth Donations Till Date By
                </div>
                <div class="accordion-content" id="clothes">
                    <ul>
                        <?php foreach ($clothNames as $name): ?>
                            <li><?php echo $name; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Accordion Item for Book Donations -->
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion('books')">
                    Book Donations Till Date By
                </div>
                <div class="accordion-content" id="books">
                    <ul>
                        <?php foreach ($bookNames as $name): ?>
                            <li><?php echo $name; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        </section>
        <p>we are also promoting events on various donations!! 
        <a href='../exploremodule/explore.php'>Click here to view</a></p>
        <!-- Button to return or navigate -->
        <a href="statstics.html" class="button">Back to Statistics</a>

    </main>

    <!-- JavaScript for Accordion Functionality -->
    <script>
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            
            // Toggle display of the accordion content
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
                // Hide other contents if needed
                const allContents = document.querySelectorAll('.accordion-content');
                allContents.forEach((item) => {
                    if (item.id !== id) {
                        item.style.display = 'none';
                    }
                });
            }
        }
    </script>

</body>
</html>