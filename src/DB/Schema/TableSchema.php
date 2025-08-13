<?php
namespace DBDiff\DB\Schema;

use DBDiff\DB\DBManager;
use Diff\Differ\MapDiffer;

use DBDiff\Diff\AlterTableEngine;
use DBDiff\Diff\AlterTableCollation;

use DBDiff\Diff\AlterTableAddColumn;
use DBDiff\Diff\AlterTableChangeColumn;
use DBDiff\Diff\AlterTableDropColumn;

use DBDiff\Diff\AlterTableAddKey;
use DBDiff\Diff\AlterTableChangeKey;
use DBDiff\Diff\AlterTableDropKey;

use DBDiff\Diff\AlterTableAddConstraint;
use DBDiff\Diff\AlterTableChangeConstraint;
use DBDiff\Diff\AlterTableDropConstraint;

use DBDiff\Logger;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpChange;
use Diff\DiffOp\DiffOpRemove;
use Illuminate\Database\Connection;


class TableSchema
{

  private DBManager $manager;
  private Connection $source;
  private Connection $target;

  function __construct(DBManager $manager)
  {
    $this->manager = $manager;
    $this->source = $this->manager->getDB('source');
    $this->target = $this->manager->getDB('target');
  }

  public function getSchema($connection, $table): array
  {
    // collation & engine
    $status = $this->{$connection}->select("show table status like '$table'");
    $engine = $status[0]->Engine;
    $collation = $status[0]->Collation;

    $schema = $this->{$connection}->select("SHOW CREATE TABLE `$table`")[0]->{'Create Table'};
    
    // Find the start and end positions of the main table definition (between parentheses)
    $startPos = strpos($schema, '(');
    if ($startPos === false) {
      // Handle edge case where there's no parenthesis
      return [
        'engine' => $engine,
        'collation' => $collation,
        'columns' => [],
        'keys' => [],
        'constraints' => [],
      ];
    }
    
    // Start position is right after the opening parenthesis
    $startPos++;
    
    // Find the matching closing parenthesis by parsing from left to right
    // This handles nested parentheses correctly
    $depth = 1;
    $endPos = $startPos;
    $length = strlen($schema);
    
    while ($depth > 0 && $endPos < $length) {
      $char = $schema[$endPos];
      if ($char === '(') {
        $depth++;
      } elseif ($char === ')') {
        $depth--;
      }
      $endPos++;
    }
    
    // Back up one position to get to the actual closing parenthesis
    $endPos--;
    
    // Extract the content between parentheses
    if ($endPos > $startPos) {
      $content = substr($schema, $startPos, $endPos - $startPos);
      $lines = array_map(function ($el) {
        return trim($el);
      }, explode("\n", $content));
    } else {
      // Fallback in case something went wrong
      $lines = [];
    }

    $columns = [];
    $keys = [];
    $constraints = [];

    foreach ($lines as $line) {
      // Skip empty lines
      if (empty(trim($line))) continue;
      
      // Try to extract identifier in backticks
      preg_match("/`([^`]+)`/", $line, $matches);
      
      // Skip lines without identifiers (like comments or empty lines)
      if (empty($matches)) continue;
      
      $name = $matches[1];
      $line = trim($line, ',');
      if (starts_with($line, '`')) { // column
        $columns[$name] = $line;
      } else {
        if (starts_with($line, 'CONSTRAINT')) { // constraint
          $constraints[$name] = $line;
        } else { // keys
          $keys[$name] = $line;
        }
      }
    }

    return [
      'engine' => $engine,
      'collation' => $collation,
      'columns' => $columns,
      'keys' => $keys,
      'constraints' => $constraints,
    ];
  }

  public function getDiff($table): array
  {
    Logger::info("Now calculating schema diff for table `$table`");

    $diffSequence = [];
    $sourceSchema = $this->getSchema('source', $table);
    $targetSchema = $this->getSchema('target', $table);

    // Engine
    $sourceEngine = $sourceSchema['engine'];
    $targetEngine = $targetSchema['engine'];
    if ($sourceEngine != $targetEngine) {
      $diffSequence[] = new AlterTableEngine($table, $sourceEngine, $targetEngine);
    }

    // Collation
    $sourceCollation = $sourceSchema['collation'];
    $targetCollation = $targetSchema['collation'];
    if ($sourceCollation != $targetCollation) {
      $diffSequence[] = new AlterTableCollation($table, $sourceCollation, $targetCollation);
    }

    // Columns
    $sourceColumns = $sourceSchema['columns'];
    $targetColumns = $targetSchema['columns'];
    $differ = new MapDiffer();
    $diffs = $differ->doDiff($targetColumns, $sourceColumns);
    foreach ($diffs as $column => $diff) {
      if ($diff instanceof DiffOpRemove) {
        $diffSequence[] = new AlterTableDropColumn($table, $column, $diff);
      } else {
        if ($diff instanceof DiffOpChange) {
          $diffSequence[] = new AlterTableChangeColumn($table, $column, $diff);
        } else {
          if ($diff instanceof DiffOpAdd) {
            $diffSequence[] = new AlterTableAddColumn($table, $column, $diff);
          }
        }
      }
    }

    // Keys
    $sourceKeys = $sourceSchema['keys'];
    $targetKeys = $targetSchema['keys'];
    $differ = new MapDiffer();
    $diffs = $differ->doDiff($targetKeys, $sourceKeys);
    foreach ($diffs as $key => $diff) {
      if ($diff instanceof DiffOpRemove) {
        $diffSequence[] = new AlterTableDropKey($table, $key, $diff);
      } else {
        if ($diff instanceof DiffOpChange) {
          $diffSequence[] = new AlterTableChangeKey($table, $key, $diff);
        } else {
          if ($diff instanceof DiffOpAdd) {
            $diffSequence[] = new AlterTableAddKey($table, $key, $diff);
          }
        }
      }
    }

    // Constraints
    $sourceConstraints = $sourceSchema['constraints'];
    $targetConstraints = $targetSchema['constraints'];
    $differ = new MapDiffer();
    $diffs = $differ->doDiff($targetConstraints, $sourceConstraints);
    foreach ($diffs as $name => $diff) {
      if ($diff instanceof DiffOpRemove) {
        $diffSequence[] = new AlterTableDropConstraint($table, $name, $diff);
      } else {
        if ($diff instanceof DiffOpChange) {
          $diffSequence[] = new AlterTableChangeConstraint($table, $name, $diff);
        } else {
          if ($diff instanceof DiffOpAdd) {
            $diffSequence[] = new AlterTableAddConstraint($table, $name, $diff);
          }
        }
      }
    }

    return $diffSequence;
  }

}
