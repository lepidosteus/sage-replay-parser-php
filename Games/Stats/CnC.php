<?php
/**
 * @author Vianney Devreese <lepidosteus@gmail.com>
 * @link http://lepidosteus.com
 * @copyright Public domain
 * @version 1.3
 *
 * History: (correction: /  addition: +  removal: -)
 * v1.3:
 *      + New "Current" field in % WIN, contains the overall victory percentage since the
 *          last ladder reset
 * v1.2:
 *      + New $_army_list static public variable containing armies list for each game
 *      + extendPlayerStats now computes the "Fixed Streak", the streak adjusted for the
 *          last ladder reset
 *      / extractPlayerStats and extendPlayerStats now have a mandatory "gameId" parameter
 *      / Corrected extractPlayerStats's regex to extract valid data for each game and fill
 *          the resulting array accordingly
 *      / Corrected computesWinPercent to use the "field" parameter to get valid results per
 *          field instead of using overall result everytime
 *      / Corrected extendPlayerStats to calculate precise % WIN for each army, and to fill
 *          the right armies for each game
 * v1.1:
 *      + Now selecting sub-statistics for each game type (unranked, ranked 1v1, ...)
 *      + New extendPlayerStats function, computes % WIN/DC/DS per game type
 *      + New "extend" parameter to getPlayerStats, if true extendPlayerStats is called
 *          on the result set
 *      + New internal functions computesFieldPercent and computesWinPercent
 *      / Numerous unused PCRE subpatterns turned to non-capturing
 *      / Numerous PCRE subpatterns now named for easier code reading
 * v1.0:
 *      + Original release.
 *
 */

require_once "Zend/Http/Client.php";

class Games_Stats_CnC
{
    const GAME_TIBERIUM_WARS    = 'TIBWARS';
    const GAME_KANES_WRATH      = 'KANESWRATH';
    const GAME_RED_ALERT_3      = 'RA3';

    const URL_PLAYER            = 'http://portal.commandandconquer.com/portal/site/cnc/stats?persona=%s:%s&game=%s';
    const URL_CLAN              = 'http://portal.commandandconquer.com/portal/site/cnc/clan?clanKey=%s:%s&game=%s';

    static public $_army_list          = array(
        self::GAME_TIBERIUM_WARS => array(
            'NOD',
            'GDI',
            'SCRIN'
        ),
        self::GAME_KANES_WRATH => array(
            'SCRIN',
            'TRAVELER',
            'REAPER',
            'NOD',
            'MARKED',
            'BLACKHAND',
            'GDI',
            'ZOCOM',
            'TALONS'
        ),
        self::GAME_RED_ALERT_3 => array(
            'ALLIED',
            'SOVIET',
            'JAPAN'
        )
    );

    public function getPlayerStats($entityName, $gameId, $extend = true)
    {
        self::checkEntityName($entityName);
        self::checkGameId($gameId);
        $url = self::buildUrl($entityName, $gameId);
        $rawData = self::fetchUrl($url);
        self::checkRawData($rawData, $entityName);
        $stats = self::extractPlayerStats($entityName, $gameId, $rawData);
        return $extend ? self::extendPlayerStats($stats, $gameId) : $stats;
    }

    public static function checkEntityName($entityName)
    {
        if (!is_string($entityName)
            || empty($entityName)
            || strlen($entityName) < 4)
        {
            throw new Exception('Invalid player name');
        }
        return true;
    }

    public static function checkGameId($gameId)
    {
        if (!is_string($gameId)
            || !in_array($gameId, array(
                Games_Stats_CnC::GAME_TIBERIUM_WARS,
                Games_Stats_CnC::GAME_KANES_WRATH,
                Games_Stats_CnC::GAME_RED_ALERT_3
            )))
        {
            throw new Exception('Invalid game id');
        }
        return true;
    }

    public static function buildUrl($entityName, $gameId, $clan = false)
    {
        $url = ($clan ? (Games_Stats_CnC::URL_CLAN) : (Games_Stats_CnC::URL_PLAYER));
        switch ($gameId) {
            case Games_Stats_CnC::GAME_TIBERIUM_WARS:
                $gameKey = 'CC_SUB';
                break;
            case Games_Stats_CnC::GAME_KANES_WRATH:
                $gameKey = 'CC_SUB';
                break;
            case Games_Stats_CnC::GAME_RED_ALERT_3:
                $gameKey = 'CNC';
                break;
            default:
                throw new Exception('Unknwown game Id, unable to build a valid url');
        }
        $url = sprintf($url, $gameKey, urlencode($entityName), $gameId);
        return $url;
    }

