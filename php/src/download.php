<?php
include('connectftp.php');

$remote_file = filter_input(INPUT_GET, 'download_file', FILTER_UNSAFE_RAW);
$download_dir = '../../downloads/';
$download_file = $download_dir . $remote_file; // Specify the local path to save the downloaded file

if (!file_exists($download_dir) && !is_dir($download_dir)) {
    mkdir($download_dir);
}

if (ftp_get($conn_id, $download_file, $remote_file, FTP_BINARY)) {

    if (file_exists($download_file)) {
        set_include_path('../../downloads/');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($remote_file)  . '"');
        readfile($remote_file, true);
    }
} else {
    echo "could not download $remote_file\n";
}
