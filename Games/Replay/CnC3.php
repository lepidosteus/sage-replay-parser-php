<?php
require_once 'Games/Replay.php';

/**
 * CnC3 datas conversion array
 * You can get elements' name from their index here.
 *
 * <code>
 * <?php
 * $rep = new cnc3_replay();
 * $rep->parse('path/to/some/replay.cnc3replay');
 * $army_idx = $rep->r_infos['players'][0]['army'];
 * echo 'Player 1 army: '.$_cnc3data['armies'][$army_idx];
 * ?>
 * </code>
 *
 * @global array $GLOBALS['_cnc3data']
 * @name $_cnc3data
 */
$GLOBALS['_cnc3data'] = array(
	'armies' => array(
		1 => 'Aléatoire',
		2 => 'Observateur',
		3 => 'Commentateur',
		/* 4 and 5 ? */
		6 => 'GDI',
		7 => 'NOD',
		8 => 'Scrin'
		),
	'versions' => array(
		'1.2.2613.21264' => '1.02',
		'1.3.2615.35899' => '1.03',
		'1.4.2620.25554' => '1.04',
		'1.5.2674.29882' => '1.05',
		'1.6.2717.27604' => '1.06',
		'1.7.2745.30656' => '1.07',
		'1.8.2761.19784' => '1.08',
		'1.9.2801.21826' => '1.09'
		),
	'maps' => array(
		/* official maps */
		'data/maps/official/map_mp_2_simon' => 'Action fluviale',
		'data/maps/official/map_mp_2_black2' => 'Arène de tournoi',
		'data/maps/official/map_mp_2_black6' => 'Grande bataille de Black',
		'data/maps/official/map_mp_2_black5' => 'Petite ville des Etats-Unis',
		'data/maps/official/map_mp_2_black9' => 'Problèmes de pipeline',
		'data/maps/official/map_mp_2_black10' => 'Sertão mortelle',
		'data/maps/official/map_mp_2_black3' => 'Territoires désolés de Barstow',
		'data/maps/official/map_mp_2_black7' => 'Tour de tournoi',
		'data/maps/official/map_mp_2_bass1' => 'Tournoi Désert',
		'data/maps/official/map_mp_3_black1' => 'Avantage déséquilibré',
		'data/maps/official/map_mp_3_black2' => 'Triple menace',
		'data/maps/official/map_mp_4_black1' => 'Le cratère du carnage',
		'data/maps/official/map_mp_4_bass' => 'Désert périphérique',
		'data/maps/official/map_mp_4_bender' => 'La bataille pour la terre égyptienne',
		'data/maps/official/map_mp_4_rao' => 'Carnage en zone rouge',
		'data/maps/official/map_mp_4_black6' => 'Rixe tumultueuse',
		'data/maps/official/map_mp_6_hayes' => 'Six pieds sous terre',
		'data/maps/official/map_mp_6_black2' => 'Symphonie explosive',
		'data/maps/official/map_mp_8_bass' => 'Le Rocktogone',
		'data/maps/official/map_mp_8_black1' => 'Massacre limitrophe',
		/* 1.05 */
		'data/maps/official/map_mp_2_black12' => 'Schlachtfeld Stuttgart',
		'data/maps/official/map_mp_2_chuck1' => 'Tournoi de la côte',
		'data/maps/official/map_mp_2_will1' => 'Tournoi de la faille',
		'data/maps/official/map_mp_4_chuck1' => 'Chaos sur la côte',
		/* 1.09 */
		'data/maps/official/map_mp_4_chuck2' => 'Wrecktropolis',
		/* semi-official maps (from EB/BestBuy pre-orders */
		'data/maps/internal/map_mp_2_black11' => 'Les armes fatales',
		'data/maps/internal/map_mp_4_black5' => 'Vallée de la mort',
		'data/maps/internal/map_mp_6_black1' => 'Méga-bataille de Black',
		/* Kane's Wrath converted */
		'data/maps/official/map_mp_2_black11' => 'Les armes fatales',
		'data/maps/official/map_mp_4_black5' => 'Vallée de la mort',
		'data/maps/official/map_mp_6_black1' => 'Méga-bataille de Black',
		/* Kane's Wrath */
        'data/maps/official/bamap_dc05_2' => 'Décision guerrière',
        'data/maps/official/map_mp_2_black4' => 'Point Zéro',
        'data/maps/official/map_mp_2_black1' => 'Tournoi Redux dans le désert',
        'data/maps/official/map_mp_2_black8' => 'Vallée du Tibre',
        'data/maps/official/bamap_dc05_3' => 'Décision partagée',
        'data/maps/official/bamap_kk03_3' => 'Le triangle de la toundra',
        'data/maps/official/bamap_ew09_03' => 'Massacre suburbain',
        'data/maps/official/bamap_dc08_4' => 'Conflit croisé',
        'data/maps/official/bamap_ew07_04' => 'Dégradation urbaine',
        'data/maps/official/bamap_ew06_04' => 'Déluge d\'artillerie',
        'data/maps/official/bamap_dc03_3' => 'Désolation',
        'data/maps/official/bamap_jf01_4' => 'Dévastation sur les docks',
        'data/maps/official/bamap_rh01_4' => 'Enfer et paradis',
        'data/maps/official/map_mp_4_ssmith_01' => 'Fracas frontalier',
        'data/maps/official/bamap_aw01_04' => 'Grabuge au port',
        'data/maps/official/bamap_ew01_4' => 'Investissements douteux',
        'data/maps/official/bamap_dc04_3' => 'La fin des haricots',
        'data/maps/official/bamap_ew05_04' => 'La sécurité en question',
        'data/maps/official/bamap_dc11_4' => 'La ville-empire',
        'data/maps/official/bamap_dc01_4' => 'Le petit conflit dans la prairie',
        'data/maps/official/bamap_dc07_4' => 'Les montagnes de la folie',
        'data/maps/official/bamap_dc10_4' => 'L\'allée des meurtriers',
        'data/maps/official/bamap_sb01_4' => 'Opportunité perdue',
        'data/maps/official/bamap_ew08_04' => 'Promesses orientales',
        'data/maps/official/bamap_dc06_4' => 'Terres arides',
        'data/maps/official/bamap_ew03_04' => 'Terreur sur l\'oasis',
        'data/maps/official/bamap_dc02_4' => 'Territoires abandonnés',
        'data/maps/official/map_mp_5_black1' => 'Les jardins de tibérium III',
        'data/maps/official/bamap_ew11_05' => 'L\'isthme de la folie',
        'data/maps/official/bamap_ew10_06' => 'Désert de tibérium',
        'data/maps/official/bamap_jf03_6' => 'En eaux troubles',
        /* Kane's Wrath bonus maps */
        'data/maps/internal/map_mp_2_black2_redzoned' => 'Arène en ruine',
        'data/maps/internal/map_mp_2_black7_redzoned' => 'Menace sur la tour',
        'data/maps/internal/map_mp_2_simon_b' => 'Rivière de l\'oubli',
        'data/maps/internal/eamap_sb05_4' => 'Les cratères de Camden',
		/* unofficial maps */
		'data/maps/internal/fallen_empire_classic' => 'Empire Déchu classique',
		'data/maps/internal/micro_wars_v1.1' => 'Micro Wars v1.1',
		'data/maps/internal/micro_wars_team_v1.1' => 'Micro Wars Teams v1.1',
		'data/maps/internal/dangerous crossing by 30-nr gate' => 'Dangerous Crossing',
		'data/maps/internal/au 4 coins du globe by 30-nr_gate' => 'Aux quatre coins du globe',
		'data/maps/internal/downbeat' => 'Downbeat',
		/* lda tournament */
		'data/maps/internal/[lda-domination]fortification' => 'Tournoi LDA Fortification',
		'data/maps/internal/[lda-domination]avant-poste' => 'Tournoi LDA Avant Poste',
		'data/maps/internal/[lda-domination]standard' => 'Tournoi LDA Standard',
		'data/maps/internal/[lda-domination]base' => 'Tournoi LDA Base'
		),
	'colors' => array(
		-1 => '#000000',	// Random
		0 => '#2B2BB3',		// Navy
		1 => '#FCE953',		// Yellow
		2 => '#00A744',		// Green
		3 => '#FD7602',		// Orange
		4 => '#FB7FD3',		// Pink
		5 => '#8301FC',		// Purple
		6 => '#D50000',		// Red
		7 => '#04DAFA'		// Cyan
		),
	'ia_name' => array(
		'CE' => 'IA Facile',
		'CM' => 'IA Moyenne',
		'CH' => 'IA Difficile',
		'CB' => 'IA Brutale'
		),
	'ia_mode' => array(
		-2 => 'Aléatoire',
		/* -1 ? */
		0 => 'Equilibrée',
		1 => 'Attaque rapide',
		2 => 'Développement tranquille',
		3 => 'Guerilla',
		4 => 'Rouleau compresseur'
		)
	);

