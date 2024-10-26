<?php

// Other core functions...

/**
 * Handles user login.
 * @param string $email
 * @param string $password
 * @return bool Returns true if login is successful, false otherwise.
 */
function login_user($email, $password) {
    // Retrieve user data from the database using ZuriORM
    $user = ORM::for_table('users')->where('email', $email)->find_one();

    if ($user && password_verify($password, $user->password)) {
        // Set user session or other login logic
        $_SESSION['user_id'] = $user->id;
        return true;
    } else {
        return false;
    }
}



function saveAdmin($username, $email, $password) {
    // Hash the password before saving it
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Set the role to 'admin'
    $role = 'admin';

    // Prepare the SQL query
    $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";

    try {
        // Get the database connection (assuming $pdo is the PDO instance)
        global $pdo;

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);

        // Execute the query
        $stmt->execute();

        // If successful, set a success message and redirect
        $_SESSION['message'] = "Admin account created successfully!";
        header("Location: index.php"); // Redirect to a success page
        exit();
    } catch (PDOException $e) {
        // If there's an error, set an error message and redirect
        $_SESSION['error'] = "Failed to create admin account: " . $e->getMessage();
        header("Location: register.php"); // Redirect to an error page
        exit();
    }
}
