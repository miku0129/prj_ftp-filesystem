<?php
include 'connectftp.php';
include 'lib.php';

$content_name = filter_input(INPUT_GET, 'download_content', FILTER_UNSAFE_RAW);
$download_dir = '../../downloads';

function rrmdir($del_content_path) // function to delete temporarily downloaded content
{
    $lists = scandir($del_content_path);

    foreach ($lists as $list) {
        $target_path = $del_content_path . "/" . $list;
        $isFile = is_file($target_path);

        if ($isFile & $list !== '.' & $list !== '..') {
            unlink($target_path); // delete file
        }

        if (!$isFile & $list !== '.' & $list !== '..') {
            $nextDir = $target_path;
            rrmdir($nextDir);
        }
    }
    rmdir($del_content_path); // delete empty parent directory
    return;
}

if (!is_directory($content_name)) { // if content is a file

    if (!file_exists($download_dir) && !is_dir($download_dir)) { // if download directory doesn't exist, create it
        mkdir($download_dir);
    }

    if (str_contains($content_name, '/')) { // if content is in a subdirectory, create the directory structure for the file
        $remote_path = array_slice(explode('/', $content_name), 0, -1);
        $dirpath = implode('/', $remote_path);

        if (!file_exists($download_dir . '/' . $dirpath) && !is_dir($download_dir . '/' . $dirpath)) { //create the directory structure for the file
            mkdir($download_dir . '/' . $dirpath, 0777, true);
        }
    }

    $download_fullpath = $download_dir . '/' . $content_name; // Specify the local path to save the downloaded file
    
    if (ftp_get($conn_id, $download_fullpath, $content_name, FTP_BINARY)) {

        if (file_exists($download_fullpath)) {
            set_include_path('../../downloads/');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($content_name)  . '"');
            readfile($content_name, true);
        }
        rrmdir($download_dir); // delete downloaded content
    } else {
        echo "could not download $content_name\n";
    }
}
