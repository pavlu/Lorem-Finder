<?php

namespace App\Command\Finder;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class FinderHeadingsCommand extends Command
{
  protected static $defaultName = 'finder:headings';

  protected function configure(): void
  {
    $this
      ->addArgument('sitemap', InputArgument::REQUIRED);
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $sitemap_url = $input->getArgument('sitemap');
    $xml = simplexml_load_file($sitemap_url);

    foreach ($xml->url as $url_element) {
      $url = (string) $url_element->loc[0];
      $html = file_get_contents($url);

      $crawler = new Crawler($html);

      $headings1 = $crawler->filterXPath('descendant-or-self::h1');
      $headings2 = $crawler->filterXPath('descendant-or-self::h2');

      $output->writeln($url);

      foreach ($headings1 as $heading1) {
        $h1 = $heading1->nodeValue;
        $output->writeln('|-- H1:<info>' . $h1 . '</info>');
      }

      foreach ($headings2 as $heading2) {
        $h2 = $heading2->nodeValue;
        $output->writeln('|---- H2:<info>' . $h2 . '</info>');
      }
    }

    return Command::SUCCESS;
  }
}
