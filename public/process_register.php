<?php

// Connect to the barbie database SQLite3 using PDO
$db_path = 'barbie.db';
$db = new PDO('sqlite:' . $db_path);

echo"Connected successfully <br>";

// Create the table users if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, email TEXT UNIQUE, password TEXT)");

// Function to validate user input
function validateInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form values
        $email = $_POST['email'];
        $password = $_POST['password'];
        $rePassword = $_POST['RePassword'];

        echo "Form submitted <br>";

        // Sanitize the input
        $email = validateInput($email);
        $password = validateInput($password);
        $rePassword = validateInput($rePassword);

        echo "Email: ". $email. "<br>";

        // Sanatize the email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format";
        }
    

         // Compare password and RePassword
        if ($password !== $rePassword) {
            return "Passwords do not match";
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        echo "Password hashed successfully<br>";

        // Insert the new user into the database
        $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();

        echo "User inserted successfully<br>";

        // Redirect to the login page after registration
        header('Location: login.html');
        exit;

        return "User registered successfully";
    }
   

    
?>