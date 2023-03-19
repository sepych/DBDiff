<?php
namespace DBDiff\Diff;


class UpdateData
{

  private $diff;
  private $table;

  function __construct($table, $diff)
  {
    $this->table = $table;
    $this->diff = $diff;
  }
}
