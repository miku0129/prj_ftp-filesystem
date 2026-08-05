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
</head>

<body>
    <div class="my-10 mx-10">
        <a class="text-blue-700 underline block" href="./index.php">Home</a>
    </div>

    <div class="flex flex-col gap-2 my-10 mx-10">
        <p class="text-lg text-blue-500">Content</p>
        <?php
        $path = $_GET['path'] ?? '/'; // Get the path from the query parameter, default to root if not set

        $contents = ftp_rawlist($conn_id, $path); // Get the list of content and directories in the specified path

        foreach ($contents as $content) {

            if (ftp_is_directory($content)) { // Check if the item is a directory

                $content_name = ftp_get_content_name($content);
                $is_head_dir = (substr($path, -1) === '/'); 
                $download_content = $is_head_dir ? ($path . $content_name . '/') : ($path . '/' . $content_name . '/'); // Handle slash in the path because first directory has '/' at the end of the path

                echo '<div class="flex gap-2">
                        <div class="px-4 py-2 font-medium">' . $content_name . '</div>
                        <button class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2" href="./download.php?download_content=' . $download_content . '" target="__blank">Download</a></button>
                        <button class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2" href="./content.php?path=' . $download_content . '">"' . "Go: " . $download_content . '"</a></button>
                    </div>';
            } else {

                $content_name = ftp_get_content_name($content);
                $is_head_dir = (substr($path, -1) === '/');
                $download_content = $is_head_dir ? ($path . $content_name) : ($path . '/' . $content_name);

                echo '<div class="flex gap-2">
                <div class="px-4 py-2 font-medium">' . $content_name . '</div>
                <button class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2" href="./download.php?download_content=' . $download_content . '" target="__blank">Download</a></button>
                </div>';
            }
        }
        ?>
    </div>
</body>

</html>