<?php

use DBDiff\Params\DefaultParams;
use DBDiff\Params\ParamsFactory;

require 'vendor/autoload.php';

class End2EndTest extends PHPUnit\Framework\TestCase
{
  private static string $host = "127.0.0.1";
  private static string $user = "diff";
  private static string $pass = "1234";

  public function testAll()
  {
    // db config
    $db1 = "diff1";
    $db2 = "diff2";
    $this->initDatabases($db1, $db2);
    $db1h = $this->getConnection($db1);
    $db1h->exec(file_get_contents('tests/end2end/db1-up.sql'));
    $db2h = $this->getConnection($db2);
    $db2h->exec(file_get_contents('tests/end2end/db2-up.sql'));

    $migration_actual = 'migration_actual';
    $migration_expected = 'migration_expected';

    ob_start();
    $params = $this->getDefaultParams($db1, $db2);
    $params->output = "./tests/end2end/$migration_actual";
    $params->template = "templates/simple-db-migrate.tmpl";
    ParamsFactory::injectParams($params);
    $dbDiff = new DBDiff\DBDiff;
    $dbDiff->run();
    ob_end_clean();

    $migration_actual_file = file_get_contents("./tests/end2end/$migration_actual");
    $migration_expected_file = file_get_contents("./tests/end2end/$migration_expected");
    // unlink("./tests/end2end/$migration_actual");

    // TODO: Apply the migration_actual UP to the target database and expect there to be no differences on the command-line anymore
    // TODO: Apply the migration actual DOWN to the target database and expect there to be the same expected differences again
    // TODO: Ensure the database is emptied/reset after each test

    $this->assertEquals($migration_actual_file, $migration_expected_file);
    unlink("./tests/end2end/$migration_actual");
  }

  public function testSimple()
  {
    // db config
    $db1 = "diff1";
    $db2 = "diff2";
    $this->initDatabases($db1, $db2);
    $db1h = $this->getConnection($db1);
    $db1h->exec(file_get_contents('tests/end2end/db1-simple-up.sql'));
    $db2h = $this->getConnection($db2);
    $db2h->exec(file_get_contents('tests/end2end/db2-simple-up.sql'));

    $output = './tests/end2end/out';
    ob_start();
    $params = $this->getDefaultParams($db1, $db2);
    $params->type = "schema";
    $params->include = "up";
    $params->output = $output;
    $params->template = "templates/simple-db-migrate.tmpl";
    ParamsFactory::injectParams($params);
    $dbDiff = new DBDiff\DBDiff;
    $dbDiff->run();
    ob_end_clean();

    $contents = file_get_contents($output);
    $expected = 'SQL_UP = u"""
ALTER TABLE `aa` ADD `pass` varchar(255) DEFAULT NULL;
ALTER TABLE `aa` DROP `er`;
"""
SQL_DOWN = u"""

"""
';
    $this->assertEquals($contents, $expected);
    unlink($output);
  }

  /**
   * @param  string|null  $database
   *
   * @return PDO
   */
  private function getConnection(?string $database = null): PDO
  {
    $host = self::$host;
    if ($database) {
      return new PDO("mysql:host=$host;dbname=$database;", self::$user, self::$pass);
    }

    return new PDO("mysql:host=$host", self::$user, self::$pass);
  }

  /**
   * @param  string  $db1
   * @param  string  $db2
   *
   * @return DefaultParams
   */
  private function getDefaultParams(string $db1, string $db2): DefaultParams
  {
    $params = new DefaultParams();

    $params->server1 = [
      'user' => self::$user,
      'password' => self::$pass,
      'host' => self::$host,
      'port' => 3306,
    ];
    $params->input = [
      'kind' => 'db',
      'source' => ['server' => 'server1', 'db' => $db1],
      'target' => ['server' => 'server1', 'db' => $db2],
    ];
    $params->type = "all";
    $params->include = "all";
    $params->nocomments = true;

    return $params;
  }

  /**
   * @param  string  $db1
   * @param  string  $db2
   *
   * @return void
   */
  private function initDatabases(string $db1, string $db2): void
  {
    $dbh = $this->getConnection();
    try {
      $dbh->exec("DROP DATABASE `$db1`;");
    } catch (PDOException $e) {
      // ignore
    }
    $dbh->exec("CREATE DATABASE $db1;");
    try {
      $dbh->exec("DROP DATABASE `$db2`;");
    } catch (PDOException $e) {
      // ignore
    }
    $dbh->exec("CREATE DATABASE $db2;");
  }
}

