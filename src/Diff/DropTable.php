<?php
namespace DBDiff\Diff;


class DropTable
{

  public $connection;
  public $table;

  function __construct($table, $connection)
  {
    $this->table = $table;
    $this->connection = $connection;
  }
}
