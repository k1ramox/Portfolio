<?php
function urlExists($url) {
    $headers = @get_headers($url);

    if ($headers) { 
        // Log the headers for debugging
        error_log(print_r($headers, true));

        $statusCode = substr($headers[0], 9, 3);

        if ($statusCode !== '404') {
            return true;
        }
        else {
            error_log("URL not found!: $url");
            return false;
        }
    }
    else {
        error_log("Failed to retrieve the headers for: $url");
    }
    return false;
}
?>