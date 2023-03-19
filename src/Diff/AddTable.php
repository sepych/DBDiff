<?php
namespace DBDiff\Diff;


class AddTable
{

  public $connection;
  public $table;

  function __construct($table, $connection)
  {
    $this->table = $table;
    $this->connection = $connection;
  }
}
