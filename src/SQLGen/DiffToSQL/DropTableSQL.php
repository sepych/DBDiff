<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class DropTableSQL implements SQLGenInterface
{

  private $obj;

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $table = $this->obj->table;

    return "DROP TABLE `$table`;";
  }

  public function getDown(): string
  {
    $table = $this->obj->table;
    $connection = $this->obj->connection;
    $res = $connection->select("SHOW CREATE TABLE `$table`");

    return $res[0]->{'Create Table'}.';';
  }

}
