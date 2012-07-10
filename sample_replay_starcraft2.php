<?php
ini_set('include_path', ini_get('include_path').':/home/www/includes/');
include('Starcraft2.php');

$r = new Games_Replay_Starcraft2();
if ($r->parse('./rep')) {
  if ($r->r_infos['winner_known']) {
    $winners = '';
    foreach($r->r_infos['players'] as $player) {
      if ($player['won'] == 1) {
	$winners .= $player['name'].', ';
      }
    }
    if (strlen($winners)) {
      $winners = substr($winners, 0, -2);
    } else {
      $winners = 'Inconnu';
    }
  } else {
    $winners = 'Inconnu';
  }
  var_dump($winners);
} else {
  die("err\n");
}