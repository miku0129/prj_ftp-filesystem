<?php
include('connectftp.php');

$content_name = filter_input(INPUT_GET, 'download_content', FILTER_UNSAFE_RAW);
$download_dir = '../../downloads/';
$download_file = $download_dir . $content_name; // Specify the local path to save the downloaded file

function isFile($content)
{
    return str_contains($content, '.');
}

function rrmdir($del_content_path) // function to delete temporarily downloaded content
{
    $lists = scandir($del_content_path);

    foreach ($lists as $list) {
        $isFile = isFile($list);

        if ($isFile & $list !== '.' & $list !== '..') {
            unlink($del_content_path . "/" . $list);
        }

        if (!$isFile) {
            $nextDir = $del_content_path . "/" . $list;
            rrmdir($nextDir);
        }
    }
    rmdir($del_content_path); // delete empty directory
    return;
}

$is_file = isFile($content_name);

if ($is_file) { // if content is a file

    if (!file_exists($download_dir) && !is_dir($download_dir)) { // if download directory doesn't exist, create it
        mkdir($download_dir);
    }

    if (str_contains($content_name, '.')) { // if content is a file, create the directory structure for the file

        $dirarray = array_slice(explode('/', $content_name), 0, -1);
        $dirpath = implode('/', $dirarray);

        if (!file_exists($download_dir . $dirpath) && !is_dir($download_dir . $dirpath)) {
            mkdir($download_dir . $dirpath, 0777, true);
        }
    }

    if (ftp_get($conn_id, $download_file, $content_name, FTP_BINARY)) {

        if (file_exists($download_file)) {
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
