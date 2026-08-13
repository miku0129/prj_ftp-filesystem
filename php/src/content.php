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
    <link href="./public/tailwind.css" rel="stylesheet">
    <link href="./public/scoped-bootstrap.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v7.3.1/css/all.css" rel="stylesheet">
    <script src="./public/bootstrap.bundle.min.js"></script>
</head>

<body class="h-screen">
    <div class="h-full flex flex-col xl:grid xl:grid-cols-5 gap-4 my-10 mx-10">

        <a class="text-xl text-blue-700 block mb-4" href="./index.php"><i class="fa-solid fa-house"></i> Home</a>

        <div class="col-span-4 overflow-auto py-3 mt-8">
            <?php
            // Create bread crumbs
            $path = $_GET['path'] ?? '/';
            $path_str = 'href="/content.php?path=';
            $path_array = explode('/', $path);
            $bread_curumbs = '';
            foreach ($path_array as $path_par) {
                if (!empty($path_par)) {
                    $path_str = ($path_str . $path_par . '/');
                    $icon = '<div class="text-blue-500 place-self-center"><i class="fa-solid fa-greater-than place-self-center"></i></div>';
                    $bread_curumbs = $bread_curumbs . $icon . '<a class="text-lg text-blue-700 underline block" ' . $path_str . '">' . $path_par . '</a>';
                }
            }
            echo '<div class="flex flex-row gap-4 mb-4"><div class="place-self-center"><a class="text-lg text-blue-700 underline block" href="./index.php"><i class="fa-solid fa-folder-open"></i></a></div>' . $bread_curumbs . '</div>';
            ?>
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
                    $content_table = '<div class="px-4 py-2 font-medium"><div class="flex gap-2"><div class="place-self-center"><i class="fa-solid fa-folder"></i></div><a class="text-blue-700 underline block" href="./content.php?path=' . $download_content . '">"' . $download_content . '"</a></div></div>';
                } else {

                    $content_name = ftp_get_content_name($content);
                    $is_head_dir = (substr($path, -1) === '/');
                    $download_content = $is_head_dir ? ($path . $content_name) : ($path . '/' . $content_name);
                    $content_table = '<div class="px-4 py-2 font-medium"><div class="flex gap-2"><div class="place-self-center"><i class="fa-solid fa-file"></i></div>' . $download_content . '</div></div>';
                }
                echo '<div class="flex justify-between border-b-2 border-gray-300">' .
                    $content_table .
                    '<div class="flex">
                        <div class="dropdown place-self-center">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="text-blue-700 underline px-4 py-2 block" href="./download.php?download_content=' . $download_content . '" target="__blank"><div class="place-self-end"><i class="fa-solid fa-download"></i> Download</div></a></li>
                                <li><a class="text-blue-700 underline px-4 py-2 block" href="./delete.php?delete_content=' . $download_content . '"><div class="place-self-end"><i class="fa-solid fa-trash"></i> Delete</div></a></li>
                            </ul>
                        </div>
                    </div>' .
                    '</div>';
            }
            ?>
        </div>
    </div>
</body>

</html>