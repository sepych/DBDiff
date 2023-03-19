<?php
namespace DBDiff\DB\Data;

use DBDiff\Diff\InsertData;
use DBDiff\Diff\UpdateData;
use DBDiff\Diff\DeleteData;
use DBDiff\Logger;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;


class DistTableData
{

  private $target;
  private $source;
  private $manager;

  function __construct($manager)
  {
    $this->manager = $manager;
    $this->source = $this->manager->getDB('source');
    $this->target = $this->manager->getDB('target');
  }

  public function getIterator($connection, $table): TableIterator
  {
    return new TableIterator($this->{$connection}, $table);
  }

  public function getDataDiff($table, $key): array
  {
    $sourceIterator = $this->getIterator('source', $table);
    $targetIterator = $this->getIterator('target', $table);
    $differ = new ArrayDiff($key, $sourceIterator, $targetIterator);

    return $differ->getDiff($table);
  }

  public function getDiff($table, $key): array
  {
    Logger::info("Now calculating data diff for table `$table`");
    $diffs = $this->getDataDiff($table, $key);
    $diffSequence = [];
    foreach ($diffs as $name => $diff) {
      if ($diff['diff'] instanceof DiffOpRemove) {
        $diffSequence[] = new DeleteData($table, $diff);
      } else {
        if (is_array($diff['diff'])) {
          $diffSequence[] = new UpdateData($table, $diff);
        } else {
          if ($diff['diff'] instanceof DiffOpAdd) {
            $diffSequence[] = new InsertData($table, $diff);
          }
        }
      }
    }

    return $diffSequence;
  }

}
