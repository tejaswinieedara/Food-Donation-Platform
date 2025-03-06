/* we need to create 2 DataBases */
/* 1.sustainfeed */
/* 2.userAuth */



/* create sustainfeed DataBase */
/* In sustainfeed DataBase */

/* create Food Donation Tables in stainfeed database */
CREATE TABLE food_donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    additional_notes TEXT,
    donation_until DATE NOT NULL, -- This is equivalent to "expiry_date"
    location VARCHAR(255) NOT NULL,
    delivery_pickup ENUM('delivery', 'pickup') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE food_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donation_id INT NOT NULL,
    food_type VARCHAR(100) NOT NULL,
    quantity VARCHAR(50) NOT NULL, -- Allowing for various formats like "1kg", "2l", etc.
    expiry_date DATE, -- Optional expiry date
    FOREIGN KEY (donation_id) REFERENCES food_donations(id) ON DELETE CASCADE
);

/* create Book Donation Tables in stainfeed database  */
CREATE TABLE bookdonations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    `condition` VARCHAR(50) NOT NULL,
    donation_until DATE NOT NULL,
    location VARCHAR(255) NOT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE book_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donation_id INT NOT NULL,
    book_type VARCHAR(50) NOT NULL,
    subject_category VARCHAR(100),
    quantity INT NOT NULL,
    author_name VARCHAR(100),
    FOREIGN KEY (donation_id) REFERENCES bookdonations(id) ON DELETE CASCADE
);
/* create Cloth Donation Tables in stainfeed database  */

CREATE TABLE clothdonations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    `condition` VARCHAR(50) NOT NULL,
    donation_until DATE NOT NULL,
    location VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE clothing_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donation_id INT NOT NULL,
    age_group VARCHAR(50) NOT NULL,
    clothing_type VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    gender VARCHAR(10),
    FOREIGN KEY (donation_id) REFERENCES bookdonations(id) ON DELETE CASCADE
);


/* create events table in sustainfeed DataBase  */
CREATE TABLE events (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) NOT NULL,
    event_description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    organizer_name VARCHAR(255) NOT NULL,
    organization_name VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20)
);






/*create userAuth DataBase*/

/* IN userAuth DataBase */

/* create users table in userAuth DataBase  */
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    otpCode VARCHAR(6),
);






