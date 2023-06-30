<?php
// first function scans eu site for links to profiles of MEP and returns array of profiles links.
function dajLinkiDoProfili()
{
  //check if link is valid 
  $homepage = file_get_contents('https://www.europarl.europa.eu/meps/en/full-list');

  $doc = new DOMDocument;
  $doc->loadHTML($homepage);

  $xpath = new DOMXPath($doc);
  $nodes = $xpath->query('//a[contains(@class,"erpl_member-list-item-content")]');

  $linkiDoProfili = array();

  foreach ($nodes as $node) {
    array_push($linkiDoProfili, $node->getAttribute('href'));
  }
  return $linkiDoProfili;
}
//second function takes link to MEP profile and harvests e-mail
function zbierzMaile($link)
{
  $stronaZMailem = file_get_contents($link);
  $doc = new DOMDocument;
  $doc->loadHTML($stronaZMailem);
  $xpath = new DOMXPath($doc);
  $nodes = $xpath->query('//a[contains(@class,"link_email")]');
  $maile = array();
  foreach ($nodes as $node) {
        array_push($maile, $node->getAttribute('href'));
      }
  unset($doc);
  unset($xpath);
  return $maile[0];
}
//set empty array
$maile = array();
//set array of links to profiles
$linki = dajLinkiDoProfili();
// for each link to profile harvest mail. Mails are reversed so script unreverse them and replaces [at] and [dot], and finally saves them into file
foreach ($linki as $key => $link) {
  $mail = zbierzMaile($link);
  $mail = str_replace('[at]', '@', $mail);
  $mail = str_replace('[dot]', '.', $mail);
  array_push($maile, $mail);
  file_put_contents('maile2.txt', strrev($mail) . PHP_EOL, FILE_APPEND);
  //it sleeps for 0,5s for every mail harvest
  usleep(500000);
}
