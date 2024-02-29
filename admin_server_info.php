<?php

include('config.php');

//If no users are there in the system then create DB with default data
$contains_users = mysqli_query($con, 'DESCRIBE `users`;');

if ($contains_users) {
	if (!(isset($_SESSION['user']) && $_SESSION['user']['group'] === "ADMIN")) {
		echo "<script>window.location.href='index.php';</script>";
		return;
	}
}

include('header.php');

function install_database($con)
{
	$queries = array();

	array_push($queries, "DROP TABLE IF EXISTS `users`;");
	array_push($queries, "DROP TABLE IF EXISTS `articles`;");
	array_push($queries, "DROP TABLE IF EXISTS `history`;");

	array_push($queries, "CREATE TABLE `users` (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`username` VARCHAR(100) NOT NULL UNIQUE,
		`password` VARCHAR(100) DEFAULT NULL,
		`group` ENUM('USER', 'ADMIN') DEFAULT 'USER',
		`name` VARCHAR(100) NOT NULL,
		`email` VARCHAR(100) DEFAULT NULL,
		`phone` VARCHAR(20) DEFAULT NULL,
		`isactive` BOOLEAN NOT NULL DEFAULT FALSE,
		`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		`lastupdated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		`lastupdatedby` INT DEFAULT NULL
	  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

	array_push($queries, "CREATE TABLE `articles` (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`keywords` VARCHAR(200) NOT NULL,
		`title` VARCHAR(500) NOT NULL,
		`content` TEXT,
		`lastupdated` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		`lastupdatedby` INT NOT NULL,
		`isdeleted` BOOLEAN NOT NULL DEFAULT FALSE,
		FULLTEXT(keywords),
		FULLTEXT(keywords,title),
		FULLTEXT(keywords,title,content),
		UNIQUE (keywords,title)
	  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

	array_push($queries, "CREATE TABLE `history` (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`userid` INT NOT NULL,
		`message` VARCHAR(500) NOT NULL,
		`articleid` INT DEFAULT 0,
		`confidence` INT DEFAULT 0,
		`date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		`source` ENUM('BOT', 'USER') DEFAULT 'USER'
	  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

	array_push($queries, "INSERT INTO `users` (`username`, `password`, `group`, `name`, `isactive`) VALUES
		('a', 'a', 'ADMIN', 'Admin', TRUE),
		('u', 'u', 'USER', 'User 1', TRUE),
		('u2', 'u', 'USER', 'User 2', TRUE),
		('s', 's', 'ADMIN', 'Shalvin', TRUE);");

	array_push($queries, "INSERT INTO `articles` (`keywords`, `title`, `content`, `lastupdatedby`) VALUES
		('Name Bot', 'I am Chat Bot', '', 1),
		('Hello Hi Good Morning', 'Hello {{user}}, how may I help you today ?', '', 1),
		('Bye See you later Have a Good Day', 'Sad to see you are going. Have a nice day', '', 1);");

	echo '<h2>Database reload started ...</h2><br>';

	foreach ($queries as $query) {
		echo htmlspecialchars($query) . '<br>';
		$res = mysqli_query($con, $query);
		if ($res) {
			echo 'SUCCESS <br>';
		} else {
			echo 'FAILED: ' . mysqli_error($con) . '<br>';
		}
		echo '<br>';
	}
}

?>

<head>
	<title>Chatbot | Server Information</title>
	<style>
		body {
			background-color: var(--bg-color) !important;
			color: var(--font-color) !important;
		}

		h1,
		h2,
		h3,
		h4 {
			color: var(--heading-color) !important;
			background-color: var(--bg-color) !important;
		}

		a {
			color: var(--font-color) !important;
			background-color: transparent !important;
		}

		a:hover {
			text-decoration: none !important;
			text-decoration-style: none !important;
			color: rgb(255, 76, 76) !important;
		}

		.e,
		.h,
		.v,
		.hr {
			background-color: var(--bg-color) !important;
		}
	</style>
</head>

<body>
	<br><br><br>
	<h3 class="title-main">Server Information</h3>
	<center><br><span>Host: <?= $conf['Host'] ?></span></center>

	<?php

	if ($contains_users == false || isset($_GET['resetdatabase'])) {
		install_database($con);
	} else {
		$res =  mysqli_query($con, "SELECT VERSION() As Version;");
		$db = mysqli_fetch_object($res);

		$db_info = "<div class='center'><h2>MySQL Environment</h2><table><tbody>
		<tr><td class='e'>Host</td><td class='v'>" . mysqli_get_host_info($con) . "</td></tr>
		<tr><td class='e'>Client</td><td class='v'>" . mysqli_get_client_info($con) . "</td></tr>
		<tr><td class='e'>Server Version</td><td class='v'> MySQL v" . mysqli_get_server_info($con) . "</td></tr>
		<tr><td class='e'>Database Version</td><td class='v'> " . $db->Version . "</td></tr>
		</tbody></table></div>";

		echo phpinfo();

		echo $db_info;
	}

	?>

</body>

<?php

include('footer.php');

?>