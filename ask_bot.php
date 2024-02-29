<?php

include('config.php');
include('stop_words.php');

if (!(isset($_SESSION['user']))) {
	header('HTTP/1.1 401 Authorization Required', true, 401);
	return;
}

$userid = $_SESSION['user']['id'];

$q = mysqli_real_escape_string($con, $_POST['q']);

$messages = array();

mysqli_query($con, "INSERT INTO history (userid, message, source) VALUES ($userid, '$q', 'user')");

$query = preg_replace('/[^A-Za-z0-9 ]/', '', $q); //Remove Special Chars
$query = trim(strtolower($query)); //Trims and lower case

$input = remove_stopwords($StopWords, $query); //Remove Stopwords to for the NL Search

//Ref: https://www.w3resource.com/mysql/mysql-full-text-search-functions.php

//First, check if the question has direct reply which does not have a content. Eg. Hello, Hi etc.
//The LIKE query is used since words with less than three characters will be skipped by MATCH
$sql = "SELECT id, title, hascontent, 0 AS 'confidence' FROM (
	SELECT id, title, isdeleted, TRIM(content) <> '' As hascontent
		FROM articles WHERE LOWER(keywords) LIKE '%$query%' UNION
	SELECT id, title, isdeleted, TRIM(content) <> '' As hascontent
		FROM articles WHERE MATCH(keywords) AGAINST('$input' IN NATURAL LANGUAGE MODE)
	) matches WHERE matches.isdeleted = false AND matches.hascontent = false GROUP BY id LIMIT 1";

$res = mysqli_query($con, $sql);

//Perform natural language search if not finding a direct reply. This MUST lead to articles.
if (!($res && mysqli_num_rows($res) == 1)) {
	$sql = "SELECT id, title, hascontent, confidence FROM (
		SELECT id, title, isdeleted, TRIM(content) <> '' As hascontent, 5 AS 'confidence'
			FROM articles WHERE MATCH(keywords) AGAINST('$input' IN NATURAL LANGUAGE MODE) UNION
		SELECT id, title, isdeleted, TRIM(content) <> '' As hascontent, 5 AS 'confidence'
			FROM articles WHERE MATCH(keywords, title) AGAINST('$input' IN NATURAL LANGUAGE MODE) UNION
		SELECT id, title, isdeleted, TRIM(content) <> '' As hascontent, 3 AS 'confidence'
			FROM articles WHERE MATCH(keywords, title, content) AGAINST('$input' IN NATURAL LANGUAGE MODE) UNION
		SELECT id, title, isdeleted, TRIM(content) <> '' As hascontent, 1 AS 'confidence'
			FROM articles WHERE MATCH(keywords, title, content) AGAINST('$input' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION)
	) matches WHERE matches.isdeleted = false AND matches.hascontent = true GROUP BY id ORDER BY confidence DESC LIMIT " . $conf['BotResultLimit'];

	$res = mysqli_query($con, $sql);
}

if ($res && mysqli_num_rows($res) > 0) {

	while ($row = mysqli_fetch_assoc($res)) {

		$message = new \stdClass();
		$message->id = $row['id'];
		$message->title = $row['title'];
		$message->confidence = $row['confidence'];

		if (strpos($message->title, '{{user}}') !== false) {
			//User name customization for personal looking message
			//TODO: Make this more powerful by providing more possible tokens
			$message->title = str_replace('{{user}}', explode(" ", $_SESSION['user']['name'])[0], $message->title);
		}

		mysqli_query($con, "INSERT INTO history (userid, message, source, articleid, confidence)
		VALUES ($userid, '$message->title', 'bot', $message->id, $message->confidence)");

		if (!$row['hascontent']) {
			unset($message->id);
		}

		array_push($messages, $message);
	}
} else {
	$message = new \stdClass();

	$message->title = "Sorry, I could not understand you";
	$message->id = '0';
	$message->confidence = 0;

	mysqli_query($con, "INSERT INTO history (userid, message, source, articleid, confidence)
	VALUES ($userid, '$message->title', 'bot', $message->id, $message->confidence)");

	array_push($messages, $message);
}

echo json_encode($messages);