/**
 * CNC 3 Tiberium Wars class.
 *
 * @package Replay parsing
 * @subpackage CnC3 Replay
 * @version 1.4
 */
class Games_Replay_CnC3 extends Replay
{
    public $game_name = 'Command and Conquer 3';
    public $game_short_name = 'CnC3';

	/**
	 * Check the file's head against CnC3 header.
	 * There are all kinds of symbols everywhere to check for replay validity
	 * and even a footer, but I honestly think we can limit ourself to the
	 * header. If a file has a valid header but isn't a cnc3 replay it will
	 * fail at regex check anyway.
	 *
	 * @return bool false if any error occurs, true otherwise
	 */
	function check_head()
	{
		if (substr($this->buf, 0, 18) == 'C&C3 REPLAY HEADER')
			return true;
		return false;
	}

	/**
	 * Fetch the data about the replay from the buffer to the $r_info array.
	 * Kind of simple: we check if the buffer contains the data fields,
	 * if yes we parse it.
	 *
	 * @return bool false if any error occurs, true otherwise
	 */
	function fetch_infos()
	{
		$regex = '#M=([^;]+);'            // map
				.'MC=([0-9A-Z]+);'		  // map crc ?
				.'MS=([0-9]+);'			  // ?
				.'SD=(-?[0-9]+);'         // Seed, could be used to resolve random to army/color/...
				.'GSID=([0-9A-Z]+);'	  // gsid = battlecast id ?
				.'GT=(-?[0-9]+);'		  // ?
				.'PC=(-?[0-9]+);'		  // post commentator (-1 when details added) ?
				.'RU=([0-9 -]+);'		  // options
				.'S=(([^:]+:){8});'		  // players
				.'.+'				  	  // garbage (contains game name in local language)
				.'(\d\.\d\.\d{1,5}\.\d{2,5})' // version value
        		.'#Us';                   // regex options
		if (preg_match($regex, $this->buf, $matches) == 0)
			return false;
		$this->init_infos();
		if (!$this->read_players($matches))
			return false;
		if (!$this->read_map($matches))
			return false;
		if (!$this->read_misc($matches))
			return false;
		if (!$this->read_options($matches))
			return false;
		return true;
	}

