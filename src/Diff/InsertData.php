<?php
namespace DBDiff\Diff;


class InsertData
{

  private $diff;
  private $table;

  function __construct($table, $diff)
  {
    $this->table = $table;
    $this->diff = $diff;
  }
}
