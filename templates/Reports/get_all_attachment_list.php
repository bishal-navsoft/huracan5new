<?php 
    // Convert to modern CakePHP style
    echo json_encode([
        'total' => $count,
        'admins' => $adminArray
    ]);
?>