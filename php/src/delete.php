<?php
include 'connectftp.php';
include 'lib.php';

// Get the content name from the query parameter
$content_name = filter_input(INPUT_GET, 'delete_content', FILTER_UNSAFE_RAW);

function ftp_rrmdir($ftp, $dir)
{
    $lists = ftp_rawlist($ftp, $dir); // get content list in the directory

    foreach ($lists as $list) {

        $ftp_is_directory = ftp_is_directory($list);

        if ($ftp_is_directory) {
            $content_name = ftp_get_content_name($list);
            $next_dir = $dir . $content_name . '/';
            ftp_rrmdir($ftp, $next_dir);
        }

        if (!$ftp_is_directory) {
            $content_name = ftp_get_content_name($list);
            $ftp_full_path = $dir . $content_name;
            ftp_raw($ftp, 'DELE ' . $ftp_full_path);
        }
    }
    ftp_raw($ftp, 'RMD ' . $dir);
    return true;
}

$is_directory = is_directory($content_name);

if (!$is_directory) {
    if (ftp_delete($conn_id, $content_name)) {
        header('Location: /');
    } else {
        echo "could not delete $content_name\n";
    }
}

if ($is_directory) {
    if (ftp_rrmdir($conn_id, $content_name)) {
        header('Location: /');
    } else {
        echo "could not delete $content_name\n";
    }
}
