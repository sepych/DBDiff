<?php
namespace DBDiff\Diff;


class AlterTableCollation
{

  public $prevCollation;
  public $collation;
  public $table;

  function __construct($table, $collation, $prevCollation)
  {
    $this->table = $table;
    $this->collation = $collation;
    $this->prevCollation = $prevCollation;
  }
}
