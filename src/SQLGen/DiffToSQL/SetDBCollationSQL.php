<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class SetDBCollationSQL implements SQLGenInterface
{

  private $obj;

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $db = $this->obj->db;
    $collation = $this->obj->collation;

    return "ALTER DATABASE `$db` COLLATE $collation;";
  }

  public function getDown(): string
  {
    $db = $this->obj->db;
    $prevCollation = $this->obj->prevCollation;

    return "ALTER DATABASE `$db` COLLATE $prevCollation;";
  }

}
