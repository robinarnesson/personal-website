<?php

spl_autoload_register('class_auto_loader');

function class_auto_loader($_class) {
  $file = '../source/'.$_class.'.php';

  if (file_exists($file)) {
    require_once $file;
  } else {
    throw new Exception('Class file '.$file.' not found.');
  }
}

set_exception_handler('exception_handler');

function exception_handler($_exception) {
  $e = array(
      'datetime' => date('ymd his'),
      'message'  => $_exception->getMessage(),
      'previous' => $_exception->getPrevious(),
      'code'     => $_exception->getCode(),
      'file'     => $_exception->getFile(),
      'line'     => $_exception->getLine(),
      'trace'    => $_exception->getTraceAsString());

  file_put_contents(constants::EX_LOG_PATH, json_encode($e)."\n", FILE_APPEND);

  mail::send('PHP exception @ '.gethostname(),
      nl2br(json_encode($e, JSON_PRETTY_PRINT)), array(constants::ROOT_EMAIL));

  utilities::show_frog_and_exit('Error. Sorry.');
}

?>
