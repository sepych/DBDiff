<?php

namespace DBDiff;

use DBDiff\Params\DefaultParams;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Filesystem\Filesystem;


class Templater
{

  private string $up;
  private string $down;
  private DefaultParams $params;

  function __construct(DefaultParams $params, string $up, string $down)
  {
    $this->params = $params;
    $this->up = $up;
    $this->down = $down;
  }

  public function output(): void
  {
    $content = $this->getComments();
    $content .= $this->getContent();
    if (is_null($this->params->output)) {
      Logger::info("Writing migration file to ".getcwd()."/migration.sql");
      file_put_contents('migration.sql', $content);
    } else {
      Logger::info("Writing migration file to ".$this->params->output);
      file_put_contents($this->params->output, $content);
    }
  }

  private function getComments(): string
  {
    if ( ! $this->params->nocomments) {
      return "# Generated by DBDiff\n# On ".date('m/d/Y h:i:s a', time())."\n\n";
    }

    return "";
  }

  private function getContent(): bool|string
  {
    $compiler = new BladeCompiler(new Filesystem, ".");
    $template = $this->getTemplate();
    $compiled = $compiler->compileString(' ?>'.$template);
    $up = trim($this->up, "\n");
    $down = trim($this->down, "\n");
    ob_start();
    eval($compiled);
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }

  private function getTemplate(): bool|string
  {
    if (file_exists($this->params->template)) {
      return file_get_contents($this->params->template);
    }

    return "#---------- UP ----------\n{!! $this->up !!}\n#---------- DOWN ----------\n{!! $this->down !!}";
  }
}
