<?php
include('connectftp.php');

// Get the file name from the query parameter
$remote_file = filter_input(INPUT_GET, 'delete_file', FILTER_UNSAFE_RAW);

if (ftp_delete($conn_id, $remote_file)) {
    header('Location: /');
} else {
    echo "could not delete $remote_file\n";
}