    public static function fetchUrl($url)
    {
        $http_client = new Zend_Http_Client();
        $http_client->setConfig(array(
            'maxredirects' => 1,
            'timeout' => 30,
            'keepalive' => false
        ));
        $http_client->setMethod(Zend_Http_Client::GET);
        $http_client->setUri($url);
        $response = $http_client->request();
        if ($response->isError()) {
            throw new Exception(
                'Http request at "'
                .$uri
                .'" failed with error code "'
                .$response->getStatus()
                .'" ('
                .Zend_Http_Client::responseCodeAsText($response->getStatus())
                .')');
        }
        return utf8_decode($response->getBody());
    }

    public static function checkRawData($rawData, $entityName, $clan = false)
    {
        $s = ($clan ? 'Clan' : 'Commander').' '.$entityName;

        if (strpos(strtolower($rawData), strtolower($s)) === false) {
            throw new Exception('Invalid rawData, no traces of the requested entity detected');
        }
    }

    public static function extractPlayerStats($entityName, $gameId, $rawData)
    {
        $result = array();

        $safeEntityName = '';
        for ($i = 0; $i < strlen($entityName); $i++) {
            $safeEntityName .= '\x'.dechex(ord($entityName[$i]));
        }
        if (preg_match('/<a\s+href="[^"]+">(?P<NAME>'.$safeEntityName.')<\/a>/i', $rawData, $match)) {
            $result['Name'] = $match['NAME'];
        } else {
            $result['Name'] = $entityName;
        }

        if (preg_match('/ of Clan(:?\s+)?<a [^>]+>(:?\s+)?(?<CLAN>[^<]+)(:?\s+)?<\/a>/', $rawData, $match)) {
            $result['Clan'] = $match['CLAN'];
        } else {
            $result['Clan'] = '';
        }

        $fields = array(
            'Total Broadcasts',
            'Commentated',
            'Total Matches',
            'Wins',
            'Losses',
            'Win %',
            'Current Streak',
            'Longest Win Streak',
            'Total Disconnects',
            'Total DeSyncs',
            'Favorite Faction',
            'Total Match Time',
            'Avg Match Length'
        );

        foreach ($fields as $field) {
            if (preg_match('/<td>(:?\s+)?<strong>'.$field.'<\/strong>(:?\s+)?<\/td>(:?\s+)?<td class="alignright">(:?\s+)?<strong>(?P<VALUE>[^<]+)<\/strong>(:?\s+)?<\/td>/', $rawData, $match)) {
                $result[$field] = $match['VALUE'];
            } else {
                throw new Exception('Error while reading field '.$field);
            }
        }

        $fields = array(
            'Ranked 1v1 Ladder',
            'Ranked 2v2 Ladder',
            'Clan 1v1 Ladder',
            'Clan 2v2 Ladder'
        );

        foreach ($fields as $field) {
            if (preg_match('/<td>(:?\s+)?<strong>'.$field.'<\/strong>(:?\s+)?<\/td>(:?\s+)?<td class="alignleft">(:?\s+)?<strong>(?P<RESULTS>[^<]+)<\/strong>(:?\s+)?<\/td>(:?\s+)?<td class="alignright">(:?\s+)?<strong>(?P<RANK>[^<]+)<\/strong>(:?\s+)?<\/td>/', $rawData, $match)) {
                $result[$field] = array($match['RESULTS'], $match['RANK']);
            } else {
                throw new Exception('Error while reading field '.$field);
            }
        }

        $fields = array(
            'Current Streak',
            'Total Matches',
            'Career Losses',
            'Career Wins',
            'Win/Loss Ratio',
            'Average Game Length',
            'Total Time Played',
            'Career Structures Built',
            'Career Structures Lost',
            'Career Enemy Structures Destroyed',
            'Career Enemy Structures Captured',
            'Career Units Built',
            'Career Units Lost',
            'Career Enemy Units Killed',
            'Unit Kill Ratio',
            'Disconnects',
            'Desyncs'
        );

        $categories = array(
            'Unranked',
            'Ranked 1v1',
            'Ranked 2v2',
            'Clan 1v1',
            'Clan 2v2'
        );

        foreach ($fields as $field) {
            $armies = self::$_army_list[$gameId];
            $regex = '/<td>'.str_replace('/', '\/', $field).'<\/td>(:?\s+)?'
                .str_repeat(
                    '<td class="alignright">(:?\s+)?<strong>(:?\s+)?([^<\s]*)(:?\s+)?<\/strong>(:?\s+)?<\/td>(:?\s+)?',
                    count($armies)
                ).'<td class="alignright">(:?\s+)?<strong>(:?\s+)?(?P<OVERALL>[^<\s]*)(:?\s+)?<\/strong>(:?\s+)?<\/td>/';
            if (preg_match_all($regex, $rawData, $matches, PREG_SET_ORDER)) {
                foreach ($categories as $k => $category) {
                    $category_stats = array();
                    foreach ($armies as $k_a => $army) {
                        $category_stats[$army] = $matches[$k][($k_a * 6) + 4];
                    }
                    $category_stats['OVERALL'] = $matches[$k]['OVERALL'];
                    $result[$category][$field] = $category_stats;
                }
            } else {
                throw new Exception('Error while reading field '.$field);
            }
        }

        return $result;
    }

