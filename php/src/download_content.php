<?php
include 'connectftp.php';
include 'lib.php';

$content_name = filter_input(INPUT_GET, 'download_content', FILTER_UNSAFE_RAW);
$download_dir = '../../downloads';

function ftp_getzip($ftp, $content, $dldir)
{
    $items = ftp_rawlist($ftp, $content); // get a list of files/directories under given directory in ftp server
    
    $dir_name = $dldir; // temporarily directory name for downloading
    if (!file_exists($dir_name) && !is_dir($dir_name)) { // if download directory doesn't exist, create it
        mkdir($dir_name);
    }

    foreach ($items as $item) {
        $isDirectory = ftp_is_directory($item);

        if (!$isDirectory) { // if content is a file, download it

            $content_name = ftp_get_content_name($item);

            $local_file_dir = $dldir . '/' . $content;
            $local_file_path = $local_file_dir . $content_name;

            $remote_file_path = ftp_pwd($ftp) . $content . $content_name;

            if (!file_exists($local_file_dir) && !is_dir($local_file_dir)) {
                mkdir($local_file_dir, 0777, true);
            }
            
            ftp_get($ftp, $local_file_path, $remote_file_path, FTP_BINARY);
        }

        if ($isDirectory) { // if content is a directory, recursively call ftp_getzip to download its contents

            $dir_name = ftp_get_content_name($item);

            ftp_getzip($ftp, $content . $dir_name . '/', $dldir);
        }
    }
    return true;
}

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

function getzip($ftp, $content, $dldir)
{
    ftp_getzip($ftp, $content, $dldir);

    $content_name = substr(str_replace('/', '_', $content), 0, -1); // replace forward slashes with underscores for zip file name
    $zip_filename = $content_name . ".zip";
    $zip_filepath = "../../downloads" . "/" . $zip_filename;

    $command = 'cd ' . "../../downloads" . ";" . "zip -r " . $zip_filename . " " . $content;
    exec($command);

    rrmdir($dldir); // delete downloaded content

    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    header("Content-Type: application/zip");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Disposition: attachment; filename=\"" . $zip_filename . "\"");
    header('Content-Length: ' . filesize($zip_filepath));

    readfile($zip_filepath);
    unlink($zip_filepath); // delete zip file
    exit();
}

if (is_directory($content_name)) {
    getzip($conn_id, $content_name, $download_dir);
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
