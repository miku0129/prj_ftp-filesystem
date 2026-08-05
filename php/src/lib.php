<?php

function ftp_is_directory($item)
{
    $item_info = explode(' ', $item); // Extract the file name from the FTP raw list
    return str_contains($item_info[0], 'd'); // Check if the first character indicates a directory ('d' for directory, '-' for file)
}

function ftp_get_content_name($item)
{
    $item_info = explode(' ', $item); // Extract the file name from the FTP raw list
    return end($item_info); // Get the last element, which is the file name
}

function is_directory($path_string)
{
    return substr($path_string, -1) === '/'; // Check if the last character is a forward slash
}
