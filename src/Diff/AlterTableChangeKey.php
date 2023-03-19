<?php
namespace DBDiff\Diff;


class AlterTableChangeKey
{

  private $diff;
  private $key;
  private $table;

  function __construct($table, $key, $diff)
  {
    $this->table = $table;
    $this->key = $key;
    $this->diff = $diff;
  }
}
