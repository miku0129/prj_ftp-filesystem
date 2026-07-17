<?php
include('connectftp.php');

$content_name = filter_input(INPUT_GET, 'download_content', FILTER_UNSAFE_RAW);
$download_dir = '../../downloads/';
$download_file = $download_dir . $content_name; // Specify the local path to save the downloaded file

function isFile($content)
{
    return str_contains($content, '.');
}

function getzip($ftp, $content, $dldir)
{

    function ftp_getzip($ftp, $content, $dldir) 
    {

        $lists = ftp_nlist($ftp, $content);
        $dir_name = $dldir;

        foreach ($lists as $list) {

            $content_dir = explode('/', $list)[0];
            $dir_name = (basename($dir_name)) !==  $content_dir ? $dir_name . $content_dir : $dir_name;

            if (!file_exists($dir_name) && !is_dir($dir_name)) {
                mkdir($dir_name);
            }

            $is_file = isFile($list);

            if ($is_file) {
                $file_name = explode('/', $list)[1];

                $local_file_path = $dir_name . "/" . $file_name;

                $remote_file_path = ftp_pwd($ftp) . "/" . $list;

                ftp_get($ftp, $local_file_path, $remote_file_path, FTP_BINARY);
            }

            if (!$is_file) {
                $next_dir = explode('/', $list)[1];
                $dir_name = $dir_name . "/" . $next_dir;

                if (!file_exists($dir_name) && !is_dir($dir_name)) {

                    mkdir($dir_name);
                }
                ftp_chdir($ftp, explode('/', $list)[0]);
                ftp_getzip($ftp, $next_dir, $dir_name);
            }
        }
        return true;
    }
    ftp_getzip($ftp, $content, $dldir);

    $zip_filepath = "../../downloads/" . $content;
    $path = explode("/", $zip_filepath);
    $zip_filename = end($path) . ".zip";

    $command = 'cd ' . "../../downloads" . ";" . "zip -r " . $zip_filename . " " . end($path);
    exec($command);


    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    header("Content-Type: application/zip");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Disposition: attachment; filename=\"" . $zip_filename . "\"");
    header('Content-Length: ' . filesize('../../downloads/' . $zip_filename));

    readfile('../../downloads/' . $zip_filename);

    // unlink($zip_filepath . ".zip");
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
    } else {
        echo "could not download $content_name\n";
    }
}

if (!$is_file) {
    getzip($conn_id, $content_name, $download_dir);
}
