<?php
namespace DBDiff\Diff;


class AlterTableDropKey
{

  public $diff;
  public $key;
  public $table;

  function __construct($table, $key, $diff)
  {
    $this->table = $table;
    $this->key = $key;
    $this->diff = $diff;
  }
}
