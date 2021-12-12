<?php

namespace App\Command\Finder;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class FinderLoremIpsumCommand extends Command
{
  protected static $defaultName = 'finder:lorem-ipsum';

  protected function configure(): void
  {
    $this
      ->addArgument('sitemap', InputArgument::REQUIRED);
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $sitemap_url = $input->getArgument('sitemap');
    $xml = simplexml_load_file($sitemap_url);
    $founded = false;

    foreach ($xml->url as $url_element) {
      $url = (string) $url_element->loc[0];
      $html = file_get_contents($url);

      $find = array(
        'lorem',
        'ipsum'
      );

      if ($this->contains($html, $find) === true) {
        $output->writeln('<error>' . $url . '</error>');
        $founded = true;
      } else {
        $output->writeln('<info>' . $url . '</info>');
      }
    }

    $output->writeln('====================================');

    if ($founded) {
      $output->writeln('Lorem Ipsum founded :(');
    } else {
      $output->writeln('No Lorem Ipsum founded. Good Job :)');
    }

    $output->writeln('====================================');

    return Command::SUCCESS;
  }

  protected function contains($str, array $arr)
  {
    foreach ($arr as $a) {
      if (stripos($str, $a) !== false) return true;
    }
    return false;
  }
}