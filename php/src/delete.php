<?php
include('connectftp.php');

// Get the content name from the query parameter
$content_name = filter_input(INPUT_GET, 'delete_content', FILTER_UNSAFE_RAW);


function isFile($content)
{
    return str_contains($content, '.');
}

function ftp_rrmdir($ftp, $dir)
{
    $lists = ftp_nlist($ftp, $dir); // get content list in the directory

    foreach ($lists as $list) {

        $is_file = isFile($list);

        // '.' exists -> it's a file so delete it
        if ($is_file) {
            ftp_raw($ftp, 'DELE ' . $list);
        }

        // '.' isn't exists -> it's a directory so step down one below then call ftp_rrmdir recursively
        if (!$is_file) {
            $next_dir = explode('/', $list)[1];
            ftp_chdir($ftp, $dir);
            ftp_rrmdir($ftp, $next_dir);
            ftp_chdir($ftp, '..'); // move back to the parent directory
            if (ftp_nlist($ftp, $dir) === false) { // if the directory is empty, delete it
                ftp_rmdir($ftp, $dir);
            }
        }
    }
    ftp_raw($ftp, 'RMD ' . $dir);
    return true;
}

$is_file = isFile($content_name);

if ($is_file) {
    if (ftp_delete($conn_id, $content_name)) {
        header('Location: /');
    } else {
        echo "could not delete $content_name\n";
    }
}

if (!$is_file) {
    if (ftp_rrmdir($conn_id, $content_name)) {
        header('Location: /');
    } else {
        echo "could not delete $content_name\n";
    }
}
