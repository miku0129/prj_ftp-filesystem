<?php
$ftp_server = "ftp-server"; // FTP server address
$ftp_user_name = "user";     // FTP username
$ftp_user_pass = "123";     // FTP password

// Establishing a connection
$conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");

// Log in to the FTP server
$conn_result = @ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

if (!$conn_result) {
    echo "Couldn't connect as $ftp_user_name\n";
    exit;
}
