<?php 
try {
    // connect to sqlite3 database
    $db_path = 'barbie.db';
    $db = new PDO('sqlite:' . $db_path);

    // Set error mode to exceptions
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sanitize and validate the email address
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
        exit();
    }

    if (strlen($email) > 255) {
        echo "Email address is too long.";
        exit();
    }

    // Debugging code
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br>";

    // Prepare and execute the SQL query
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Fetch the user from the database
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // show the user data if they exist
    if ($row) {
        echo "User found: ". $row['email']. "<br>";
    } else {
        echo "User not found.";
    }

    // Check if the user exists in the database
    if ($row) {
        // User exists, check the password
        if ($row['password'] !== null && password_verify($password, $row['password'])) {
            // Password is correct, log in the user
            session_start();
            $_SESSION['loggedIn'] = true;
            $_SESSION['email'] = $email;
            header("Location: home.html");
            exit();
        } else {
            // Password is incorrect, display an error message
            echo "Invalid password.";
        }
    } else {
        // User does not exist, display an error message
        echo "User not found.";
    }

} catch (Exception $e) {
    echo "Error connecting to the database: " . $e->getMessage();
    exit();
}
?>