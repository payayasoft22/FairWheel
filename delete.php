<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include your database connection
    include('db_connection.php');

    if (!empty($_POST['delete_ids'])) {
        // Get selected IDs
        $delete_ids = $_POST['delete_ids'];

        // Build placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($delete_ids), '?'));

        // SQL to delete selected records
        $sql = "DELETE FROM history WHERE id IN ($placeholders)";

        // Prepare statement
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing SQL statement: " . $conn->error);
        }

        // Bind parameters dynamically
        $types = str_repeat('i', count($delete_ids)); // 'i' for integers
        $stmt->bind_param($types, ...$delete_ids);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect with success message
            header("Location: Profile.php?message=Records deleted successfully");
            exit;
        } else {
            // Redirect with error message
            header("Location: history.php?message=Failed to delete records: " . $stmt->error);
            exit;
        }
    } else {
        // Redirect with error message if no IDs are selected
        header("Location: history.php?message=No records selected for deletion");
        exit;
    }
}
?>
