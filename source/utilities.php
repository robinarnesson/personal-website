<?php

class utilities {
  public static function get_minified_html($_html) {
    $search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');
    $replace = array('>', '<', '\\1');
    return preg_replace($search, $replace, $_html);
  }

  public static function html_entities($_value) {
    return htmlentities($_value, ENT_QUOTES, 'utf-8');
  }

  public static function is_valid_recaptcha_response($_response_code, $_recaptcha_secret,
      $_recaptcha_verify_url) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $_recaptcha_verify_url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_POST, 2);
    curl_setopt($c, CURLOPT_POSTFIELDS,
      'secret='.urlencode($_recaptcha_secret).'&'.
      'response='.urlencode($_response_code));
    $result = curl_exec($c);
    curl_close($c);

    $json = json_decode($result, true);
    if ($json == null || !isset($json['success']) || !$json['success'])
      return false;
    return true;
  }

  public static function get_random_string($_string_length = 50) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars_len = strlen($chars);
    $res = '';
    for ($i=0; $i<$_string_length; $i++)
      $res .= $chars[mt_rand(0, $chars_len - 1)];
    return $res;
  }

  public static function get_client_ip() {
    return (filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) ?
        $_SERVER['REMOTE_ADDR'] : null;
  }

  public static function show_frog_and_exit($_frog_message) {
    echo '<div style="text-align: center;">';
    echo '<pre>';
    echo " oO)-.  \n";
    echo "/__  _\ \n";
    echo "\  \(  |\n";
    echo " \__|\ {\n";
    echo " '  '--'\n\n";
    echo $_frog_message;
    echo "</pre>\n\n";
    echo "<a href='index.php'>Go to start</a>";
    echo "</div>";

    exit;
  }
}
?>
