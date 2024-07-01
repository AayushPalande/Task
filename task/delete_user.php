<?php
require_once('db_config.php');

// Check if user ID is provided and is valid
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $delete_id = $_GET['id'];

    // Delete user from database
    $stmt_delete_user = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_delete_user->bind_param("i", $delete_id);

    if ($stmt_delete_user->execute()) {
        // Redirect to main page after deletion
        header("Location: index.php?action=delete");
        exit();
    } else {
        echo "Error deleting user: " . $stmt_delete_user->error;
    }

    $stmt_delete_user->close();
} else {
    echo "Invalid user ID.";
}

$conn->close();
?>
