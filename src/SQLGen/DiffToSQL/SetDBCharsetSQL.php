<?php
namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class SetDBCharsetSQL implements SQLGenInterface
{

  private $obj;

  function __construct($obj)
  {
    $this->obj = $obj;
  }

  public function getUp(): string
  {
    $db = $this->obj->db;
    $charset = $this->obj->charset;

    return "ALTER DATABASE `$db` CHARACTER SET $charset;";
  }

  public function getDown(): string
  {
    $db = $this->obj->db;
    $prevCharset = $this->obj->prevCharset;

    return "ALTER DATABASE `$db` CHARACTER SET $prevCharset;";
  }

}
