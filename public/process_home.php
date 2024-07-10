<?php

// start the session
session_start();

// connect to sqlite3 database
$db_path = 'barbie.db';
$db = new PDO('sqlite:' . $db_path);

// check if the database connection was successful
if (!$db) {
    die("Failed to connect to the database: " . $db->lastErrorMsg());
}

// create the info table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS info (
    id     INTEGER PRIMARY KEY AUTOINCREMENT,
    fname  TEXT    NOT NULL,
    lname  TEXT    NOT NULL,
    email  TEXT    NOT NULL,
    bdate  TEXT    NOT NULL,
    pseudo TEXT    NOT NULL UNIQUE,
    pp     BLOB    NOT NULL,
    FOREIGN KEY (email) REFERENCES users (email) 
)");

// Get form data
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$bdate = $_POST['bdate'];
$pseudo = $_POST['pseudo'];
$pp = $_FILES['pp'];

// transform the profile picture into a BLOB
$pp_blob = file_get_contents($pp['tmp_name']);

// Get the email of the user from the session
$email = $_SESSION['email'];

// check if the user already exists in the info table
$stmt = $db->prepare("SELECT * FROM info WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if the user already exists, update their profile
if (count($result) > 0) {
    $stmt = $db->prepare("UPDATE info SET fname = :fname, lname = :lname, bdate = :bdate, pseudo = :pseudo, pp = :pp WHERE email = :email");
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':bdate', $bdate);
    $stmt->bindParam(':pseudo', $pseudo);
    $stmt->bindParam(':pp', $pp_blob, PDO::PARAM_LOB);
    $stmt->execute();
} else { 
    // sanitize the email to prevent SQL injection
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // if the user does not exist, insert them
    $stmt = $db->prepare("INSERT INTO info (fname, lname, email, bdate, pseudo, pp) VALUES (:fname, :lname, :email, :bdate, :pseudo, :pp)");
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':bdate', $bdate);
    $stmt->bindParam(':pseudo', $pseudo);
    $stmt->bindParam(':pp', $pp_blob, PDO::PARAM_LOB);
    $stmt->execute();
}


// close the database connection


// redirect to the ok page
header('Location: ok.html');
exit;

?>