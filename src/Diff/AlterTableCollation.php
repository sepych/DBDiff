<?php
namespace DBDiff\Diff;


class AlterTableCollation
{

  private $prevCollation;
  private $collation;
  private $table;

  function __construct($table, $collation, $prevCollation)
  {
    $this->table = $table;
    $this->collation = $collation;
    $this->prevCollation = $prevCollation;
  }
}
