<?php
if (function_exists('curl_version')) {
    echo "cURL is installed.";
    $curl_version = curl_version();
    print_r($curl_version);
} else {
    echo "cURL is not installed.";
}
?>



<?php // phpinfo(); ?>
