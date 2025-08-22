<?php
include "db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $provider_id       = $_SESSION['user_id'] ?? 0;
    $food_title        = $_POST['food_title'];
    $food_description  = $_POST['food_description'];
    $food_type         = $_POST['food_type']; // 'Veg' or 'Non-Veg'
    $quantity          = $_POST['quantity'];
    $freshness_status  = $_POST['freshness_status'];
    $pickup_location   = $_POST['pickup_location'];
    $available_until   = $_POST['available_until']; // should be in 'YYYY-MM-DD HH:MM:SS' format

    $sql = "INSERT INTO food_listings 
            (provider_id, food_title, food_description, food_type, quantity, freshness_status, pickup_location, available_until) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    // provider_id = int, food_title = string, food_description = string, food_type = string,
    // quantity = int, freshness_status = string, pickup_location = string, available_until = string(datetime)
    $stmt->bind_param(
        "isssisss",
        $provider_id,
        $food_title,
        $food_description,
        $food_type,
        $quantity,
        $freshness_status,
        $pickup_location,
        $available_until
    );

  if ($stmt->execute()) {
        echo "<script>
            alert('Listing added successfully!');
            window.location.href = 'provider_dashboard.php#browseSection';
        </script>";

    } else {
        echo "<script>
                alert('Error: " . addslashes($stmt->error) . "');
                window.location.href = 'provider_dashboard.php#addFormSection';
              </script>";
    }
    $listing_id = $conn->insert_id; // new listing id

    $notifSql = "
    INSERT INTO notifications (recipient_id, listing_id, message)
    SELECT id AS recipient_id, ? AS listing_id, CONCAT('New listing added: ', ?) AS message
    FROM users
    WHERE role = 'recipient'
    ";
    $notifStmt = $conn->prepare($notifSql);
    $notifStmt->bind_param('is', $listing_id, $food_title);
    $notifStmt->execute();
    $notifStmt->close();


    $stmt->close();
    $conn->close();
}
?>
