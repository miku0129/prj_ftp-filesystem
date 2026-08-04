<?php
include('connectftp.php');

$content_name = filter_input(INPUT_GET, 'download_content', FILTER_UNSAFE_RAW);
$download_dir = '../../downloads/';
$download_file = $download_dir . $content_name; // Specify the local path to save the downloaded file

function isFile($content)
{
    return str_contains($content, '.');
}

function ftp_getzip($ftp, $content, $dldir)
{
    $lists = ftp_nlist($ftp, $content); // get a list of files/directories under given directory in ftp server
    $dir_name = $dldir; // temporarily directory name for downloading

    if (!file_exists($dldir) && !is_dir($dldir)) { // if download directory doesn't exist, create it
        mkdir($dldir);
    }

    foreach ($lists as $list) {

        $list_first_half = explode('/', $list)[0]; // $list is formatted like, 'hoge/sample.pdf' or 'hoge/huga/'
        $list_second_half = explode('/', $list)[1];

        // check current $dir_name, then update with directory of content
        $dir_name = (basename($dir_name)) !==  $list_first_half ? $dir_name . $list_first_half : $dir_name;

        if (!file_exists($dir_name) && !is_dir($dir_name)) {
            mkdir($dir_name);
        }

        $is_file = isFile($list);

        if ($is_file) {

            $local_file_path = $dir_name . '/' . $list_second_half;

            $file_path_if_pwd_root = ftp_pwd($ftp) . $list;
            $file_path_if_pwd_isnt_root = ftp_pwd($ftp) . '/' . $list;
            $remote_file_path = ftp_pwd($ftp) !== '/' ? $file_path_if_pwd_isnt_root : $file_path_if_pwd_root;

            ftp_get($ftp, $local_file_path, $remote_file_path, FTP_BINARY);
        }

        if (!$is_file) {

            $copy_dir_name = $dir_name;
            $dir_name = $dir_name . "/" . $list_second_half; // update dir_name

            if (!file_exists($dir_name) && !is_dir($dir_name)) {

                mkdir($dir_name);
            }
            ftp_chdir($ftp, $list_first_half); // go into one directory down in FTP server
            ftp_getzip($ftp, $list_second_half, $dir_name);

            ftp_chdir($ftp, '../'); // reset position in ftp server
            $dir_name = $copy_dir_name; //reset dir_name
        }
    }
    return true;
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

function getzip($ftp, $content, $dldir)
{
    ftp_getzip($ftp, $content, $dldir);

    $zip_filename = $content . ".zip";
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

$is_file = isFile($content_name);

if ($is_file) { // if content is a file

    if (!file_exists($download_dir) && !is_dir($download_dir)) {
        mkdir($download_dir);
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

if (!$is_file) {
    getzip($conn_id, $content_name, $download_dir);
}
