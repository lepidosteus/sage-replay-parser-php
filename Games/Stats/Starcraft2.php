<?php
require_once "Zend/Http/Client.php";

class Games_Stats_Starcraft2 {
    const GAME_STARCRAFT_2 = 'SC2';
    const URL_PROFILE = 'http://eu.battle.net/sc2/en/profile/%d/1/%s/';
    const URL_PROFILE_LADDER = 'http://eu.battle.net/sc2/en/profile/%d/1/%s/ladder/%s';

    public static function buildUrl($playerName, $playerId, $league = '')
    {
      if ($league == '') {
	return sprintf(self::URL_PROFILE, (int)$playerId, urlencode($playerName));
      } else {
	return sprintf(self::URL_PROFILE_LADDER, (int)$playerId, urlencode($playerName), $league);
      }
    }

    // fonctions pour identifier un joueur à partir d'un lien (+ vérification validité)

    public static function extractPlayerIds($url)
    {
        if (!preg_match('#' . str_replace(array('%d', '%s'), array('(\d+)', '([^/]+)'), self::URL_PROFILE) . '#', $url, $match)) {
            return false;
        }
        $http_client = new Zend_Http_Client();
        $http_client->setConfig(array(
                'timeout' => 30
                ));
        $http_client->setMethod(Zend_Http_Client::GET);
        $http_client->setUri(Zend_Registry::get('config')->domainUrl . 'starcraft2/players/');
        $http_client->setParameterGet(array(
                'siteKey' => '22a892495a147057682b2ade97215e184e8a9075',
                'names' => $match[1] . '.' . $match[2]
                ));
        $response = $http_client->request();
        if ($response->isError()) return false;
        if (Zend_Json::decode($response->getBody()) == null) {
            return false;
        }
        return array($match[1], $match[2]);
    }

    // fonctions pour recupérer les stats d'un joueurs à partir de ses infos

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
        $http_client->setCookie('int-SC2', '1');
        $response = $http_client->request();
        if ($response->isError()) {
            throw new Exception(
                'Http request at "'
                 . $url
                 . '" failed with error code "'
                 . $response->getStatus()
                 . '"');
        }
        return utf8_decode($response->getBody());
    }

    public function getPlayerStats($playerName, $playerId)
    {
        $url = self::buildUrl($playerName, $playerId, 'leagues');
        $rawData = self::fetchUrl($url);
        $stats = self::extractPlayerStats($playerName, $playerId, $rawData);
        return $stats;
    }

    public static $leagues = array(
        'Diamond' => 50,
        'Platinum' => 40,
        'Gold' => 30,
        'Silver' => 20,
        'Bronze' => 10
        );

    public static function extractPlayerStats($playerName, $playerId, $rawData)
    {
        preg_match_all('!<a href="/sc2/en/profile/' . $playerId . '/1/' . $playerName . '/ladder/(\d+)#current-rank"!', $rawData, $matches);
        $ladders = array();
        foreach ($matches[1] as $ladder) {
            try {
                $rawData = self::fetchUrl(self::buildUrl($playerName, $playerId, $ladder));
                if (!preg_match('!<tr id="current-rank">.+</tr>!Us', $rawData, $match)) {
                    throw new Exception('Unable to find matching rank row');
                }
                $ladder_info = array(
                    'league' => 0,
                    'league_name' => '',
                    'players' => array(),
                    'join_date' => '00/00/0000',
                    'wins' => 0,
                    'losses' => 0,
                    'points' => 0,
                    'rank' => 0,
                    'highest' => 0,
                    'previous' => 0,
                    'icon' => '',
                    'type' => ''
                    );
                if (preg_match('!<title>[^\s]*\s(.+)\s-!U', $rawData, $tmp)) {
                    if (isset(self::$leagues[ucfirst($tmp[1])])) {
                        $ladder_info['league_name'] = ucfirst($tmp[1]);
                        $ladder_info['league'] = self::$leagues[ucfirst($tmp[1])];
                    } else {
                        throw new Exception('Unknown league "' . $tmp[1] . '"');
                    }
                }
                if (preg_match('!<li class="active">\s*<a[^>]+>\s*(\dv\d) !U', $rawData, $tmp)) {
                    $ladder_info['type'] = strtolower($tmp[1]);
                }
                if (preg_match('!Joined Division: (\d+)/(\d+)/(\d+)!', $match[0], $tmp)) {
                    $ladder_info['join_date'] = sprintf('%02d', $tmp[2]) . '/' . sprintf('%02d', $tmp[1]) . '/' . $tmp[3];
                }
                if (preg_match('!<img src="/sc2/static/images/icons/ladder/([^"]+)"!', $match[0], $tmp)) {
                    $ladder_info['icon'] = $tmp[1];
                }
                if (preg_match('!<td [^>]+>(\d+)[nrdsth]{2}</td>!', $match[0], $tmp)) {
                    $ladder_info['rank'] = $tmp[1];
                }
                if (preg_match_all('!<td class="align-center">(\d+)</td>!', $match[0], $tmp)) {
                    $ladder_info['points'] = $tmp[1][0];
                    $ladder_info['wins'] = $tmp[1][1];
                    $ladder_info['losses'] = $tmp[1][2];
                }
                preg_match_all('!<div id="player-info-[^>]+>.+</div>\s*</td>!Us', $match[0], $players);
                $ladder_info['listings'] = array('players' => '', 'armies' => '');
                foreach ($players[0] as $player) {
                    if (preg_match('!<div class="tooltip-title">(.+)</div>.+Highest Rank:</strong> (.+)<br.+Previous Rank:</strong> (.+)<br.+Favorite Race:</strong> (.+)</div>!s', $player, $tmp)) {
                        $ladder_info['players'][] = array(
                            'name' => $tmp[1],
                            'race' => trim($tmp[4])
                            );
                        $ladder_info['highest'] = $tmp[2];
                        $ladder_info['previous'] = $tmp[3];
                        $ladder_info['listings']['players'] .= trim($tmp[1]).' + ';
                        $ladder_info['listings']['armies'] .= trim($tmp[4]).' + ';
                    }
                }
                $ladder_info['listings']['players'] = substr($ladder_info['listings']['players'], 0, -3);
                $ladder_info['listings']['armies'] = substr($ladder_info['listings']['armies'], 0, -3);
		// total
		$ladder_info['total'] = (int)($ladder_info['wins'] + $ladder_info['losses']);
		// win %
		$ladder_info['win%'] = ($ladder_info['wins'] * 100) / ($ladder_info['total'] > 0 ? $ladder_info['total'] : 1);
            }
            catch (Exception $e) {
            }
            if (!empty($ladder_info['type'])) {
                $ladders[] = $ladder_info;
            }
        }
        // on sort le meilleur de chaque
        $player = array('ladders' => $ladders, 'bnet_id' => $playerId, 'name' => $playerName);
        foreach (array('1v1', '2v2', '3v3', '4v4') as $type) {
            foreach ($player['ladders'] as $k => $ladder) {
                if ($ladder['type'] != $type) {
                    continue;
                }
                if (!isset($player[$type])) {
                    $player[$type] = $k;
                } else if ($player['ladders'][$player[$type]]['league'] < $ladder['league']) {
                    $player[$type] = $k;
                } else if ($player['ladders'][$player[$type]]['points'] < $ladder['points'] && $player['ladders'][$player[$type]]['league'] == $ladder['league']) {
                    $player[$type] = $k;
                }
            }
        }
        return $player;
    }
}