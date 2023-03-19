<?php
namespace DBDiff\Diff;


class DropTable
{

  private $connection;
  private $table;

  function __construct($table, $connection)
  {
    $this->table = $table;
    $this->connection = $connection;
  }
}
