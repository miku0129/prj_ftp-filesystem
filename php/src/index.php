<?php
$ftp_server = "ftp-server"; // FTP server address
$ftp_user_name = "user";     // FTP username
$ftp_user_pass = "123";     // FTP password

// Establishing a connection
$conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");

// Log in to the FTP server
if (@ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
    echo "Connected as $ftp_user_name@$ftp_server\n";
} else {
    echo "Couldn't connect as $ftp_user_name\n";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="./public/styles.css" rel="stylesheet">
</head>

<body>
    <p>hello ftp</p>
</body>

</html>