<?php

require_once 'handlers.php';

$mysql = database::connect();

$statement = $mysql->prepare('
    SELECT
      id,
      INET6_NTOA(ip) AS ip,
      datetime,
      name,
      email,
      company
    FROM
      contacts
    WHERE
      notification_sent=0
    LIMIT 3
  ');

$statement->execute();
$result = $statement->get_result();

while ($row = $result->fetch_array()) {
  $subject = 'C: '.$row['name'].', '.$row['email'];

  $row = array_map('utilities::html_entities', $row);
  $message = "New contact.\n\n";
  $message .= "Datetime: ".$row['datetime']."\n";
  $message .= "IP: ".($row['ip'] ? $row['ip'] : '-')."\n";
  $message .= "Name: ".$row['name']."\n";
  $message .= "E-mail: ".$row['email']."\n";
  $message .= "Company: ".($row['company'] ? $row['company'] : '-')."\n\n";
  $message .= "/".gethostname();

  mail::send($subject, nl2br($message), array(constants::ROOT_EMAIL));

  $statement = $mysql->prepare('
      UPDATE
        contacts
      SET
        notification_sent=1
      WHERE
        id=?
    ');

  $statement->bind_param('i', $row['id']);
  $statement->execute();
}

?>
