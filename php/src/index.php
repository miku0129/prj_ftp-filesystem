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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="./public/tailwind.css" rel="stylesheet">
    <link href="./public/scoped-bootstrap.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v7.3.1/css/all.css" rel="stylesheet">
    <script src="./public/bootstrap.bundle.min.js"></script>
</head>

<body class="h-screen">
    <div class="h-full flex flex-col xl:grid xl:grid-cols-5 gap-4 my-10 mx-10">
        <div>
            <button class="btn btn-primary btn-primary btn-lg" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
                + new
            </button>
        </div>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
            <div class="offcanvas-header">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div>
                    <form class="mb-5" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="upload_form" method="post"
                        enctype="multipart/form-data">

                        <div>
                            <div class="flex flex-row gap-2">
                                <div class="place-self-center"><i class="fa-solid fa-file"></i></div>
                                <span class="text-lg text-blue-500 inline-block align-middle">Upload a new file</span>
                            </div>

                            <input class="text-sm text-stone-500 mt-3 file:mr-3 file:py-1 file:px-3 file:border-[1px]
                    file:bg-stone-50 file:text-stone-700
                    hover:file:cursor-pointer hover:file:bg-blue-50
                    hover:file:text-blue-700 block" type="file" id="file" name="file" />

                            <input class="text-sm text-stone-500 mt-3 py-1 px-3 border-[1px]
                    hover:cursor-pointer hover:bg-blue-50
                    hover:text-blue-700 block" type="submit" name="upload_submit_file" value="Upload" />
                        </div>
                        <div class="mt-10">
                            <div class="flex flex-row gap-2">
                                <div class="place-self-center"><i class="fa-solid fa-folder"></i></div>
                                <span class="text-lg text-blue-500 inline-block align-middle">Upload a new folder</span>
                            </div>

                            <input class="text-sm text-stone-500 mt-3 file:mr-3 file:py-1 file:px-3 file:border-[1px]
                    file:bg-stone-50 file:text-stone-700
                    hover:file:cursor-pointer hover:file:bg-blue-50
                    hover:file:text-blue-700 block" type="file" id="file" name="files[]" webkitdirectory multiple />

                            <input class="text-sm text-stone-500 mt-3 py-1 px-3 border-[1px]
                    hover:cursor-pointer hover:bg-blue-50
                    hover:text-blue-700 block" type="submit" name="upload_submit_folder" value="Upload" />
                        </div>

                    </form>

                </div>
            </div>
        </div>
        <div class="col-span-4 overflow-auto py-3 mt-8">
            <?php
            $contents = ftp_rawlist($conn_id, ".");

            if ($contents) {
                for ($i = 0; $i < count($contents); $i++) {
                    $isDirectory = ftp_is_directory($contents[$i]);
                    $contentName = ftp_get_content_name($contents[$i]);
                    $icon = '';

                    if ($isDirectory) {
                        $contents[$i] = $contentName . '/'; // Append a forward slash to indicate it's a directory
                        $icon = '<i class="fa-solid fa-folder"></i>';
                        $content_table = '<div class="px-4 py-2 font-medium "><div class="flex gap-4"><div class="place-self-center">' . $icon . '</div><a class="text-blue-700 underline block" href="./content.php?path=' . $contents[$i] . '">"' . $contents[$i] . '"</a></div></div>';
                    } else {
                        $contents[$i] = $contentName; // It's a file, keep the name as is
                        $icon = '<i class="fa-solid fa-file"></i>';
                        $content_table = '<div class="px-4 py-2 font-medium"><div class="flex gap-4"><div class="place-self-center">'. $icon . '</i></div>' . $contents[$i] . '</div></div>';
                    }
                    echo '<div class="flex justify-between border-b-2 border-gray-300">'
                        . $content_table .
                        '<div class="flex">
                                    <div class="dropdown place-self-center">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="text-blue-700 underline px-4 py-2 block" href="./download.php?download_content=' . $contents[$i] . '" target="__blank"><div class="place-self-end"><i class="fa-solid fa-download"></i> Download</div></a></li>
                                            <li><a class="text-blue-700 underline px-4 py-2 block" href="./delete.php?delete_content=' . $contents[$i] . '"><div class="place-self-end"><i class="fa-solid fa-trash"></i> Delete</div></a></li>
                                        </ul>
                                    </div>
                                </div>' .
                        '</div>';
                }
            }
            ?>
        </div>
    </div>

</body>

</html>