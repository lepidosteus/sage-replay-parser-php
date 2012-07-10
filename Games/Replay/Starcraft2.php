<?php
require_once('phpsc2replay/php/mpqfile.php');
require_once('phpsc2replay/php/sc2replay.php');
require_once('phpsc2replay/php/sc2replayutils.php');

class Games_Replay_Starcraft2
{
    public $game_name = 'StarCraft 2';
    public $game_short_name = 'SC2';

    public $r_infos = array(
        'players' => array(),
        'obs' => array(),
        'map' => '',
        'type' => '',
        'version' => '0.0.0.0',
        'length' => 0,
        'real_length' => 0
        );

    public static $versions = array(
        '1.0.3.16291' => '1.0.3',
        '1.0.2.16223' => '1.0.2',
        '1.0.1.16195' => '1.0.1',
        '1.0.0.16117' => '1.0',
        '0.15.0.15449' => 'B-13',
        '1.14.0.15392' => 'B-12b',
        '1.14.0.15343' => 'B-12',
        '1.13.0.15250' => 'B-11',
        '1.11.0.15133' => 'B-9'
        );

    public static $armies = array(0 => 'terran',
        1 => 'zerg',
        2 => 'protoss'
        );

    public static $gameSpeeds = array(
        0 => "Plus lent",
        1 => "Lent",
        2 => "Normal",
        3 => "Rapide",
        4 => "Plus rapide"
    );
    public static $difficultyLevels = array(
        0 => "Très facile",
        1 => "Facile",
        2 => "Moyen",
        3 => "Difficile",
        4 => "Très difficile",
        5 => "Dément"
    );

    public function version()
    {
        if (isset(self::$versions[$this->r_infos['version']])) {
            return self::$versions[$this->r_infos['version']];
        }
        return substr($this->r_infos['version'], 0, strrpos($this->r_infos['version'], '.'));
    }

    public function getRawVersion()
    {
        return $this->r_infos['version'];
    }

    public function getMapHash()
    {
        return $this->r_infos['map_hash'];
    }

    public function getSpeed()
    {
        if (isset(self::$gameSpeeds[$this->r_infos['speed']])) {
            return self::$gameSpeeds[$this->r_infos['speed']];
        }
        return 'Inconnue';
    }

    public function parse($path)
    {
        $mpq = new MPQFile($path, true, false);
        $init = $mpq->getState();
        if (!$init) {
            return false;
        }
        $r = $mpq->parseReplay();
        if (!$r) {
            return false;
        }
        $this->r_infos['version'] = $mpq->getVersionString();
        $this->r_infos['realm'] = $r->getRealm();
        $this->r_infos['speed'] = $r->getGameSpeed();
        $this->r_infos['winner_known'] = $r->isWinnerKnown();
        $this->r_infos['type'] = $r->getTeamSize();
        $this->r_infos['length'] = $r->getGameLength();
        $this->r_infos['real_length'] = (int)($this->r_infos['length'] * 60) / 83.5;
        $this->r_infos['map'] = $r->getMapName();
        $this->r_infos['map_hash'] = $r->getMapHash();
        $this->r_infos['players'] = $r->getPlayers();
        foreach ($this->r_infos['players'] as $k => &$player) {
            if ($player['isObs']) {
                $this->r_infos['obs'][] = $this->r_infos['players'][$k];
                unset($this->r_infos['players'][$k]);
		continue;
            }
            $player['army'] = $player['race'];
            $player['bnet_id'] = (int)$player['uid']/* / 2*/;
        }
        $this->computeAPM();
        $this->_computeMessages($r);
	$this->_computeWinner();
        return true;
    }

    protected function _computeWinner()
    {
      if (!$this->r_infos['winner_known']
	  && count($this->r_infos['players']) > 2) {
	$teams = array();
	foreach ($this->r_infos['players'] as $player) {
	  $score = isset($player['won']) ? $player['won'] : 0;
	  if (!isset($teams[$player['team']])) {
	    $teams[$player['team']] = $score;
	  } else {
	    $teams[$player['team']] += $score;
	  }
	}
	asort($teams);
	$found = false;
	foreach ($teams as $team => $score) {
	  if (!isset($pscore)) {
	    $pscore = $score;
	  } elseif ($score != $pscore) {
	    if ($score > $pscore) {
	      $team_win = $team;
	    }
	    $found = true;
	  }
	}
	if ($found) {
	  $this->r_infos['winner_known'] = true;
	  foreach ($this->r_infos['players'] as $k => $player) {
	    if ($player['team'] == $team_win) {
	      $this->r_infos['players'][$k]['won'] = 1;
	    } elseif (!isset($player['won'])) {
	      $this->r_infos['players'][$k]['won'] = 0;
	    }
	  }
	}
      }
    }

