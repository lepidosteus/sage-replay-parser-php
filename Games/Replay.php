<?php
/**
 * @author Vianney Devreese <lepidosteus@gmail.com>
 * @link http://lepidosteus.com
 * @copyright Do whatever you want with this.
 * @version 1.4
 * @package Replay parsing
 *
 * This file contains two class, 'replay' is a base class you should inherit
 * from when doing a game's class, and 'cnc3_replay' is a class ready to parse
 * Command and Conquer 3 replays.
 * Every implementation fill it's internal $r_infos array, external code then
 * reads it to get the details.
 * In addition there is an array named $_cnc3data set in the global scope
 * containing the translation data from $r_infos to human language (in french).
 * Both classes are php4 compatibles, which explains the lack of private
 * members, abstract class/function and exception thrown.
 *
 * Usage example:
 * <?php
 * $rep = new cnc3_replay();
 * if (!$rep->parse('path/to/some/replay.cnc3replay'))
 *     die('Unable to parse file, are you sure it is a valid cnc3 replay ?');
 * echo $rep->FrmtStr('%version% - %map% - %matchup% - %players_army_clan%');
 * ?>
 *
 * History: (correction: /  addition: +  removal: -)
 * v1.4:
 *      + Added getTeams, getMatchup, getPlayersCount, isClanGame, isParsed,
 *          getClansList, getPlayersList, getMatchupStr, getIAName
 *          _subFrmtStr and FrmtStr methods
 *      / Corrected the map regex again, less strict detection, allows for
 *          somewhat invalid map names (like LDA's ones)
 *      + Added a set of constants for matchups
 *      / Corrected the global regex, removed post version footer since it does
 *          not always appear in 1.05 replays
 *      / Name field is now filled for IAs
 * v1.3:
 *      + Added some unofficial map's names
 *      + Added 1.05 support (version and map, regex correction)
 * v1.2:
 *      / Corrected a possible bug in the regex match where \n in the garbage
 *			section before the replay version would cause the parsing to fail.
 *			(faulting replay provided by Noize)
 *      + Modified the doc with more information
 * v1.1:
 *      / Corrected a possible bug where map identification fails due to internal
 *			map number miswriten by the game (map numbers are not used anymore,
 *			which may create collision bug later).
 *			(faulting replay provided by Noize)
 *      / Corrected a map name inversion between 'Le cratère du carnage' and
 *			'Carnage en zone rouge'.
 *      + Added PhpDocumentor syntax.
 * v1.0:
 *      + Original release.
 *
 */

$i = 0;
$GLOBALS['_replay_matchup'] = array(
    '1v1' => 1,
    '2v2' => 2,
    '3v3' => 3,
    '4v4' => 4,
    'ffa' => 5,
    'other' => 6
);

/**
 * Base class.
 * Implements buffer reading and base interface, childs should do what's left.
 * Do not instantiate it, should be abstract, php4 doesn't allow it.
 *
 * @abstract
 * @package Replay parsing
 * @version 1.2
 */
class replay
{
	/**
	 * Internal buffer, false or up to 1 kb of binary data.
	 * This size is subject to change depending on the value submitted
	 * to the read_head method.
	 *
	 * @var string|bool
     * @access protected
	 */
	var $buf = false;
	/**
	 * Size of file being read in bytes.
	 * Storing here since path is not transmitted anymore to fetch_infos()
	 *
	 * @var int
     * @access protected
	 */
	var $size = 0;
	/**
	 * Array containing the replay informations after parsing.
	 * You fill this, external code reads it.
	 * Only valid if the parse() method returned true.
	 *
	 * @var array
     * @access public
	 */
	var $r_infos = array();

	/**
	 * Internal object array containing its own cache for queries.
	 * Do not touch this out of the object scope.
	 *
	 * @var array
	 */
	var $_cache = array();

	/**
	 * Ping back the return value after cleaning the buffer.
	 * This method is used to make sure the buffer is cleaned whatever happens.
	 *
	 * @param bool $status Return value to ping back
	 * @return bool $status
	 */
	function full_return($status = false)
	{
		$this->buf = false;
		$this->size = 0;
		return $status;
	}

	/**
	 * Read the file top $size bytes into the buffer for later parsing.
	 * Read the first $size bytes of the file $path into the class buffer
	 * Does not read using an incremential buffer, we only grab the first X
	 * bytes from the file and hope we find the header there.
	 * Okay for all Sage engine game, but you should probably change that for
	 * other games.
	 *
	 * @param string $path Path to the file (must be php readable)
	 * @param int $size Quantity of data to read, in bytes. Defaulting to 1536.
	 * @return bool false if any error occurs, true otherwise
	 */
	function read_head($path, $size = 1536)
	{
		if (!($handle = @fopen($path, "r"))) // (supress warning since most php users display errors ...)
			return false;
		$this->buf = fread($handle, $size); // 1536 should be enough
		fclose($handle);
		if (!$this->buf)
			return false;
		$this->size = filesize($path);
		return true;
	}

	/**
	 * Check if file header is valid.
	 * Dummy, childs implements it (returning false here for poor abstract
	 * class alternative).
	 *
	 * @abstract
	 * @return bool false
	 */
	function check_head()
	{
		return false;
	}

	/**
	 * Initialize the $r_infos array.
	 * Not used here, given for convenience of child classes.
	 * Feel free to override.
	 */
	function init_infos()
	{
		$this->r_infos = array(
			'players' => array(),
			'map' => array(),
			'options' => array(),
			'misc' => array()
			);
	}

	/**
	 * Fill $r_infos with the data in buf.
	 * Dummy, childs implements it (returning false here for poor abstract
	 * class alternative).
	 *
	 * @abstract
	 * @return bool false
	 */
	function fetch_infos()
	{
		return false;
	}

	/**
	 * Checks whether or not this object contains informations about a parsed
	 * replay.
	 *
	 * @return boolean true if a replay has been parsed, false otherwise
	 */
	function isParsed()
	{
	    if (!isset($this->_cache['parsed']) || !$this->_cache['parsed']) {
	        return false;
	    }
	    return true;
	}

	/**
	 * Parsing file given as argument, filling $r_infos with values.
	 * Will return false if any error is encountered, true otherwise. If true
	 * is returned, the replay details can be read in $r_infos.
	 *
	 * @param string $path
	 * @return bool true if success, false otherwise
	 */
	function parse($path)
	{
	    $this->_cache = array();
		if (!$this->read_head($path))
			return $this->full_return();
		if (!$this->check_head())
			return $this->full_return();
		if (!$this->fetch_infos())
			return $this->full_return();
	    $this->_cache['parsed'] = true;
		return $this->full_return(true);
	}

	/**
	 * Returns the matchup type using the constants.
	 * Dummy, childs implements it (returning -1 here for poor abstract
	 * class alternative).
	 *
	 * @abstract
	 * @return int -1
	 */
	function getMatchup()
	{
        return -1;
	}

	/**
	 * Returns the teams listing array.
	 * It is also used to fill the cache for teams informations.
	 *
	 * @abstract
	 * @return int -1
	 */
	function getTeams()
	{
        return -1;
	}
}
?>