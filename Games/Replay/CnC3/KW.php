<?php
require_once 'Games/Replay/CnC3.php';

/**
 * Kane's Wrath addendum to the data array
 * Versions and armies specific
 *
 * Maps are set directly into the cnc3 array
 */
$GLOBALS['_cnc3data']['kw'] = array(
    'armies' => array(
		1 => 'Aléatoire',
		2 => 'Observateur',
		3 => 'Commentateur',
		6 => 'GDI',
		7 => 'Steel Talons',
		8 => 'ZOCOM',
		9 => 'NOD',
		10 => 'Black Hand',
		11 => 'Marked of Kane',
		12 => 'Scrin',
		13 => 'Reaper-17',
		14 => 'Traveler-59'
		),
    'short_armies' => array(
		1 => 'Rnd',
		2 => 'Obs',
		3 => 'Com',
		6 => 'GDI',
		7 => 'ST',
		8 => 'ZOCOM',
		9 => 'NOD',
		10 => 'BH',
		11 => 'MoK',
		12 => 'Scrin',
		13 => 'R-17',
		14 => 'T-59'
		),
	'versions' => array(
		'1.0.2955.37387' => '1.00',
		'1.1.2955.37387' => '1.01',
		// bug compat'
		'1.2.0.0' => '1.02',
		'1.2.0.04' => '1.02'
		)
    );

class Games_Replay_CnC3_KW extends Games_Replay_CnC3
{
    public $game_name = 'La Fureur de Kane';
    public $game_short_name = 'CNC3KW';

    /**
	 * Returns the user friendly version name
	 *
	 * @param string $full_version full version string
	 * @return user readable version name (eg: 1.05)
	 */
	function getVersion($full_version)
	{
	    if (isset($GLOBALS['_cnc3data']['kw']['versions'][$full_version])) {
	        return $GLOBALS['_cnc3data']['kw']['versions'][$full_version];
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
	    if (isset($GLOBALS['_cnc3data']['kw']['armies'][$army_id])) {
	        return $GLOBALS['_cnc3data']['kw']['armies'][$army_id];
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
	    if (isset($GLOBALS['_cnc3data']['kw']['short_armies'][$army_id])) {
	        return $GLOBALS['_cnc3data']['kw']['short_armies'][$army_id];
	    }
	    return 'Err';
	}
}