    protected function _computeMessages($replay)
    {
        $this->r_infos['messages'] = $replay->getMessages();
        foreach ($this->r_infos['messages'] as &$msg) {
            $msg['message'] = utf8_decode($msg['message']);
            foreach ($this->r_infos['players'] as $player) {
                if ($player['name'] == $msg['name']) {
                    if ($msg['target'] == 2) {
                        $msg['dest'] = 'team';
                        $msg['dest_txt'] = utf8_decode('allié');
                    } else {
                        $msg['dest'] = 'all';
                        $msg['dest_txt'] = 'tous';
                    }
                    continue 2;
                }
            }
            $msg['dest'] = 'obs';
            $msg['dest_txt'] = 'obs';
        }
    }

    public function computeAPM()
    {
        $this->r_infos['apm_tick_length'] = ceil($this->r_infos['length'] / 10);
        $this->r_infos['apm_mark_length'] = floor($this->r_infos['apm_tick_length'] / 3);
        $this->r_infos['apm_point_length'] = floor($this->r_infos['apm_tick_length'] / 8);
        //correction pour highchart, on force à la minute supérieure
        while (($this->r_infos['apm_tick_length'] % 60) != 0) {
            $this->r_infos['apm_tick_length']++;
        }
        $this->r_infos['apm_max_total'] = 50;
        $this->r_infos['apm_max_avg'] = 50;

        foreach ($this->r_infos['players'] as &$player) {
            $session_mark = 0;
            $session_point = 0;
            $mark = $this->r_infos['apm_mark_length'];
            $point = $this->r_infos['apm_point_length'];
            $player['apm_marks'] = array();
            $player['apm_points'] = array();
            $player['apm_avg'] = 0;
            foreach ($player['apm'] as $time => $actions) {
                if ($time >= $mark) {
                    $player['apm_marks'][$mark] = (int)floor($session_mark * (60 / $this->r_infos['apm_mark_length']));
                    // recalibre l'axe
                    while ($player['apm_marks'][$mark] > $this->r_infos['apm_max_total']) {
                        $this->r_infos['apm_max_total'] += 20;
                    }
                    $mark += $this->r_infos['apm_mark_length'];
                    $session_mark = 0;
                }
                if ($time >= $point) {
                    $player['apm_points'][$point] = (int)floor($session_point * (60 / $this->r_infos['apm_point_length']));
                    // recalibre l'axe
                    while ($player['apm_points'][$point] > $this->r_infos['apm_max_total']) {
                        $this->r_infos['apm_max_total'] += 20;
                    }
                    $point += $this->r_infos['apm_point_length'];
                    $session_point = 0;
                }
                $session_mark += (int)$actions;
                $session_point += (int)$actions;
            }
            // termine les dernieres marque
            $player['apm_marks'][$mark] = (int)$session_mark;
            $player['apm_points'][$point] = (int)$session_point;
            // moyenne globale
            $player['apm_avg'] = (int)floor($player['apmtotal'] / floor($time / 60));
            // moyenne globale apres differentiation
            $player['apm_avg_valid'] = (int)floor(array_sum($player['apm']) / floor($time / 60));
            // recalibre l'axe
            while (max($player['apm_avg'], $player['apm_avg_valid']) > $this->r_infos['apm_max_avg']) {
                $this->r_infos['apm_max_avg'] += 20;
            }
        }
    }

    public function isClanGame()
    {
        return false;
    }

    public function frmap()
    {
        if (isset(self::$maps[$this->r_infos['map_hash']])) {
            return self::$maps[$this->r_infos['map_hash']];
        }
        return $this->r_infos['map'];
    }

    public function enmap()
    {
        if (isset(SC2ReplayUtils::$depHashes[$this->r_infos['map_hash']])) {
            return SC2ReplayUtils::$depHashes[$this->r_infos['map_hash']]['name'];
        }
        return $this->r_infos['map'];
    }

