<?php
$file_name = urldecode($_GET['file']);  // Reverse the URL encoding
$file_path = "/var/www/html/vote-records/" . $file_name;

if(file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    ob_clean();
    flush();
    readfile($file_path);
    exit;
} else {
    // Error, file not found
    echo "File not found.";
    exit;
}
?>

