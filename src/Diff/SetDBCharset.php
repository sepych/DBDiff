<?php
namespace DBDiff\Diff;


class SetDBCharset
{

  private $prevCharset;
  private $charset;
  private $db;

  function __construct($db, $charset, $prevCharset)
  {
    $this->db = $db;
    $this->charset = $charset;
    $this->prevCharset = $prevCharset;
  }
}
