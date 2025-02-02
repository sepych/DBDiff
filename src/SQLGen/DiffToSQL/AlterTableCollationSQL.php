<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableCollationSQL implements SQLGenInterface
{

  private $obj;

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $table = $this->obj->table;
    $collation = $this->obj->collation;

    return "ALTER TABLE `$table` DEFAULT COLLATE $collation;";
  }

  public function getDown(): string
  {
    $table = $this->obj->table;
    $prevCollation = $this->obj->prevCollation;

    return "ALTER TABLE `$table` DEFAULT COLLATE $prevCollation;";
  }

}
