<?php
//Set the banning active
$ssi_ban = true;
if (file_exists("settings.php")) {
	include ("settings.php");
} else {
	die("Modify settings.template.php and save as settings.php.");
}
require 'PHP-Minecraft-Query/MinecraftRcon.class.php';
require 'PHP-Minecraft-Query/MinecraftQuery.class.php';
//Path to SSI.php
require ("../minetowns.com/SSI.php");

//Page title. This will appear in the browser
$context['page_title_html_safe'] = 'Whitelist';

$id = $context['user']['id'];
loadMemberData(array($id));
global $user_profile, $context;
$mcusername = $user_profile[$id]['options']['cust_minecr'];
if ($mcusername == "" && isset($_REQUEST['mcuser'])) {
	$mcusername = $_REQUEST['mcuser'];
}

$is_logged_on_mc = false;
if ($mcusername != "") {
	$Query = new MinecraftQuery();
	try {
		$Query -> Connect($mc_server_address, $mc_query_port);
		if (in_array($mcusername, $Query -> GetPlayers())) {
			$is_logged_on_mc = true;
		}
	} catch( MinecraftQueryException $e ) {
	}
}
//This is self explanatory
template_header();

//Here we define the link tree
$context['linktree'] = array('href' => $scripturl, );
if (isset($_REQUEST['reqwhitelist'])) {
	if ($is_logged_on_mc) {
		try {
			$Rcon = new MinecraftRcon;

			$Rcon -> Connect($mc_server_address, $mc_rcon_port, $mc_rcon_password, $mc_rcon_timeout);
			$result = lookup($mcusername, $mcbans_api_key);
			$Data = $Rcon -> Command("wl addtolist " . $mcusername);
			if (count($result['global']) > 0 || count($result['local']) > 0) {
				$Rcon -> Command("a [Whitelist] User " . $mcusername . " using forum account " . $user_profile[$id]['member_name'] . " has " . count($result['global']) . " global and " . count($result['local']) . " bans!");
				echo '<p>You have ' . count($result['global']) . ' global and ' . count($result['local']) . ' bans! Please see <a href="http://mcbans.com/player/' . $mcusername . '">MCBans.com</a>.  Please request an admin to whitelist you on the server.</p>';
			} elseif ($context['user']['is_logged']) {
				$Data = $Rcon -> Command("wl add " . $mcusername);
				$Rcon -> Command("a [Whitelist] User " . $mcusername . " using forum account " . $user_profile[$id]['member_name'] . " was autowhitelisted");
				echo '<p>You were autowhitelisted as you were logged in on the forum</p>';
			} else {
				echo '<p>Thank you, as you weren\'t registered on the site your whitelist has gone into a queue. Please request an admin to whitelist you on the server.</p>';
			}
			if ($Data === false) {
				throw new MinecraftRconException("Failed to get command result.");
			} else if (StrLen($Data) == 0) {
				throw new MinecraftRconException("Got command result, but it's empty.");
			}

			echo HTMLSpecialChars($Data);
		} catch( MinecraftRconException $e ) {
			echo $e -> getMessage();
		}

		$Rcon -> Disconnect();
	} else {
		echo "You must be logged in on the server in order to get whitelisted.";
	}
} else {

	//Here is the content, such as the title and the body message of the custom page.
	include ("content.html");
	if ($mcusername != "") {

		echo "<b>Your Minecraft username seems to be <i>" . $mcusername . "</i></b>. If this is not correct, please correct that information in your profile settings.<br/>";
		if ($is_logged_on_mc) {
			echo 'You are logged in on the server.
	<form method="POST" action="?">
		<input type="hidden" name="reqwhitelist" value="true">
		<input type="hidden" name="mcuser" value="' . $_REQUEST['mcuser'] . '">
		<input type="submit" value="Request whitelist">
	</form>';
		} else {
			echo "You are <b>not</b> logged in on the server. Please connect to the server first.<br/>";
		}
	}
	if (!$context['user']['is_logged']) {
		echo "<b>Please log in or register to get automatically whitelisted. If you do not want to register, enter your minecraft username below:</b><br/>";
		echo '<form method="POST">
		Minecraft username: <input type="text" name="mcuser" maxlength="16" />
		<input type="submit" value="Submit username">
	</form>';
	}
}

//This is self explanatory too.
template_footer();

function lookup($player, $apikey) {
	$data = array('player' => $player, 'exec' => 'playerLookup');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://api.mcbans.com/v2/' . $apikey);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}
?>