	/**
	 * Read various replay informations.
	 * As for now: Length, gsid, version, commented.
	 *
	 * @param array &$matches Matches array from the regex matching
	 * @return bool false if any error occurs, true otherwise
	 */
	function read_misc(&$matches)
	{
		$this->r_infos['misc'] = array(
			'gsid' => $matches[5],		// gsid = battlecast id ?
			'version' => $matches[11],
			'commented' => ($matches[7] == '-1' ? true : false), // unsure -- need check
			/**
			 * Length computing is not-that-perfect: this value could be
			 * entirely wrong. The only way to get a valid amount would be by
			 * parsing the whole file's blocks (but it represent an estimated
			 * amount of seconds elapsed during the game)
			 */
			'length' => round((($this->size / 1024) / (0.18 * count($this->r_infos['players'])))
							  - (($this->size / 1536) - (104 * count($this->r_infos['players']))))
			);
		if ($this->r_infos['misc']['commented']) {
			/**
			 * Length couldn't really be trusted, this one is even worse :)
			 * Unsure about wether commentator detection is valid in the first
			 * place, then there is no way to estimate how much voice and
			 * signals are stored in the replay so we roughly do an estimation.
			 */
			$this->r_infos['misc']['length']
				= round($this->r_infos['misc']['length'] / 2.20);
		}
		return true;
	}

