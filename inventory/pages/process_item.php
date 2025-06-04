<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    $sql = "UPDATE items SET jumlah = jumlah + $quantity WHERE id = $item_id";
    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Item added successfully.'); window.location.href='../pages/petugas.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "'); window.location.href='../pages/petugas.php';</script>";
    }
}
?>