    public static function extendPlayerStats(array $playerStats, $gameId)
    {
        $categories = array(
            'Unranked',
            'Ranked 1v1',
            'Ranked 2v2',
            'Clan 1v1',
            'Clan 2v2',
        );

        foreach ($categories as $category) {
            if ($category == 'Unranked') {
                $tmp = array(
                    $playerStats[$category]['Career Wins']['OVERALL'],
                    $playerStats[$category]['Career Losses']['OVERALL']
                );
            } else {
                $tmp = explode('/', $playerStats[$category.' Ladder'][0]);
                $playerStats[$category]['Fixed Streak'] = $playerStats[$category]['Current Streak']['OVERALL'];
                if ($playerStats[$category]['Fixed Streak'][0] == '-') {
                    if ((-1 * (int)$playerStats[$category]['Fixed Streak']) > (int)$tmp[1]) {
                        $playerStats[$category]['Fixed Streak'] = (string)$tmp[1];
                    }
                } else {
                    if ((int)$playerStats[$category]['Fixed Streak'] > (int)$tmp[0]) {
                        $playerStats[$category]['Fixed Streak'] = (string)$tmp[0];
                    }
                }
            }
            $playerStats[$category]['Wins'] = $tmp[0];
            $playerStats[$category]['Losses'] = $tmp[1];
            $playerStats[$category]['Current Total Matches'] = $tmp[0] + $tmp[1];
            $armies = self::$_army_list[$gameId];
            $win_percent = array();
            foreach ($armies as $army) {
                $win_percent[$army] = self::computesWinPercent($playerStats[$category], $army);
            }
            $playerStats[$category]['% WIN'] = $win_percent;
            $playerStats[$category]['% WIN']['OVERALL'] = self::computesWinPercent($playerStats[$category], 'OVERALL');
            $playerStats[$category]['% WIN']['CURRENT'] = self::computesWinPercent($playerStats[$category], null);
            $playerStats[$category]['% DC'] = self::computesFieldPercent($playerStats[$category], 'Disconnects');
            $playerStats[$category]['% DS'] = self::computesFieldPercent($playerStats[$category], 'Desyncs');
            $tmp = $playerStats[$category]['Total Matches'];
            unset($tmp['OVERALL']);
            arsort($tmp);
            $playerStats[$category]['Favorite Faction'] = key($tmp);
        }

        return $playerStats;
    }

    public static function computesWinPercent(array $array, $field)
    {
        if (is_null($field)) {
            $win_ptr =& $array['Wins'];
            $lose_ptr =& $array['Losses'];
        } else {
            $win_ptr =& $array['Career Wins'][$field];
            $lose_ptr =& $array['Career Losses'][$field];
        }
        $totalMatches = $win_ptr + $lose_ptr;
        return (float)$totalMatches == 0 ? '0' : round(($win_ptr * 100) / $totalMatches, 1);
    }

    public static function computesFieldPercent(array $array, $field)
    {
        return (float)$array['Total Matches']['OVERALL'] == 0 ? '0' : round(($array[$field]['OVERALL'] * 100) / $array['Total Matches']['OVERALL'], 1);
    }
}