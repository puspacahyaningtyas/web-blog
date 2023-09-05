<?php
// Include database.php
@include '../../../application/config/database.php';

/**
 * Parse Manually Session Data CodeIgniter to Array
 * @param String
 * @return Array
 */
if(! function_exists('sessionToArray')) {
	function sessionToArray($data) {
		if(strlen($data) == 0)
			return [];
		preg_match_all('/(^|;|\})([a-zA-Z0-9_]+)\|/i', $data, $matchesArray, PREG_OFFSET_CAPTURE);
	 	$session_data = [];
		$lastOffset = NULL;
		$currentKey = '';
		foreach($matchesArray[2] as $value) {
			$offset = $value[1];
			if(!is_null($lastOffset)) {
				$valueText = substr($data, $lastOffset, $offset - $lastOffset);
				$session_data[$currentKey] = unserialize($valueText);
			}
			$currentKey = $value[0];
			$lastOffset = $offset + strlen($currentKey) + 1;
		}
		$valueText = substr($data, $lastOffset);
		$session_data[$currentKey] = unserialize($valueText);
		return $session_data;
	}
}

$dsn = 'mysql:dbname='.$db['default']['database'].';host='.$db['default']['hostname'];
try {
	$dbh = new PDO($dsn, $db['default']['username'], $db['default']['password']);
} catch (PDOException $e) {
	echo 'Connection failed: ' . $e->getMessage();
}
// Get Cookie
$session_id = $_COOKIE['_sessions'];
$query = $dbh->prepare("SELECT data FROM `_sessions` WHERE id=:param");
$query->bindParam(':param', $session_id);
$query->execute();
$result = $query->fetch();
$session_data = sessionToArray($result['data']);
if (!in_array('is_logged_in', $session_data) || !$session_data['is_logged_in']) {
	die();
}