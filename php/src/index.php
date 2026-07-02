<?php
include('connectftp.php');

if (isset($_POST['upload_submit'])) {
    $local_file = $_FILES['file']["tmp_name"]; // Get the temporary file path
    $remote_file = $_FILES['file']['name']; // Get the original file name

    // Upload file
    if (ftp_put($conn_id, $remote_file, $local_file, FTP_ASCII)) {
        header('Location: /');
    } else {
        echo "Error uploading $local_file\n";
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
</head>

<body>
    <div class="my-10 mx-10">
        <form class="mb-5" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="upload_form" method="post"
            enctype="multipart/form-data">

            <div>
                <p class="text-lg text-blue-500">Upload a new file</p>

                <input class="text-sm text-stone-500 mt-3 file:mr-3 file:py-1 file:px-3 file:border-[1px]
                    file:bg-stone-50 file:text-stone-700
                    hover:file:cursor-pointer hover:file:bg-blue-50
                    hover:file:text-blue-700 block" type="file" id="file" name="file" />

                <input class="text-sm text-stone-500 mt-3 py-1 px-3 border-[1px]
                    hover:cursor-pointer hover:bg-blue-50
                    hover:text-blue-700 block" type="submit" name="upload_submit" value="Upload" />
            </div>
        </form>

        <div class="overflow-auto py-3 mt-8">
            <p class="text-lg text-blue-500">My files</p>
            <table class="table-auto">
                <thead>
                    <tr>
                        <th class="border border-blue-500 px-4 py-2 text-blue-500">File Name</th>
                        <th class="border border-blue-500 px-4 py-2 text-blue-500">Download</th>
                        <th class="border border-blue-500 px-4 py-2 text-blue-500">Delete</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $files = ftp_nlist($conn_id, ".");

                    if ($files) {


                        for ($i = 0; $i < count($files); $i++) {
                            echo '<tr>
                                    <td class="border border-blue-500 px-4 py-2 font-medium">' . $files[$i] . '</td>
                                    <td class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2 block" href="./download.php?download_file=' . $files[$i] . '" target="__blank">Download</a></td>
                                    <td class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2 block" href="./delete.php?delete_file=' . $files[$i] . '">Delete</a></td>
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