	/**
	 * Read the game options
	 *
	 * @param array &$matches Matches array from the regex matching
	 * @return bool false if any error occurs, true otherwise
	 */
	function read_options(&$matches)
	{
		$o = explode(' ', $matches[8]);
		/**
		 * There are plenty of things here (every options of multiplayer games
		 * are stored here), but we only extract some usefull information.
		 * If you need more, look at what is stored into $o.
		 */
		$this->r_infos['options'] = array(
			'speed' => $o[1], // %
			'money' => $o[2], // $
			'delay' => $o[5], // minutes -- battlecast delay
			'crates' => ($o[6] == 1 ? true : false)
			);
		return true;
	}

	/**
	 * Read the map internal path
	 *
	 * @param array &$matches Matches array from the regex matching
	 * @return bool false if any error occurs, true otherwise
	 */
	function read_map(&$matches)
	{
		$this->r_infos['map'] = array(
			'fname' => substr($matches[1], 3)
			);
		return true;
	}

	/**
	 * Converting IP (so-called "uid" ...) from hex to dec
	 *
	 * @param string $uid A player's UID
	 * @return string Player's IPv4
	 */
	function uid2ip($uid)
	{
		$ip = '';
		for ($i = 0; $i < 4; $i++)
			$ip .= hexdec(substr($uid, $i * 2, 2)).'.';
		return substr($ip, 0, -1);
	}

	/**
	 * Fetching one player data into it's row
	 *
	 * @param array &$player One row of the players' array
	 */
	function read_player(&$player)
	{
		$p = explode(',', $player);
		switch ($p[0]{0}) {
			case 'H': 	// human
				if ($p[0] == 'Hpost Commentator') // hard coded ?
					return;
				$this->r_infos['players'][] = array(
					'clan' => $p[11],
					'army' => $p[5],
					'color' => $p[4],
					'position' => $p[6],			// N/A => 0, else pos number
					'handicap' => $p[8],			// %, afaik only negatives or 0 can be found
					'human' => true,
					'team' => $p[7] + 1, 			// N/A => 0, else team number
					'uid' => $p[1],					// Player's IP in hex base
					'ip' => $this->uid2ip($p[1]),
					'name' => substr($p[0], 1)		// Remove H at start
					);
				break;
			case 'C':	// computer
				$this->r_infos['players'][] = array(
					'army' => $p[2],
					'color' => $p[1],
					'handicap' => $p[5],
					'human' => false,
					'team' => $p[4] + 1,
					'fname' => $p[0],
					'name' => $this->getIAName($p[0]),
					'ia_mode' => $p[6],
					'position' => $p[3]
					);
				break;
			case 'X': return; break;  // empty slot written as X
		}
	}

	/**
	 * Read through the whole players' data and extract one player's
	 * informations at a time
	 *
	 * @param array &$matches Matches array from the regex matching
	 * @return bool false if any error occurs, true otherwise
	 */
	function read_players(&$matches)
	{
		$p = explode(':', $matches[9]);
		unset($p[count($p) - 1]); // explode produces dummy at end
		foreach ($p as $player)
			$this->read_player($player);
		if (!count($this->r_infos['players']))
			return false;
		return true;
	}

