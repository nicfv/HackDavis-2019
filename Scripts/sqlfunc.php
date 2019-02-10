<?php

$servername = 'LOCALHOST'; // sql141.main-hosting.eu
$username = 'u341003167_suser';
$password = 'mTen8VdtpkTvknSaxP';
$dbname = 'u341003167_seize';

// Sends a database query to a specific table and returns the result. Returns NULL if there was an error.
function query($sql) {
    global $servername, $username, $password, $dbname;
    // Create connection.
    $conn = mysqli_connect($servername, $username, $password);
    // Check connection.
    if (!$conn) {
        die();
        scripterr(mysqli_connect_error());
        return NULL;
    }
    // Return the result.
    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);
    return $result;
}

// Retrieves data from column col of row id in table table.
function getcol($table, $id, $col) {
    global $dbname;
    $result = query('SELECT '.safestring($col).' FROM `'.$dbname.'`.`'.safestring($table).'` WHERE id='.safestring((int)$id));
    if($result) {
        $row = mysqli_fetch_assoc($result);
        return $row[$col];
    }
    scripterr('There was an error fetching data.');
    return NULL;
}

// Returns a safe string to upload to SQL databases and display on HTML webpages.
function safestring($str) {
    return addslashes(htmlentities(substr(trim((string)$str), 0, 3000)));
}

// Puts an error message in the browser's console.
function scripterr($msg) {
    echo '<script type="application/javascript">console.error("' . $msg . '");</script>';
}

?>