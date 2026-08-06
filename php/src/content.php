<?php
include 'connectftp.php'; // Include the configuration file for FTP connection
include 'lib.php'; // Include the library file for FTP functions
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content</title>
    <link href="./public/styles.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v7.3.1/css/all.css" rel="stylesheet">
</head>

<body>
    <div class="grid grid-cols-4 gap-4 my-10 mx-10">

        <div>
            <div class="flex flex-row gap-2">
                <div class="place-self-center"><i class="fa-solid fa-house"></i></div>
                <a class="text-lg text-blue-700 underline block" href="./index.php">Home</a>
            </div>
        </div>

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
                    $path = $_GET['path'] ?? '/'; // Get the path from the query parameter, default to root if not set

                    $contents = ftp_rawlist($conn_id, $path); // Get the list of content and directories in the specified path

                    foreach ($contents as $content) {

                        if (ftp_is_directory($content)) { // Check if the item is a directory

                            $content_name = ftp_get_content_name($content);
                            $is_head_dir = (substr($path, -1) === '/');
                            $download_content = $is_head_dir ? ($path . $content_name . '/') : ($path . '/' . $content_name . '/'); // Handle slash in the path because first directory has '/' at the end of the path

                            echo '<tr>
                                    <td class="border border-blue-500 px-4 py-2 font-medium"><div class="flex gap-2"><div class="place-self-center"><i class="fa-solid fa-folder"></i></div><a class="text-blue-700 underline block" href="./content.php?path=' . $download_content . '">"' . $download_content . '"</a></div></td>
                                    <td class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2" href="./download.php?download_content=' . $download_content . '" target="__blank"><div class="place-self-center"><i class="fa-solid fa-download"></i></div></a></td>
                                    <td class="border border-blue-500 px-4 py-2 font-medium"></td>
                                </tr>';
                        } else {

                            $content_name = ftp_get_content_name($content);
                            $is_head_dir = (substr($path, -1) === '/');
                            $download_content = $is_head_dir ? ($path . $content_name) : ($path . '/' . $content_name);

                            echo '<tr">
                                    <td class="border border-blue-500 px-4 py-2 font-medium"><div class="flex gap-2"><div class="place-self-center"><i class="fa-solid fa-file"></i></div>' . $download_content . '</div></td>
                                    <td class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2" href="./download.php?download_content=' . $download_content . '" target="__blank"><div class="place-self-center"><i class="fa-solid fa-download"></i></div></a></td>
                                    <td class="border border-blue-500 px-4 py-2 font-medium"></td>
                                </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- </div> -->
    </div>
</body>

</html>