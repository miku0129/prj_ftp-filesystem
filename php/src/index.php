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
    <link href="./public/styles.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v7.3.1/css/all.css" rel="stylesheet">
</head>

<body>
    <div class="flex flex-col xl:grid xl:grid-cols-4 gap-4 my-10 mx-10">
        <form class="mb-5" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="upload_form" method="post"
            enctype="multipart/form-data">

            <div>
                <div class="flex flex-row gap-2">
                    <div class="place-self-center"><i class="fa-solid fa-file"></i></div>
                    <p class="text-lg text-blue-500">Upload a new file</p>
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
                    <p class="text-lg text-blue-500">Upload a new folder</p>
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

        <div class="col-span-3 overflow-auto py-3 mt-8">
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th class="border border-blue-500 px-4 py-2 text-blue-500">Content</th>
                        <th class="border border-blue-500 px-4 py-2 text-blue-500">Download</th>
                        <th class="border border-blue-500 px-4 py-2 text-blue-500">Delete</th>
                    </tr>
                </thead>
                <tbody>

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
                                $content_table = '<td class="border border-blue-500 px-4 py-2 font-medium "><div class="flex gap-2"><div class="place-self-center">' . $icon . '</div><a class="text-blue-700 underline px-4 py-2 block" href="./content.php?path=' . $contents[$i] . '">"' . $contents[$i] . '"</a></div></td>';
                            } else {
                                $contents[$i] = $contentName; // It's a file, keep the name as is
                                $icon = '<i class="fa-solid fa-file"></i>';
                                $content_table = '<td class="border border-blue-500 px-4 py-2 font-medium"><div class="flex gap-2"><div class="place-self-center"><i class="fa-solid fa-file"></i></div>' . $contents[$i] . '</div></td>';
                            }
                            echo '<tr>'
                                . $content_table .
                                    '<td class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2 block" href="./download.php?download_content=' . $contents[$i] . '" target="__blank"><div class="place-self-center"><i class="fa-solid fa-download"></i></div></a></td>
                                    <td class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2 block" href="./delete.php?delete_content=' . $contents[$i] . '"><div class="place-self-center"><i class="fa-solid fa-trash"></i></div></a></td>
                                </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>