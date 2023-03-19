<?php
namespace DBDiff\Diff;


class AddTable
{

  private $connection;
  private $table;

  function __construct($table, $connection)
  {
    $this->table = $table;
    $this->connection = $connection;
  }
}