	/**
	 * Count the number of players in the game who's army is not in the omit
	 * array. This is usefull to know the number of real player into a game,
	 * ommiting spectators and such.
	 *
	 * @param array $omit List of army id to omit
	 * @return int Number of players in the game with an army not listed in $omit
	 */
	function getPlayersCount($omit = array(2, 3))
	{
	    if (!$this->isParsed()) {
	        return 0;
	    }
        if (count($teams = $this->getTeams()) == 0) {
            return 0;
        }
        $count = 0;
        foreach ($teams as $team) {
            foreach ($team as $player) {
                if (!in_array($player['army'], $omit)) {
                    $count++;
                }
            }
        }
        return $count;
	}

	/*
	 * Returns the matchup type using the constants.
	 * We look into the team/players listing and compare with known case.
	 *
	 * @return int one of the $GLOBALS['_replay_matchup'] values
	 */
	function getMatchup()
	{
	    if (!$this->isParsed()) {
	        return $GLOBALS['_replay_matchup']['other'];
	    }
        if (!isset($this->_cache['matchup'])) {
            do {
                $matchup = $GLOBALS['_replay_matchup']['other'];
                if (count($teams = $this->getTeams()) == 0) {
                    break; // there's obviously an error here, but die silently
                }
                if (($pcount = $this->getPlayersCount()) == 0) {
                    break; // there's obviously an error here, but die silently
                }
                $tcount = count($teams);
                // treat 1v1 first, else they will collide later
                if ($pcount == 2
                    && ($tcount == 2          // either there is two team
                        || ($tcount == 1      // or only one team but being the
                            && isset($teams[0])))) {// no-teamed one
                    $matchup = $GLOBALS['_replay_matchup']['1v1'];
                    break;
                }
                // if more than two teams, we have an irregular matchup
                if ($tcount != 2 || isset($teams[0])) {
                    if (($tcount == 1 && isset($teams[0]))) {
                        $matchup = $GLOBALS['_replay_matchup']['ffa'];
                    } else {
                        $matchup = $GLOBALS['_replay_matchup']['other'];
                    }
                    break;
                }
                $nb = 0;
                foreach ($teams as $team) {
                    if ($nb == 0) {
                        $nb = count($team);
                    // players counts are not egal in the two teams, irregular matchup
                    } else if ($nb != count($team)) {
                        $matchup = $GLOBALS['_replay_matchup']['other'];
                        break;
                    }
                }
                switch ($nb) {
                    case 1: $matchup = $GLOBALS['_replay_matchup']['1v1']; break;
                    case 2: $matchup = $GLOBALS['_replay_matchup']['2v2']; break;
                    case 3: $matchup = $GLOBALS['_replay_matchup']['3v3']; break;
                    case 4: $matchup = $GLOBALS['_replay_matchup']['4v4']; break;
                    default: $matchup = $GLOBALS['_replay_matchup']['other']; break;
                }
            } while (false); // we're not looping, just abusing do/while breaking
            $this->_cache['matchup'] = $matchup;
        }
        return $this->_cache['matchup'];
	}

	/**
	 * Returns the teams listing array.
	 * It is also used to fill the cache for teams informations.
	 * We read the player list, store them in the team index (0 for non teamed
	 * players) and returns it. Non playing players are not stored.
	 *
	 * @return array listing of the teams (subarray containg players)
	 */
	function getTeams($omit = array(2, 3))
	{
	    if (!$this->isParsed()) {
	        return array();
	    }
        if (!isset($this->_cache['teams'])) {
            $teams = array();
            do {
                if (!isset($this->r_infos['players'])) {
                    break; // probably not parsed or parsing failed
                }
                foreach ($this->r_infos['players'] as $player) {
                    if (in_array($player['army'], $omit)) {
                        continue; // observator or commentator, do not store
                    }
                    if (!isset($teams[$player['team']])
                        || !is_array($teams[$player['team']])) {
                        $teams[$player['team']] = array();
                    }
                    array_push($teams[$player['team']], $player);
                }
            } while (false); // we're not looping, just abusing do/while breaking
            $this->_cache['teams'] = $teams;
        }
        return $this->_cache['teams'];
	}

