<?php
require_once 'Games/Replay/CnC3.php';

$GLOBALS['_ra3data'] = array(
    'maps' => array(
        // 1v1
        'data/maps/official/map_mp_2_feasel3' => 'Arche secrète',
        'data/maps/official/map_mp_2_feasel4' => 'Base de combat flottante',
        'data/maps/official/map_mp_2_feasel5' => 'Chasse neige',
        'data/maps/official/map_mp_2_feasel6' => 'Force industrielle',
        'data/maps/official/map_mp_2_feasel8' => 'Ile en feu',
        'data/maps/official/map_mp_2_rao1' => 'L\'ile éternelle',
        'data/maps/official/map_mp_2_feasel2' => 'Les canaux du carnage',
        'data/maps/official/map_mp_2_feasel7' => 'Rencontre explosive',
        'data/maps/official/map_mp_2_feasel1' => 'République de cabana',
        'data/maps/official/map_mp_2_black1b' => 'Temple principal',
        // 1v1v1
        'data/maps/official/map_mp_3_feasel3' => 'La forteresse cachée',
        'data/maps/official/map_mp_3_feasel2' => 'Le cratère du chaos',
        'data/maps/official/map_mp_3_feasel4' => 'Pyroclasme',
        // 2v2
        'data/maps/official/map_mp_4_ssmith2-remix' => 'Assaut aquatique',
        'data/maps/official/map_mp_4_feasel7' => 'Cercle de feu',
        'data/maps/official/map_mp_4_feasel3' => 'Folie marine',
        'data/maps/official/map_mp_4_feasel5' => 'Grabuge naval',
        'data/maps/official/map_mp_4_feasel2' => 'La crête rocheuse',
        'data/maps/official/map_mp_4_feasel6' => 'Opposition Navale',
        'data/maps/official/map_mp_4_stewart_1' => 'Opposition virulante',
        'data/maps/official/map_mp_4_feasel1' => 'Rotonde explosive',
        'data/maps/official/map_mp_4_black_xslice' => 'Territoire hostile',
        // 1v1v1v1v1
        'data/maps/official/map_mp_5_feasel2' => 'Affrontements sur la mesa',
        'data/maps/official/map_mp_5_feasel3' => 'Circus maximum',
        // 3v3
        'data/maps/official/map_mp_6_ssmith2' => 'Carville',
        'data/maps/official/map_mp_6_feasel3' => 'Heure zéro',
        'data/maps/official/map_mp_6_feasel4' => 'Magmageddon',
        'data/maps/official/map_mp_6_feasel1' => 'Paradis perdu',
        // Cartes bonus
        // Edition collector
        'data/maps/internal/map_mp_2_ssmith1-redux' => 'Le village de la tortue',
        'data/maps/internal/map_mp_3_feasel1' => 'Mission technique',
        'data/maps/internal/map_mp_4_feasel4' => 'Dernier recours',
        'data/maps/internal/map_mp_5_feasel1' => 'Loch en danger',
        'data/maps/internal/map_mp_6_feasel2' => 'Danger marin',
        // Warhammer Online
        'data/maps/internal/map_mp_promo_feasel4' => 'Age of Wreckoning',
        // Pré-commandes
        'data/maps/internal/map_mp_promo_feasel7' => 'Port en crise',
        // Bêta testeurs
        'data/maps/internal/map_mp_promo_feasel6' => 'Tortue Noire',
        // EA Store
        'data/maps/internal/map_mp_promo_feasel5' => 'Bout du monde',
        // Game
        'data/maps/internal/map_mp_promo_feasel2' => 'Attractions fatales',
        // Gamestop
        'data/maps/internal/map_mp_promo_feasel3a' => 'Allée des Dreadnoughts',
        // Best Buy
        'data/maps/internal/map_mp_promo_feasel1' => 'Mauvaise pente',
        // EBGames
        'data/maps/internal/map_mp_promo_feasel3b' => 'Voie des Dreadnoughts'
        ),
    'armies' => array(
        1 => 'Observateur',
        2 => 'Empire',
        3 => 'Commentateur',
		4 => 'Alliés',
		7 => 'Aléatoire',
		8 => 'Soviétiques',
		),
    'short_armies' => array(
        1 => 'Obs',
		2 => 'E',
		3 => 'Com',
		4 => 'A',
		7 => 'Rnd',
		8 => 'S'
		),
	'colors' => array(
		-1 => '#000000',	// Random
		0 => '#2B2BB3',		// Navy
		1 => '#FCE953',		// Yellow
		2 => '#00A744',		// Green
		3 => '#FD7602',		// Orange
		4 => '#8301FC',		// Purple
		5 => '#D50000',		// Red
		6 => '#04DAFA'		// Cyan
		),
	'versions' => array(
	    '1.0.3174.697' => '1.00', // won't work in parser, header is x0C instead of x0E
	    '1.1.3185.21765' => '1.01',
	    '1.2.3194.30243' => '1.02',
	    '1.3.3195.25881' => '1.03',
	    '1.4.3205.30624' => '1.04',
	    '1.5.3227.15829' => '1.05',
	    '1.6.3230.17659' => '1.06',
	    '1.7.3285.27919' => '1.07',
	    '1.8.3314.30153' => '1.08',
	    '1.9.3333.26811' => '1.09',
	    '1.10.3346.29997' => '1.10',
	    '1.11.3412.20041' => '1.11',
	    '1.12.3444.25830' => '1.12'
		)
    );

class Games_Replay_RA3 extends Games_Replay_CnC3
{
    public $game_name = 'Alerte Rouge 3';
    public $game_short_name = 'RA3';

	function check_head()
	{
	    if (substr($this->buf, 0, 17) == 'RA3 REPLAY HEADER') {
			return true;
	    }
		return false;
	}

    /**
	 * Returns the user friendly version name
	 *
	 * @param string $full_version full version string
	 * @return user readable version name (eg: 1.05)
	 */
	function getVersion($full_version)
	{
	    if (isset($GLOBALS['_ra3data']['versions'][$full_version])) {
	        return $GLOBALS['_ra3data']['versions'][$full_version];
	    }
	    return 'UnknownVersion';
	}

	/**
	 * Returns the army's name from its id
	 *
	 * @param string $army_id id of the army
	 * @return army name (eg: GDI)
	 */
	function getArmyName($army_id)
	{
	    if (isset($GLOBALS['_ra3data']['armies'][$army_id])) {
	        return $GLOBALS['_ra3data']['armies'][$army_id];
	    }
	    return 'UnknownArmy';
	}


	function getTeams($omit = array(1, 3))
	{
	   return parent::getTeams($omit);
	}


	function getPlayersCount($omit = array(1, 3))
	{
	   return parent::getPlayersCount($omit);
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
	    if (isset($GLOBALS['_ra3data']['short_armies'][$army_id])) {
	        return $GLOBALS['_ra3data']['short_armies'][$army_id];
	    }
	    return 'Err';
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
				.'S=(([^:]+:){6});'		  // players
				.'.+'				  	  // garbage (contains game name in local language)
				//.'\x0E\x00\x00\x00'       // version header
				.'(\d\.\d{1,2}\.\d{4}\.\d{5})' // version value
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

	function getMapName($map_id)
	{
	    if (isset($GLOBALS['_ra3data']['maps'][$map_id])) {
	        return $GLOBALS['_ra3data']['maps'][$map_id];
	    }
	    return 'UnknownMap';
	}
}

