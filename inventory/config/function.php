<?php
function generateUniqueID($conn, $table, $column, $prefix = 'X', $length = 3) {
    $attempt = 0;
    $maxAttempts = 100;

    do {
        if ($attempt++ >= $maxAttempts) {
            throw new Exception("Gagal menghasilkan ID unik setelah $maxAttempts percobaan.");
        }

        $randomDigits = str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        $id = $prefix . $randomDigits;

        $stmt = $conn->prepare("SELECT $column FROM $table WHERE $column = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0);

    return $id;
}
?>