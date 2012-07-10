<?php
include 'Starcraft2.php';
$s = new Games_Stats_Starcraft2();
var_dump($s->getPlayerStats('RoMe', 769092));