    public function map()
    {
        if (isset(SC2ReplayUtils::$depHashes[$this->r_infos['map_hash']])) {
            return SC2ReplayUtils::$depHashes[$this->r_infos['map_hash']]['name'];
        }
        return 'Unknown map';
    }

    function _subFrmtStr($m)
    {
        $stags = array_slice(explode('_', substr($m[0], 1, - 1)), 1);
        $subtags = array('army', 'fullarmy', 'clan');
        foreach ($subtags as $subtag) {
            $stname = 's' . $subtag;
            // we are injecting the variable into the current namespace
            // ie this will create a $sclan variable into the fonction namespace
            if (in_array($subtag, $stags)) {
                $$stname = true;
            } else {
                $$stname = false;
            }
        }
        switch ($m[1]) {
            case 'map':
                $s = utf8_decode($this->frmap());
                break;
            case 'version':
                $s = $this->version();
                break;
            case 'players':
                $s = $this->getPlayersList($sclan, $sarmy, $sfullarmy);
                break;
            case 'clans':
                $s = '';
                break;
            case 'matchup':
                $s = $this->getMatchupStr();
                break;
            default:
                $s = $m[0]; // we should not be here, invalid tag, left it untouched
                break;
        }
        return $s;
    }

    function getPlayersList($clan = false, $army = false, $fullarmy = false)
    {
        $teams = $this->getTeams();
        $s = '';
        foreach ($teams as $tnum => $team) {
            $ss = '';
            foreach ($team as $player) {
                if (!empty($ss)) {
                    $ss .= ' ' . ($tnum == 0 ? 'vs' : '+') . ' ';
                }
                $ss .= $player['name'];
                if ($fullarmy) {
                    $ss .= ' (' . (ucfirst($player['army'])) . ')';
                } else if ($army) {
                    $ss .= ' (' . (ucfirst(substr($player['army'], 0, 1))) . ')';
                }
            }
            if (empty($ss)) {
                continue;
            }
            if (!empty($s)) {
                $s .= ' vs ';
            }
            $s .= $ss;
        }
        return $s;
    }

    function getTeams($omit = array(2, 3))
    {
        $teams = array();
        do {
            foreach ($this->r_infos['players'] as $player) {
                if (!isset($teams[$player['team']]) || !is_array($teams[$player['team']])) {
                    $teams[$player['team']] = array();
                }
                array_push($teams[$player['team']], $player);
            }
        } while (false); // we're not looping, just abusing do/while breaking
        return $teams;
    }

    public function getMatchup()
    {
        if ($this->r_infos['type'] == '1v1') {
            return $GLOBALS['_replay_matchup']['1v1'];
        } else if ($this->r_infos['type'] == '2v2') {
            return $GLOBALS['_replay_matchup']['2v2'];
        } else if ($this->r_infos['type'] == '3v3') {
            return $GLOBALS['_replay_matchup']['3v3'];
        } else if ($this->r_infos['type'] == '4v4') {
            return $GLOBALS['_replay_matchup']['4v4'];
        } else {
            return $GLOBALS['_replay_matchup']['ffa'];
        }
    }

    public function FrmtStr($frmt)
    {
        $tags = array('map', 'version', 'players', 'clans', 'matchup');
        $subtags = array('army', 'fullarmy', 'clan');
        // this will match invalid sub tags, but they will not be treated afterward
        // eg: %map_army_clan% will be treated like %map%
        return preg_replace_callback(
            '/%(' . implode('|', $tags) . ')(_(' . implode('|', $subtags) . ')){0,2}%/',
            array($this, '_subFrmtStr'),
            $frmt
            );
    }

    public function getMatchupStr()
    {
        return $this->r_infos['type'];
    }

    public function getPlayersCount()
    {
        return count($this->r_infos['players']);
    }

