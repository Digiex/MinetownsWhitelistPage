<?php
//Set the banning active
$ssi_ban = true;
if (file_exists("settings.php")) {
	include ("settings.php");
} else {
	die("Modify settings.template.php and save as settings.php.");
}
require 'PHP-Minecraft-Query/MinecraftRcon.class.php';
//Path to SSI.php
require ("../minetowns.com/SSI.php");

//Page title. This will appear in the browser
$context['page_title_html_safe'] = 'Whitelist';

//This is self explanatory
template_header();

//Here we define the link tree
$context['linktree'] = array('href' => $scripturl, );

//Here is the content, such as the title and the body message of the custom page.
?>
<P STYLE="margin-bottom: 0in">
	Welcome to the Minetown’s whitelist
	process. This is an automated process to gain whitelist (to be able
	to build) in our server. To complete this, you must be ingame at the
	same time, and enter your username at the bottom of this page to gain
	a code to whitelist.
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	Before you do however, please read the
	warning below as it’s important as well as the server rules further
	down which you agree to as part of the whitelist process.
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	<B>Important:</B>
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	This server operates Cheat detection
	(including Flying and X-raying) as well grief control (including full
	logging of block placement/removal and chest access). Any attempt to
	cheat or grief will result in your actions being logged and a global
	ban being issued.
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	This server operates as part of the <B>MCBans Global Banlist</B> (link behind that) project therefore any
	bans received on our server will go global on every server using
	McBans (which is the large percentage). Therefore it is not worth
	your time causing trouble here as we will damage your username. As
	part of the process of McBans, you can appeal any ban however we will
	take screenshots and download the log to guarantee we will always win
	and your ban will stay.
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	By getting whitelist on our server, you
	must accept the above. We will never penalize innocent players and
	will accept genuine appeals on our website, but anyone who is out
	there to cause trouble will damage their reputation across multiple
	servers, as well as doing no damage to use (as we have a simple
	rollback a user command available to undo your damage).
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	Server Rules:
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	[Latest copy of server rules from site
	here]
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	I herby acknowledge I have read the
	notice regarding the McBans Global banlist as well the server rules
	and agree to the terms to be whitelisted:
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	<BR>
</P>
<P STYLE="margin-bottom: 0in">
	<A NAME="_GoBack"></A>Please note, if
	you have any active bans on MCBans, this process will fail and
	require manual intervention between you and a member of staff to
	discuss your whitelist.
</P>
<!-- Snook's text ends here -->
<p>
	<?php
	$id = $context['user']['id'];
	loadMemberData(array($id));
	global $user_profile, $context;
	if ($context['user']['is_logged']) {
		echo "<b>Your Minecraft username seems to be <i>" . $user_profile[$id]['options']['cust_minecr'] . "</i></b>. If this is not correct, please correct that information in your profile settings.";
		try {
			$Rcon = new MinecraftRcon;

			$Rcon -> Connect($mc_server_address, $mc_rcon_port, $mc_rcon_password, $mc_rcon_timeout);

			$Data = $Rcon -> Command("say Sharks");

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
		echo "<b>Please log in or register first to get whitelisted.</b>";
	}
?>
</p>
<?php
//This is self explanatory too.
template_footer();
?>