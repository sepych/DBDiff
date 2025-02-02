<?php

namespace DBDiff\DB;

use DBDiff\Params\DefaultParams;
use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use DBDiff\Exceptions\DBException;
use Illuminate\Database\Connection;


class DBManager
{

  private Capsule $capsule;

  function __construct()
  {
    $this->capsule = new Capsule;
  }

  public function connect(DefaultParams $params): void
  {
    foreach ($params->input as $key => $input) {
      if ($key === 'kind') {
        continue;
      }
      $server = $params->{$input['server']};
      $db = $input['db'];
      $this->capsule->addConnection([
        'driver' => 'mysql',
        'host' => $server['host'],
        'port' => $server['port'],
        'database' => $db,
        'username' => $server['user'],
        'password' => $server['password'],
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
      ], $key);
    }
  }

  /**
   * @throws DBException
   */
  public function testResources(DefaultParams $params): void
  {
    $this->testResource($params->input['source'], 'source');
    $this->testResource($params->input['target'], 'target');
  }

  /**
   * @throws DBException
   */
  public function testResource(array $input, string $res): void
  {
    try {
      $this->capsule->getConnection($res);
    } catch (Exception) {
      throw new DBException("Can't connect to target database");
    }
    if ( ! empty($input['table'])) {
      try {
        $this->capsule->getConnection($res)->table($input['table'])->first();
      } catch (Exception) {
        throw new DBException("Can't access target table");
      }
    }
  }

  public function getDB(string $res): Connection
  {
    return $this->capsule->getConnection($res);
  }

  public function getTables(string $connection): array
  {
    $result = $this->getDB($connection)->select("show tables");
    $arr = [];
    foreach ($result as $value) {
      $arr[] = array_values((array)$value)[0];
    }

    return $arr;
//        return array_flatten($result);
  }

  public function getColumns(string $connection, string $table): array
  {
    $result = $this->getDB($connection)->select("show columns from `$table`");

    return array_pluck($result, 'Field');
  }

  public function getKey(string $connection, string $table): array
  {
    $keys = $this->getDB($connection)->select("show indexes from `$table`");
    $ukey = [];
    foreach ($keys as $key) {
      if ($key->Key_name === 'PRIMARY') {
        $ukey[] = $key->Column_name;
      }
    }

    return $ukey;
  }

}
