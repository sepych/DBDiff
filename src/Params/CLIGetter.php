<?php
namespace DBDiff\Params;

use DBDiff\Exceptions\CLIException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class CLIGetter implements ParamsGetter
{

  /**
   * @return DefaultParams
   * @throws CLIException
   */
  public function getParams(): DefaultParams
  {
    $params = new DefaultParams();

    $inputDefinition = new InputDefinition([
      new InputArgument('resource', InputArgument::REQUIRED, 'Resources to compare'),
      new InputOption('server1', null, InputOption::VALUE_REQUIRED, 'Server 1 connection string'),
      new InputOption('server2', null, InputOption::VALUE_REQUIRED, 'Server 2 connection string'),
      new InputOption('format', null, InputOption::VALUE_REQUIRED, 'Output format'),
      new InputOption('template', null, InputOption::VALUE_REQUIRED, 'Template to use'),
      new InputOption('type', null, InputOption::VALUE_REQUIRED, 'Type of comparison'),
      new InputOption('include', null, InputOption::VALUE_REQUIRED, 'Include pattern'),
      new InputOption('nocomments', null, InputOption::VALUE_OPTIONAL, 'No comments flag'),
      new InputOption('config', null, InputOption::VALUE_REQUIRED, 'Config file'),
      new InputOption('output', null, InputOption::VALUE_REQUIRED, 'Output file'),
      new InputOption('debug', null, InputOption::VALUE_OPTIONAL, 'Debug mode'),
    ]);

    $input = new ArgvInput(null, $inputDefinition);

    try {
      $input->bind($inputDefinition);
      
      $resourceArg = $input->getArgument('resource');
      if ($resourceArg) {
        $params->input = $this->parseInput($resourceArg);
      } else {
        throw new CLIException("Missing input");
      }

      if ($input->getOption('server1')) {
        $params->server1 = $this->parseServer($input->getOption('server1'));
      }
      if ($input->getOption('server2')) {
        $params->server2 = $this->parseServer($input->getOption('server2'));
      }
      if ($input->getOption('format')) {
        $params->format = $input->getOption('format');
      }
      if ($input->getOption('template')) {
        $params->template = $input->getOption('template');
      }
      if ($input->getOption('type')) {
        $params->type = $input->getOption('type');
      }
      if ($input->getOption('include')) {
        $params->include = $input->getOption('include');
      }
      if ($input->hasOption('nocomments') && $input->getOption('nocomments') !== null) {
        $params->nocomments = $input->getOption('nocomments');
      }
      if ($input->getOption('config')) {
        $params->config = $input->getOption('config');
      }
      if ($input->getOption('output')) {
        $params->output = $input->getOption('output');
      }
      if ($input->hasOption('debug') && $input->getOption('debug') !== null) {
        $params->debug = $input->getOption('debug');
      }
    } catch (\Exception $e) {
      throw new CLIException("Error parsing command line arguments: " . $e->getMessage());
    }

    return $params;
  }

  protected function parseServer($server): array
  {
    $parts = explode('@', $server);
    $creds = explode(':', $parts[0]);
    $dns = explode(':', $parts[1]);

    return [
      'user' => $creds[0],
      'password' => $creds[1],
      'host' => $dns[0],
      'port' => $dns[1],
    ];
  }

  protected function parseInput($input): array
  {
    $parts = explode(':', $input);
    if (sizeof($parts) !== 2) {
      throw new CLIException("You need two resources to compare");
    }
    $first = explode('.', $parts[0]);
    $second = explode('.', $parts[1]);
    if (sizeof($first) !== sizeof($second)) {
      throw new CLIException("The two resources must be of the same kind");
    }

    if (sizeof($first) === 2) {
      return [
        'kind' => 'db',
        'source' => ['server' => $first[0], 'db' => $first[1]],
        'target' => ['server' => $second[0], 'db' => $second[1]],
      ];
    } else {
      if (sizeof($first) === 3) {
        return [
          'kind' => 'table',
          'source' => ['server' => $first[0], 'db' => $first[1], 'table' => $first[2]],
          'target' => ['server' => $second[0], 'db' => $second[1], 'table' => $second[2]],
        ];
      } else {
        throw new CLIException("Unkown kind of resources");
      }
    }
  }
}
