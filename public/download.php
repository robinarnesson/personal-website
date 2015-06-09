<?php

session_start();

require_once '../source/handlers.php';

// Validate request
if (empty($_GET['file']) ||
    empty($_GET['token']) ||
    empty($_SESSION['token']) ||
    empty($_SESSION['contact-id']) ||
    $_GET['token'] !== $_SESSION['token'] ||
    strpos(constants::VALID_FILES, urldecode($_GET['file'])) === false) {
  utilities::show_frog_and_exit('Nono.');
}

$file = constants::FILE_DIR.urldecode($_GET['file']);
if (!file_exists($file))
  throw new Exception('File '.$file.' not found.');

// Log download
database::save_download($_SESSION['contact-id'], basename($file));

// Output file
header('content-description: file transfer');
header('content-type: application/octet-stream');
header('content-disposition: attachment; filename='.basename($file));
header('expires: 0');
header('cache-control: must-revalidate');
header('pragma: public');
header('content-length: '.filesize($file));
readfile($file);

exit;

?>
