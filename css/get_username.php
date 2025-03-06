<?php
session_start();

// Check if the username is set in the session
if (isset($_SESSION['username'])) {
    echo json_encode(['username' => $_SESSION['username']]);
} else {
    echo json_encode(['username' => null]);
}
?>