<?php
namespace DBDiff\Diff;


class SetDBCollation
{

  private $prevCollation;
  private $collation;
  private $db;

  function __construct($db, $collation, $prevCollation)
  {
    $this->db = $db;
    $this->collation = $collation;
    $this->prevCollation = $prevCollation;
  }
}
