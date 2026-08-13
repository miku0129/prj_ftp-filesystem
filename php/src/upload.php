<?php
include 'connectftp.php';
include 'lib.php';

if (isset($_POST['upload_submit_file'])) {
    $remote_filename = $_FILES['file']["tmp_name"]; // Get the temporary file path
    $local_filename = $_FILES['file']['name']; // Get the original file name

    if (ftp_put($conn_id, $local_filename, $remote_filename, FTP_BINARY)) {
        header('Location: /');
    } else {
        echo "Error uploading $remote_filename\n";
    }
}

if (isset($_POST['upload_submit_folder'])) {

    foreach ($_FILES['files']['error'] as $key => $error) {
        if ($error === UPLOAD_ERR_OK) {
            $remote_filename = $_FILES['files']['tmp_name'][$key];
            $local_filename = $_FILES['files']['name'][$key];
            $file_fullpath = $_FILES['files']['full_path'][$key];

            if ($local_filename === '.DS_Store') {
                continue;
            }

            $path = dirname($file_fullpath);
            $path_arr = explode('/', $path);

            for ($i = 0; $i < count($path_arr); $i++) {

                $cd_dir_result = @ftp_chdir($conn_id, $path_arr[$i]); // try to change to the directory, suppress errors with @

                if (!$cd_dir_result) { // if the directory does not exist, create it and change to that directory         
                    ftp_mkdir($conn_id, $path_arr[$i]);
                    ftp_chdir($conn_id, $path_arr[$i]);

                    if ($i === count($path_arr) - 1) { // if this is the last directory in the path, upload the file
                        ftp_put($conn_id, $local_filename, $remote_filename, FTP_BINARY);
                    }
                } else if ($i === count($path_arr) - 1) { // if the directory exists, and if this is the last directory in the path, upload the file 
                    ftp_put($conn_id, $local_filename, $remote_filename, FTP_BINARY);
                }
            }
        }
        ftp_chdir($conn_id, '/');
    }
    header('Location: /');
}