	/**
	 * Check if the game is a clan game or not.
	 * Clan games are defined as regular matchup (X vs X) where every member of
	 * the same team are from the same clan.
	 *
	 * @return boolean true if this is a clan fame, false otherwise
	 */
	function isClanGame()
	{
	    if (!$this->isParsed()) {
	        return false;
	    }
	    $matchup = $this->getMatchup();
        if (!($matchup >= $GLOBALS['_replay_matchup']['1v1']
                && $matchup <= $GLOBALS['_replay_matchup']['4v4'])) {
            return false;
        }
        if (!count($teams = $this->getTeams())) {
            return false;
        }
        foreach ($teams as $tkey => $team) {
            $clan = NULL;
            foreach ($team as $player) {
                if (!$player['human']) {
                    return false; // if there is an ia, not a clan game
                }
                if ($tkey == 0) {
                    if (empty($player['clan'])) {
                        return false;
                    }
                    $clan = $player['clan'];
                } else {
                    if (is_null($clan)) {
                        $clan = $player['clan'];
                    }
                    if ($player['clan'] != $clan) {
                        return false; // players from != clans in the same team, not a clan game
                    }
                }
            }
            if (empty($clan) || is_null($clan)) {
                return false; // no clan or error, not a clan game
            }
        }
        return true;
	}

	function getMapName($map_id)
	{
	    if (isset($GLOBALS['_cnc3data']['maps'][$map_id])) {
	        return $GLOBALS['_cnc3data']['maps'][$map_id];
	    }
	    return 'UnknownMap';
	}

