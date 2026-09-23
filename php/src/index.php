<?php
include 'connectftp.php';
include 'lib.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/public/favicon.ico" />
    <title>TAIGAS | Home</title>
    <link href="./public/tailwind.css" rel="stylesheet">
    <link href="./public/scoped-bootstrap.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v7.3.1/css/all.css" rel="stylesheet">
    <script src="./public/bootstrap.bundle.min.js"></script>
</head>

<body class="h-screen">
    <div class="h-full flex flex-col xl:grid xl:grid-cols-5 gap-4 my-10 mx-10">

        <div>
            <div class="my-4">
                <a class="flex gap-3 no-underline" href="./index.php">
                    <img alt="logo of the association" src='./public/taigas.webp' width='50px'>
                    <div class="place-self-center"><span class="text-3xl inline-block align-middle"> TAIGAS</span></div>
                </a>
            </div>

            <button class="btn btn-light btn-primary btn-lg shadow-sm" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
                + new
            </button>
        </div>

        <?php require_once __DIR__ . '/components/offcanvas-upload-form.php' ?>

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
                        $content_table = '<div class="px-4 py-2 font-medium"><div class="flex gap-4"><div class="place-self-center">' . $icon . '</i></div>' . $contents[$i] . '</div></div>';
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