<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class database {
  public static function save_contact($_name, $_email, $_company) {
    $mysql = self::connect();

    $statement = $mysql->prepare('
        INSERT INTO
          contacts
        SET
          ip=INET6_ATON(?),
          datetime=NOW(),
          notification_sent=0,
          name=?,
          email=?,
          company=?
        ');

    $statement->bind_param('ssss', utilities::get_client_ip(), $_name, $_email, $_company);
    $statement->execute();

    return $mysql->insert_id;
  }

  public static function save_download($_contact_id, $_filename) {
    $mysql = self::connect();

    $statement = $mysql->prepare('
        INSERT INTO
          downloads
        SET
          contact_id=?,
          ip=INET6_ATON(?),
          datetime=NOW(),
          filename=?
        ');

    $statement->bind_param('iss', $_contact_id, utilities::get_client_ip(), $_filename);
    $statement->execute();
  }

  public static function connect( ) {
    return new mysqli(
        constants::MYSQL_HOST,
        constants::MYSQL_USER,
        constants::MYSQL_PASS,
        constants::MYSQL_DB);
  }
}

/*
  CREATE TABLE `contacts` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `ip` varbinary(16) DEFAULT NULL,
    `datetime` datetime NOT NULL,
    `notification_sent` bit(1) NOT NULL DEFAULT b'0',
    `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
    `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `company` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

  CREATE TABLE `downloads` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `contact_id` int(10) unsigned NOT NULL,
    `ip` varbinary(16) DEFAULT NULL,
    `datetime` datetime NOT NULL,
    `filename` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
*/

?>
