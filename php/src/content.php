<?php
include 'connectftp.php'; // Include the configuration file for FTP connection

function isFile($content)
{
    return str_contains($content, '.');
}
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

        $list = ftp_nlist($conn_id, $path); // Get the list of files and directories in the specified path

        if ($list) {

            for ($i = 0; $i < count($list); $i++) {
                if (str_contains($list[$i], '.')) {
                    echo '<div class="flex flex-row gap-2"><div class="px-4 py-2 font-medium">' . $list[$i] . '</div><button class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2" href="./download_content.php?download_content=' . $list[$i] . '" target="__blank">Download</a></button></div>';
                } else {
                    echo '<div class="flex gap-2">
                            <div class="px-4 py-2 font-medium">' . $list[$i] . '</div>
                                <button class="border border-blue-500 px-4 py-2 font-medium"><a class="text-blue-700 underline px-4 py-2" href="./content.php?path=' . $list[$i] . '">"' . "Go: " . $list[$i] . '"</a></button>
                            </div>';
                }
            }
        }
        ?>
    </div>
</body>

</html>