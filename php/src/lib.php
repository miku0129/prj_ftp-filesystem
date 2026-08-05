<?php

function is_directory($path_string)
{
    return substr($path_string, -1) === '/'; // Check if the last character is a forward slash
}
