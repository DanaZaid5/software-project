<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = 'root';  // Using 'root' as the password
$database = 'glammd';
$port = 3306;  // Default MySQL port

// Try to connect to the database
$conn = mysqli_connect($host, $username, $password, $database, $port);

// Check connection
if (!$conn) {
    // Log the error
    error_log("Database connection failed: " . mysqli_connect_error());
    
    // User-friendly error message
    die("<div style='font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 50px auto; border: 1px solid #f5c6cb; border-radius: 5px; background-color: #f8d7da; color: #721c24;'>
            <h2 style='color: #721c24;'>Database Connection Error</h2>
            <p>We're having trouble connecting to the database. Please try the following:</p>
            <ol>
                <li>Make sure your MySQL server is running</li>
                <li>Verify your database credentials in db.php</li>
                <li>Check if the database 'glammd' exists</li>
            </ol>
            <p><strong>Error details:</strong> " . mysqli_connect_error() . "</p>
            <p>Current connection settings:</p>
            <ul>
                <li>Host: $host</li>
                <li>Username: $username</li>
                <li>Port: $port</li>
                <li>Database: $database</li>
            </ul>
        </div>");
}

// Set charset to ensure proper encoding
mysqli_set_charset($conn, 'utf8mb4');
?>
