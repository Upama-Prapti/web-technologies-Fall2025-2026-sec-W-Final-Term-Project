<?php
// Helper functions to convert PDO-style code to mysqli

function mysqli_prepare_execute($conn, $query, $params = []) {
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return ['success' => false, 'error' => $conn->error];
    }
    
    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return ['success' => true, 'stmt' => $stmt];
}

function mysqli_fetch_all_assoc($result) {
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}
?>

