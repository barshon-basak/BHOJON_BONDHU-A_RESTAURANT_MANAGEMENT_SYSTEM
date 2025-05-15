<?php
function validate_image_url($url) {
    if (empty($url)) {
        return true; // Image URL is optional
    }

    // Check if the URL is valid
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    // Check if the URL ends with an image extension
    $valid_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $path_info = pathinfo(strtolower($url));
    
    if (!isset($path_info['extension']) || !in_array($path_info['extension'], $valid_extensions)) {
        return false;
    }

    return true;
}

function get_image_upload_help() {
    return '
        <div class="mt-4 p-4 bg-gray-800 rounded-md">
            
            
        </div>
    ';
}
?>
