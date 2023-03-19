<?php
namespace DBDiff\Diff;


class InsertData
{

  public $diff;
  public $table;

  function __construct($table, $diff)
  {
    $this->table = $table;
    $this->diff = $diff;
  }
}
