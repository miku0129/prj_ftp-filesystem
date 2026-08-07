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
    <div class="flex flex-col xl:grid xl:grid-cols-4 gap-4 my-10 mx-10">

        <div>
            <div class="flex flex-row gap-2">
                <div class="place-self-center"><i class="fa-solid fa-house"></i></div>
                <a class="text-lg text-blue-700 underline block" href="./index.php">Home</a>
            </div>
        </div>

        <div class="col-span-3 overflow-auto py-3 mt-8">
            <?php
            // Create bread crumbs
            $path = $_GET['path'] ?? '/';
            $path_str = 'href="/content.php?path=';
            $path_array = explode('/', $path);
            $bread_curumbs = '';
            foreach ($path_array as $path_par) {
                if (!empty($path_par)) {
                    $path_str = ($path_str . $path_par . '/');
                    $bread_curumbs = $bread_curumbs . '>' . '<a class="text-lg text-blue-700 underline px-3 block" ' . $path_str . '">' . $path_par . '</a>';
                }
            }
            echo '<div class="flex flex-row"><div class="place-self-center px-3"><a class="text-lg text-blue-700 underline block" href="./index.php"><i class="fa-solid fa-folder-open"></i></a></div>' . $bread_curumbs . '</div>';
            ?>
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
                        $download_content = '';
                        $content_table = '';

                        if (ftp_is_directory($content)) { // Check if the item is a directory

                            $content_name = ftp_get_content_name($content);
                            $is_head_dir = (substr($path, -1) === '/');
                            $download_content = $is_head_dir ? ($path . $content_name . '/') : ($path . '/' . $content_name . '/'); // Handle slash in the path because first directory has '/' at the end of the path
                            $content_table = '<td class="border border-blue-500 px-4 py-2 font-medium"><div class="flex gap-2"><div class="place-self-center"><i class="fa-solid fa-folder"></i></div><a class="text-blue-700 underline block" href="./content.php?path=' . $download_content . '">"' . $download_content . '"</a></div></td>';
                        } else {

                            $content_name = ftp_get_content_name($content);
                            $is_head_dir = (substr($path, -1) === '/');
                            $download_content = $is_head_dir ? ($path . $content_name) : ($path . '/' . $content_name);
                            $content_table = '<td class="border border-blue-500 px-4 py-2 font-medium"><div class="flex gap-2"><div class="place-self-center"><i class="fa-solid fa-file"></i></div>' . $download_content . '</div></td>';
                        }
                        echo '<tr>' .
                            $content_table .
                            '<td class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2" href="./download.php?download_content=' . $download_content . '" target="__blank"><div class="place-self-center"><i class="fa-solid fa-download"></i></div></a></td>
                            <td class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2 block" href="./delete.php?delete_content=' . $download_content . '"><div class="place-self-center"><i class="fa-solid fa-trash"></i></div></a></td>
                            </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>