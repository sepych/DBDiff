<?php
namespace DBDiff\Diff;


class SetDBCollation
{

  public $prevCollation;
  public $collation;
  public $db;

  function __construct($db, $collation, $prevCollation)
  {
    $this->db = $db;
    $this->collation = $collation;
    $this->prevCollation = $prevCollation;
  }
}