	/**
	 * This function should be private, it is called by FrmtStr whenever a tag is
	 * encountered, and does the replacement with the tag's value.
	 *
	 * @private
	 * @param array $m preg_match matches array
	 * @return string the replacement string for the tag
	 */
	function _subFrmtStr($m)
	{
	    $stags = array_slice(explode('_', substr($m[0], 1, -1)), 1);
	    $subtags = array('army', 'fullarmy', 'clan');
	    foreach ($subtags as $subtag) {
    	    $stname = 's'.$subtag;
    	    // we are injecting the variable into the current namefield
    	    // ie this will create a $sclan variable into the fonction namespace
    	    if (in_array($subtag, $stags)) {
    	        $$stname = true;
    	    } else {
    	        $$stname = false;
    	    }
	    }
	    switch ($m[1]) {
	        case 'map':
	            $s = $this->getMapName($this->r_infos['map']['fname']);
	            break;
	        case 'version':
	            $s = $this->getVersion($this->r_infos['misc']['version']);
	            break;
	        case 'players':
                $s = $this->getPlayersList($sclan, $sarmy, $sfullarmy);
	            break;
	        case 'clans':
                $s = $this->getClansList($sarmy, $sfullarmy);
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

	/**
	 * Returns the user friendly version name
	 *
	 * @param string $full_version full version string
	 * @return user readable version name (eg: 1.05)
	 */
	function getVersion($full_version)
	{
	    if (isset($GLOBALS['_cnc3data']['versions'][$full_version])) {
	        return $GLOBALS['_cnc3data']['versions'][$full_version];
	    }
	    return 'UnknownVersion';
	}

	/**
	 * This function takes a string and replaces known tags in it by their values.
	 * Tags are written as: %tag%
	 * Subtags are optionnal, and can be stacked, eg: %tag_subtag%, %tag_subtag_subtag%
	 * Supported tags are:
	 *     map (no subtags):
	 *         the map's name
	 *     version (no subtags):
	 *         the version number
	 *     matchup (no subtags):
	 *         the matchup of this game (eg: 2v2, 1v3v2, ...)
	 *     clans (army):
	 *         list of the clans playing in this game, in the form
	 *         clan1 + clan2 vs clan3 vs clan4 ...
	 *         IA are set as 'IA', players without a clan as 'Autres'.
	 *         Spectators/commentators are NOT counted.
	 *        - If fullarmy subtag is set, armies in the team are written next to the
	 *         clan listing, eg:
	 *         clan1 + clan2 (nod + gdi) vs clan3 (nod + scrin) vs clan4 (gdi)
	 *        - If army subtag is set, armies in the team are written next to the
	 *         clan listing, using their short names eg:
	 *         clan1 + clan2 (mok + gdi) vs clan3 (nod + t-59) vs clan4 (gdi)
	 *     players (army, clan):
	 *         list of the players in this game, in the form
	 *         player1 + player2 vs player3 vs player4 ...
	 *         Spectators/commentators are NOT counted.
	 *        - If fullarmy subtag is set, the army of the player is written next to
	 *         his name, eg:
	 *         player1 (nod) + player2 (gdi) vs player3 (scrin) vs player4 (gdi)
	 *        - If army subtag is set, the army of the player is written next to
	 *         his name, using it's short name eg:
	 *         player1 (nod) + player2 (mok) vs player3 (t-59) vs player4 (gdi)
	 *        - If clan subtag is set, the clan of the player is written next to
	 *         his name, eg:
	 *         [clan1]player1 + player2 vs player3 vs [clan2]player4
	 * Exemple of a format string:
	 *     "%version% - %matchup% on %map%, involving %players_army_clan%"
	 * WARNING: map/players/clans's names may contain html entities. Please clean
	 *     it up before displaying for obvious security concerns (you can use
	 *     htmlentities in exemple).
	 *
	 * @param string $frmt the format string
	 * @return string the resulting formatted string
	 */
	function FrmtStr($frmt)
	{
	    if (!$this->isParsed()) {
	        return $frmt;
	    }
	    $tags = array('map', 'version', 'players', 'clans', 'matchup');
	    $subtags = array('army', 'fullarmy', 'clan');
	    // this will match invalid sub tags, but they will not be treated afterward
	    // eg: %map_army_clan% will be treated like %map%
	    return preg_replace_callback(
	       '/%('.implode('|', $tags).')(_('.implode('|', $subtags).')){0,2}%/',
	       array($this, '_subFrmtStr'),
	       $frmt);
	}

	/**
	 * Returns a string with the list of players involved in the game.
	 * See FrmtStr %players% tag for more informations.
	 *
	 * @param boolean $clan see clan subtags informations
	 * @param boolean $army see army subtag informations
	 * @return string list of players
	 */
	function getPlayersList($clan = false, $army = false, $fullarmy = false)
	{
	    if (!$this->isParsed()) {
	        return '';
	    }
        $teams = $this->getTeams();
        $s = '';
        foreach ($teams as $tnum => $team) {
            $ss = '';
            foreach ($team as $player) {
                if (!empty($ss)) {
                    $ss .= ' '.($tnum == 0 ? 'vs' : '+').' ';
                }
                if ($player['human'] && $clan && !empty($player['clan'])) {
                    $ss .= '['.$player['clan'].']';
                }
                $ss .= $player['name'];
                if ($fullarmy) {
                    $ss .= ' ('.($this->getArmyName($player['army'])).')';
                } else if ($army) {
                    $ss .= ' ('.($this->getShortArmyName($player['army'])).')';
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

	/**
	 * Returns the army's name from its id
	 *
	 * @param string $army_id id of the army
	 * @return army name (eg: GDI)
	 */
	function getArmyName($army_id)
	{
	    if (isset($GLOBALS['_cnc3data']['armies'][$army_id])) {
	        return $GLOBALS['_cnc3data']['armies'][$army_id];
	    }
	    return 'UnknownArmy';
	}

	/**
	 * Returns the army's short name from its id
	 *
	 * @param string $army_id id of the army
	 * @return army's short name (eg: MoK)
	 */
	function getShortArmyName($army_id)
	{
	    return $this->getArmyName($army_id);
	}

	/**
	 * Get a complete IA's name from it's short name and mode.
	 *
	 * @param string $fname the short name for this IA (given is ['fname'] in the players array)
	 * @param null|int $mode IA's mode, if null given it won't be written
	 * @return string the full IA's name
	 */
	function getIAName($fname, $mode = null)
	{
	    $s = $GLOBALS['_cnc3data']['ia_name'][$fname];
	    if (!is_null($mode)) {
	        $s .= ' '.$GLOBALS['_cnc3data']['ia_mode'][$mode];
	    }
        return $s;
	}

	/**
	 * Returns a string with the matchup of the game.
	 * See FrmtStr %matchup% tag for more informations.
	 *
	 * @return string the matchup of this game
	 */
	function getMatchupStr()
	{
	    if (!$this->isParsed()) {
	        return '';
	    }
	    // runtime compilation slow down php, but I'm being lazy
	    $display = create_function(
	       '$pcount, &$s',
	       '$s .= (empty($s) ? $pcount : "vs".$pcount);'
	       );
        $teams = $this->getTeams();
        $s = '';
        foreach ($teams as $tnum => $team) {
            $pcount = 0;
            foreach ($team as $player) {
                if ($tnum == 0) {
                    $display(1, $s);
                } else {
                    $pcount++;
                }
            }
            if ($pcount == 0) {
                continue;
            }
            $display($pcount, $s);
        }
        return $s;
	}

	/**
	 * Returns a string with the list of clans involved in the game.
	 * See FrmtStr %clans% tag for more informations.
	 *
	 * @param boolean $army see army subtag informations
	 * @return string list of clans
	 */
	function getClansList($army = false, $fullarmy = false)
	{
	    if (!$this->isParsed()) {
	        return '';
	    }
	    // runtime compilation slow down php, but I'm being lazy
	    $display = create_function(
	       '$clans, &$s, $armies, $army, $fullarmy, $replay_object',
	       '$clans = (array)$clans;
	       $armies = (array)$armies;
	       if (count($clans)) {
                if (!empty($s)) {
                    $s .= " vs ";
                }
                $ss = "";
                foreach ($clans as $clan) {
                    if (!empty($ss)) {
                        $ss .= " + ";
                    }
                    if ($clan == -1) {
                        $ss .= "IA";
                    } else if ($clan == -2) {
                        $ss .= "Autre";
                    } else {
                        $ss .= "[".$clan."]";
                    }
                }
                $s .= $ss;
                if ($army || $fullarmy) {
                    $ss = "";
                    foreach ($armies as $carmy) {
                        if (!empty($ss)) {
                            $ss .= " + ";
                        }
                        if ($fullarmy) {
                            $ss .= $replay_object->getArmyName($carmy);
                        } else {
                            $ss .= $replay_object->getShortArmyName($carmy);
                        }
                    }
                    $s .= " (".$ss.")";
                }
            };'
            );
        $teams = $this->getTeams();
        $s = '';
        foreach ($teams as $tnum => $team) {
            $clans = array();
            $sarmies = array();
            foreach ($team as $player) {
                if ($player['human']) {
                    if (!empty($player['clan'])) {
                        $add = $player['clan'];
                    } else {
                        $add = -2;
                    }
                } else {
                    $add = -1;
                }
                if ($tnum == 0) {
                    $display($add, $s, $player['army'], $army, $fullarmy, $this);
                } else {
                    if (!in_array($add, $clans)) {
                        $clans[] = $add;
                    }
                    $sarmies[] = $player['army'];
                }
            }
            $display($clans, $s, $sarmies, $army, $fullarmy, $this);
        }
        return $s;
	}
}

//back compat
class Games_Replay_Sage_CnC3 extends Games_Replay_CnC3 {}
class cnc3_replay extends Games_Replay_Sage_CnC3 {}