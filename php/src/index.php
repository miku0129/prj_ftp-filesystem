<?php
$ftp_server = "ftp-server"; // FTP server address
$ftp_user_name = "user";     // FTP username
$ftp_user_pass = "123";     // FTP password

// Establishing a connection
$conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");

// Log in to the FTP server
if (@ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
    echo "Connected as $ftp_user_name@$ftp_server\n";

    $local_file = "../../helloworld.txt";
    $remote_file = "/helloworld.txt";
    $local_downloaded_file = "../../helloworld_downloaded_file.txt";
    $remote_dir = 'www';

    // Upload file
    // if (ftp_put($conn_id, $remote_file, $local_file, FTP_ASCII)) {
    //     echo "Successfully uploaded $local_file to $remote_file\n";
    // } else {
    //     echo "Error uploading $local_file\n";
    // }

    // Download a file
    // ftp_get($conn_id, $local_downloaded_file, $remote_file, FTP_BINARY);

    // Delete file
    // if (ftp_delete($conn_id, $remote_file)) {
    //     echo "$remote_file deleted successful\n";
    // } else {
    //     echo "could not delete $remote_file\n";
    // }

    // Create directory
    // if (ftp_mkdir($conn_id, $remote_dir)) {
    //     echo "successfully created $remote_dir\n";
    // } else {
    //     echo "There was a problem while creating $remote_dir\n";
    // }

    // Delete directory
    if (ftp_rmdir($conn_id, $remote_dir)) {
        echo "Successfully deleted $remote_dir\n";
    } else {
        echo "There was a problem while deleting $remote_dir\n";
    }

    // List files
    $files = ftp_nlist($conn_id, ".");
    echo "Files in the current directory:\n";
    print_r($files);
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