    public static $maps = array(
        "d0482679c925f8c5dd6228f2411c995e0fd2768015eb3dbc777584922731d349" => "Abyss",
        "ee129ab013dd3fe080fc437d19b380b868ab3e302c35ae526ea6f15af6f6b078" => "Agria Valley",
        "4fa2764424a0d4b06c3c3f160df5ce17c382cec712d1a0b9aec104ca2236eff3" => "Arakan Citadel",
        "c22955a9e3b97f3511d1d3950859ce9d2855a338fb5ab931eb977c7188255bd6" => "Arid Wastes",
        "7abaae864c20201d74f657a6f7406106f113c8e6b52b078ecafdaaf290a761b6" => "Arid Wastes",
        "e33a5890ad2eedeaa56ce17356d1e618ad4ff99f50e76a34e7772e087802e8ea" => "Désert Ardent",
        "7dbf6b641bc5fce7abdaa352c66a9ba4e942d5bdf48a67e33cbff0ff32b3d47c" => "Burial Grounds",
        "60d74c966e691599876ea18b55db99b36f2b62b85c05ff4e69551bb10bc0fd0b" => "Colony 426",
        "824d92a85f50bc4924966d0c0ad8733ae581020cc0409c7ec42c76bc16fcaa26" => "Crossfire",
        "b68951b568e7255f442d7285a178870494b9b3e6023b831770a7558400b4cf5e" => "Debris Field",
        "5dc1d6dde914c02b8d64adf8f51cb1f811be7e89db53c6c0ded8c6e057202bad" => "Quadrant Delta",
        "f34aa7113bb3c1a7892bfa0dd60de7f34e619434ebefe45412164277abed5bf3" => "Oasis du Désert",
        "2b3a003426de842f63fb97f2f68e6b71a6177d98ef9a945aba877f6cfb2c0a89" => "Dig Site",
        "8fe944d0879bf22c6fc7a6b3f36ea946e43d6636448d6a08b7734d6822bc425d" => "Dirt Side",
        "505b560e0d2739a919384766e2f331fd42483a347c92111549c442e7e1cd738d" => "Discord IV",
        "58fca7d89dc576b889ae1f7ab194f425545fbe8c9dac2c3d739fcb1cd2c2f324" => "Elysium",
        "da2512882b815f774b0a7469e0d52945c486599406271e10d8cb7193e77c64db" => "Extinction",
        "c845f8e0b2084352047ed0ea2d8701321e3fa908b4d67b5002d04656b45b4a45" => "Forbidden Planet",
        "36422e713b8b825a7aa8c4f27301145400bacf76fd5ddd5caedde411c935ea0d" => "Frontier",
        "4c26d855a635fae81efa1bbebc39f52a3a0703ac966698288fe526cdaad7f35d" => "High Ground",
        "1d961d930214c7de51b903db4c2e6796aee154531fe344686ce54803107a168c" => "High Orbit",
        "395fb54902a9ae84698940dbf414a91520b3c27322515ff3e7e600b548ac81b7" => "Hâvre Calciné",
        "d3c721754d1534500142950e3943a0bd631f54df56593096c7740389f01fa816" => "Jungle Basin",
        "a49177ada7f0f1848761319edd889f31a925b292a37b5d2622628a5dfa267f43" => "Junk Yard",
        "c7667c45f7cc6e3c2c453feee632b34dae9670f4a269bd373872f70cabd274eb" => "Ravin de Kulas",
        "99e71fcbffff967b8f008d87e297c78ab913d0d129ca354ec8174b57f398ce68" => "Ravin de Kulas",
        "5e43b6129af26498d8c84a230936a2eb350e6f0ba54791cd41d5a22b6f728556" => "Lava Flow",
        "cfa196a601daaadd3d29a16cd1b2cf0a2917ad8d959ca9813ca12ce123782760" => "Temple Oublié",
        "711dd8749cd5f04a06d1cf2f5f7b045febf9a655c382d09424620752594657d1" => "Megaton",
        "71d1c6773ad36035333cad61ceac0789828e3b49690fad95efb1475ca3b6f1d8" => "Metalopolis",
        "5f1c8e57d5b1f33dd7a116fad1157848e890ee07de12181488fb77506b96279d" => "Monlyth Ridge",
        "ce28ed769b1e73267c94256fafec4c5569d346e325d06fdcb6d8339d4d3c8d5a" => "Monsoon",
        "3e600ff2917bae8e5734e56808958ca59978f3868c06272761a5194b0c58ad32" => "New Antioch",
        "5c19269e878b4b9fbb37131eea80c3b3c828cc71f48c5d87b14e05585104a6d6" => "Nightmare",
        "638cbab4b1d5db1025c6edd07d9316f39731103fffcefaf6f0bd310cd8cc8002" => "Désert Ardent - Débutant",
        "f57d57fff5c83846f4c29d54afe14b32c29914f384f31eae76cba67c772aac6c" => "Oasis du Désert - Débutant",
        "1fc47bb57ac40ba7187ef8df88574208be0f5ab33864beba5a42ddb78ecd6482" => "Novice Discord IV",
        "2d6f32bd6489cafc593d932d71d8975c3f1d5bf33618f66d39a3ffa5138965d1" => "Ravin de Kulas - Débutant",
        "bb1b7483a7a8424f9e3463198a2e9830a8bae4cb05fc2be5eb8f7398a3f7feb9" => "Metalopolis - Débutant",
        "614bb763e37ee9e422d4747c4e1979a32a14e7013db02b386d5ef9d5c48108e5" => "Novice Monlyth Ridge",
        "0c09c2ab0f2243e3b0cef68a285e6ab83845f51e51d9cefb8acba2030f69fd0b" => "Steppes de guerre - Débutant",
        "9a037ed0bb5bb0a5cd5f62961d89f319a6cef92fa0765645f1171cafca6a2012" => "Novice Terminus",
        "50f1dec30dbea330227e19d986f60b7cfeedd96bb12766ca473db4cf77a1f044" => "Forteresse du Crépuscule - Débutant",
        "3ab8aa07be82e09220e5bf6c0bb433907d84f5c31c8a18d97a776cd70681a291" => "Outpost",
        "c685252a915588b6143e77450995b9ccf91f1260030c04d823a9d8deac9a5e7a" => "Primeval",
        "0d5ca2cfdfdec15c9343d2a41f5fb6246b6dbcc4b738924c361747af1d34d2ce" => "Quicksand",
        "ca6d7bf4f761ad3be1ce2548136ff4485e711bb166fc867ad6570026aa09baec" => "Red Stone Gulch",
        "32e63267ec70860333cce4b11b2656908668bb8821e5faa2f7d429b341133632" => "Sacred Ground",
        "509a19f1cfc38ed0c34a71ec472b1c27aaa55d4fe02a35bbbc3c1e41ef05b006" => "Sand Canyon",
        "f88dfc7b28d920295984c0190a4eb4d33c16ef78acf82aaa70deedcc2e751de1" => "Hâvre Calciné",
        "3e066c551a8692fef70f653a09d68959092ea2a3c8cc87e83b34602964c3b97d" => "Station de recyclage",
        "c890239f06d85410e2709991c854965529b811ec54638d8103f3ef3d93d073e1" => "Shakuras Plateau",
        "0ab117440ef3ddc71dc180c73a860c23bf43e118e22c54114bc29b3bd0c038cc" => "Steppes de guerre",
        "916959be930e8a42e31fe085a6c47aed2cc0f88f261d82b5fa511dde7d877f3b" => "Steppes de guerre",
        "8a5123168202e152a93e46446151ceba237d47310b766273836f279f71e0f133" => "Tarsonis Assault",
        "b6a71bd3eff8399dd6e374d3639b8950ea74763cc15b90fff197a0cad5b2a849" => "Tectonic Rift",
        "f9039f994680d63928a549ec2082fd8429b0aa454c6f3346b2330c7dae47dee0" => "Tempest",
        "a286bef07928f4c9c12d9ff96c7e32a7580f1691cd84b3d5db3bb9487621860d" => "Terminus",
        "d54fab65d8ba0a8c867e48cfab365d381b7f22e5db4f0853b73432227b6ba271" => "The Bio Lab",
        "f720500a2306f36330980d951ec016eaec6c33f0e7d7816323f3e1b111bde0da" => "Toxic Slums",
        "f99ea81406e87393885e793ec89aa3078154792429db49f948c7f52bfd636bd8" => "Forteresse du Crépuscule",
        "47604d075cbc4ff7eb8a716192e47bc4ece549ec1d280d79de15a06c81357cc0" => "Typhon",
        "9c512517d50954090136bd220bf7e093a39b4d2dd0c46d9e5509e03cd462f93e" => "Ulaan Deeps",
        "badc898529ca88e0ba055d4563e8739304bc9811a6841fb313456a3b164cfea9" => "War Zone",
        "666ebf589538e74ccd6f2e21dba2a80c03eff5c611e6d56ef19302c071aaa932" => "Worldship",
        "c89809141f63d58d05866ac8dad481a68cce8276264135d6a59b2c12abbea354" => "Cavernes Xel'Naga"
        );
}