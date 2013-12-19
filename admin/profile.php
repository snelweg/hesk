<?php
/*******************************************************************************
*  Title: Help Desk Software HESK
*  Version: 2.5.2 from 13th October 2013
*  Author: Klemen Stirn
*  Website: http://www.hesk.com
********************************************************************************
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2005-2013 Klemen Stirn. All Rights Reserved.
*  HESK is a registered trademark of Klemen Stirn.

*  The HESK may be used and modified free of charge by anyone
*  AS LONG AS COPYRIGHT NOTICES AND ALL THE COMMENTS REMAIN INTACT.
*  By using this code you agree to indemnify Klemen Stirn from any
*  liability that might arise from it's use.

*  Selling the code for this program, in part or full, without prior
*  written consent is expressly forbidden.

*  Using this code, in part or full, to create derivate work,
*  new scripts or products is expressly forbidden. Obtain permission
*  before redistributing this software over the Internet or in
*  any other medium. In all cases copyright and header must remain intact.
*  This Copyright is in full effect in any country that has International
*  Trade Agreements with the United States of America or
*  with the European Union.

*  Removing any of the copyright notices without purchasing a license
*  is expressly forbidden. To remove HESK copyright notice you must purchase
*  a license for this script. For more information on how to obtain
*  a license please visit the page below:
*  https://www.hesk.com/buy.php
*******************************************************************************/

define('IN_SCRIPT',1);
define('HESK_PATH','../');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');
hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

/* Check permissions */
$can_view_tickets = hesk_checkPermission('can_view_tickets',0);
$can_reply_tickets = hesk_checkPermission('can_reply_tickets',0);
$can_view_unassigned = hesk_checkPermission('can_view_unassigned',0);

/* Update profile? */
if ( ! empty($_POST['action']))
{
	// Demo mode
	if ( defined('HESK_DEMO') )
	{
		hesk_process_messages($hesklang['sdemo'], 'profile.php', 'NOTICE');
	}

	// Update profile
	update_profile();
}
else
{
	$res = hesk_dbQuery('SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `id` = '".intval($_SESSION['id'])."' LIMIT 1");
	$tmp = hesk_dbFetchAssoc($res);

	foreach ($tmp as $k=>$v)
	{
		if ($k == 'pass')
        {
			if ($v == '499d74967b28a841c98bb4baaabaad699ff3c079')
			{
				define('WARN_PASSWORD',true);
			}
			continue;
        }
        elseif ($k == 'categories')
		{
			continue;
		}
		$_SESSION['new'][$k]=$v;
	}
}

if ( ! isset($_SESSION['new']['username']))
{
	$_SESSION['new']['username'] = '';
}

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

/* Print admin navigation */
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');
?>

</td>
</tr>
<tr>
<td>

<?php
/* This will handle error, success and notice messages */
hesk_handle_messages();

if (defined('WARN_PASSWORD'))
{
	hesk_show_notice($hesklang['chdp2'],'<span class="important">'.$hesklang['security'].'</span>');
}
?>

	<h3 align="center"><?php echo $hesklang['profile_for'].' <b>'.$_SESSION['new']['user']; ?></b></h3>

	<p align="center"><?php echo $hesklang['req_marked_with']; ?> <span class="important">*</span></p>

	<?php
	if ($hesk_settings['can_sel_lang'])
	{
		/* Update preferred language in the database? */
		if (isset($_GET['save_language']) )
		{
			$newlang = hesk_input( hesk_GET('language') );

			/* Only update if it's a valid language */
			if ( isset($hesk_settings['languages'][$newlang]) )
			{
            	$newlang = ($newlang == HESK_DEFAULT_LANGUAGE) ? "NULL" : "'" . hesk_dbEscape($newlang) . "'";
				hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."users` SET `language`=$newlang WHERE `id`='".intval($_SESSION['id'])."' LIMIT 1");
			}
		}

		$str  = '<form method="get" action="profile.php" style="margin:0;padding:0;border:0;white-space:nowrap;">';
        $str .= '<input type="hidden" name="save_language" value="1" />';
        $str .= '<p>'.$hesklang['chol'].': ';

        if ( ! isset($_GET) )
        {
        	$_GET = array();
        }

		foreach ($_GET as $k => $v)
		{
			if ($k == 'language' || $k == 'save_language')
			{
				continue;
			}
			$str .= '<input type="hidden" name="'.htmlentitieshesk_htmlentities($k).'" value="'.hesk_htmlentities($v).'" />';
		}

        $str .= '<select name="language" onchange="this.form.submit()">';
		$str .= hesk_listLanguages(0);
		$str .= '</select>';

	?>
        <script language="javascript" type="text/javascript">
		document.write('<?php echo str_replace(array('"','<','=','>'),array('\42','\74','\75','\76'),$str . '</p></form>'); ?>');
        </script>
        <noscript>
        <?php
        	echo $str . '<input type="submit" value="'.$hesklang['go'].'" /></p></form>';
        ?>
        </noscript>
	<?php
	}
    ?>

	<form method="post" action="profile.php" name="form1">

<br />

<span class="section">&raquo; <?php echo $hesklang['pinfo']; ?></span>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<!-- Contact info -->
	<table border="0">
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['name']; ?>: <font class="important">*</font></td>
	<td><input type="text" name="name" size="30" maxlength="50" value="<?php echo $_SESSION['new']['name']; ?>" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['email']; ?>: <font class="important">*</font></td>
	<td><input type="text" name="email" size="30" maxlength="255" value="<?php echo $_SESSION['new']['email']; ?>" /></td>
	</tr>
    <?php
    // Let admins change their username
    if ($_SESSION['isadmin'])
    {
	?>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['username']; ?>: <font class="important">*</font></td>
	<td><input type="text" name="user" size="30" maxlength="50" value="<?php echo $_SESSION['new']['user']; ?>" autocomplete="off" /></td>
	</tr>
    <?php
    }
    ?>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['new_pass']; ?>: </td>
	<td><input type="password" name="newpass" size="30" maxlength="20" onkeyup="javascript:hesk_checkPassword(this.value)" autocomplete="off" /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="200"><?php echo $hesklang['confirm_pass']; ?>: </td>
	<td><input type="password" name="newpass2" size="30" maxlength="20" autocomplete="off" /></td>
	</tr>
	<tr>
	<td width="200" style="text-align:right"><?php echo $hesklang['pwdst']; ?>:</td>
	<td>
	<div style="border: 1px solid gray; width: 100px;">
	<div id="progressBar"
	     style="font-size: 1px; height: 14px; width: 0px; border: 1px solid white;">
	</div>
	</div>
	</td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<span class="section">&raquo; <?php echo $hesklang['sig']; ?></span>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<!-- signature -->
	<table border="0">
	<tr>
	<td style="text-align:right" valign="top" width="200"><?php echo $hesklang['signature_max']; ?>:</td>
	<td><textarea name="signature" rows="6" cols="40"><?php echo $_SESSION['new']['signature']; ?></textarea><br />
	<?php echo $hesklang['sign_extra']; ?></td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<?php
if ($can_reply_tickets)
{
?>

<span class="section">&raquo; <?php echo $hesklang['pref']; ?></span>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<table border="0">
	<tr>
	<td style="text-align:right" valign="top" width="200"><?php echo $hesklang['aftrep']; ?>:</td>
	<td>
    <label><input type="radio" name="afterreply" value="0" <?php if (!$_SESSION['new']['afterreply']) {echo 'checked="checked"';} ?>/> <?php echo $hesklang['showtic']; ?></label><br />
    <label><input type="radio" name="afterreply" value="1" <?php if ($_SESSION['new']['afterreply'] == 1) {echo 'checked="checked"';} ?>/> <?php echo $hesklang['gomain']; ?></label><br />
    <label><input type="radio" name="afterreply" value="2" <?php if ($_SESSION['new']['afterreply'] == 2) {echo 'checked="checked"';} ?>/> <?php echo $hesklang['shownext']; ?></label><br />
    </td>
	</tr>
	<tr>
	<td style="text-align:right" valign="top" width="200"><?php echo $hesklang['ts']; ?>:</td>
	<td>
    <label><input type="checkbox" name="autostart" value="1" <?php if (!empty($_SESSION['new']['autostart'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['autoss']; ?></label><br />
    </td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<br />

<?php
} // END $can_reply_tickets
?>


<span class="section">&raquo; <?php echo $hesklang['notn']; ?></span>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

    <p><?php echo $hesklang['nomw']; ?></p>

	<table border="0">
	<tr>
	<td>
    <?php
    if ($can_view_tickets)
    {
		if ($can_view_unassigned)
		{
			?>
			<label><input type="checkbox" name="notify_new_unassigned" value="1" <?php if (!empty($_SESSION['new']['notify_new_unassigned'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['nwts']; ?> <?php echo $hesklang['unas']; ?></label><br />
			<?php
		}
		else
        {
			?>
			<input type="hidden" name="notify_new_unassigned" value="0" />
			<?php
		}
		?>

		<label><input type="checkbox" name="notify_new_my" value="1" <?php if (!empty($_SESSION['new']['notify_new_my'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['nwts']; ?> <?php echo $hesklang['s_my']; ?></label><br />
		<hr />

        <?php if ($can_view_unassigned)
		{
			?>
			<label><input type="checkbox" name="notify_reply_unassigned" value="1" <?php if (!empty($_SESSION['new']['notify_reply_unassigned'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['ncrt']; ?> <?php echo $hesklang['unas']; ?></label><br />
			<?php
		}
		else
		{
			?>
			<input type="hidden" name="notify_reply_unassigned" value="0" />
			<?php
		}
		?>
	    <label><input type="checkbox" name="notify_reply_my" value="1" <?php if (!empty($_SESSION['new']['notify_reply_my'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['ncrt']; ?> <?php echo $hesklang['s_my']; ?></label><br />
        <hr />
	    <label><input type="checkbox" name="notify_assigned" value="1" <?php if (!empty($_SESSION['new']['notify_assigned'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['ntam']; ?></label><br />
	    <label><input type="checkbox" name="notify_note" value="1" <?php if (!empty($_SESSION['new']['notify_note'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['ntnote']; ?></label><br />
    <?php
    } // END $can_view_tickets
    ?>
        <label><input type="checkbox" name="notify_pm" value="1" <?php if (!empty($_SESSION['new']['notify_pm'])) {echo 'checked="checked"';}?> /> <?php echo $hesklang['npms']; ?></label><br />
    </td>
	</tr>
	</table>

	</td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

	<!-- Submit -->
	<p align="center"><input type="hidden" name="action" value="update" />
    <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" /> 
	<input type="submit" value="<?php echo $hesklang['update_profile']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>
    <p>&nbsp;</p>

    </form>

<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


/*** START FUNCTIONS ***/

function update_profile() {
	global $hesk_settings, $hesklang, $can_view_unassigned;

	/* A security check */
	hesk_token_check('POST');

    $sql_pass = '';
    $sql_username = '';

    $hesk_error_buffer = '';

	$_SESSION['new']['name']  = hesk_input( hesk_POST('name') ) or $hesk_error_buffer .= '<li>' . $hesklang['enter_your_name'] . '</li>';
	$_SESSION['new']['email'] = hesk_validateEmail( hesk_POST('email'), 'ERR', 0) or $hesk_error_buffer = '<li>' . $hesklang['enter_valid_email'] . '</li>';
	$_SESSION['new']['signature'] = hesk_input( hesk_POST('signature') );

	/* Signature */
	if (strlen($_SESSION['new']['signature'])>255)
    {
		$hesk_error_buffer .= '<li>' . $hesklang['signature_long'] . '</li>';
    }

    /* Admins can change username */
    if ($_SESSION['isadmin'])
    {
		$_SESSION['new']['user']  = hesk_input( hesk_POST('user') ) or $hesk_error_buffer .= '<li>' . $hesklang['enter_username'] . '</li>';

	    /* Check for duplicate usernames */
		$result = hesk_dbQuery("SELECT `id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `user`='".hesk_dbEscape($_SESSION['new']['user'])."' AND `id`!='".intval($_SESSION['id'])."' LIMIT 1");
		if (hesk_dbNumRows($result) != 0)
		{
	        $hesk_error_buffer .= '<li>' . $hesklang['duplicate_user'] . '</li>';
		}
        else
        {
        	$sql_username =  ",`user`='" . hesk_dbEscape($_SESSION['new']['user']) . "'";
        }
    }

	/* Change password? */
    $newpass = hesk_input( hesk_POST('newpass') );
    $passlen = strlen($newpass);
	if ($passlen > 0)
	{
        /* At least 5 chars? */
        if ($passlen < 5)
        {
        	$hesk_error_buffer .= '<li>' . $hesklang['password_not_valid'] . '</li>';
        }
        /* Check password confirmation */
        else
        {
        	$newpass2 = hesk_input( hesk_POST('newpass2') );

			if ($newpass != $newpass2)
			{
				$hesk_error_buffer .= '<li>' . $hesklang['passwords_not_same'] . '</li>';
			}
            else
            {
				$v = hesk_Pass2Hash($newpass);
				if ($v == '499d74967b28a841c98bb4baaabaad699ff3c079')
				{
					define('WARN_PASSWORD',true);
				}
				$sql_pass = ',`pass`=\''.$v.'\'';
            }
        }
	}

    /* After reply */
    $_SESSION['new']['afterreply'] = intval( hesk_POST('afterreply') );
    if ($_SESSION['new']['afterreply'] != 1 && $_SESSION['new']['afterreply'] != 2)
    {
    	$_SESSION['new']['afterreply'] = 0;
    }

    /* Auto-start ticket timer */
    $_SESSION['new']['autostart'] = isset($_POST['autostart']) ? 1 : 0;

    /* Notifications */
    $_SESSION['new']['notify_new_unassigned']	= empty($_POST['notify_new_unassigned']) || ! $can_view_unassigned ? 0 : 1;
    $_SESSION['new']['notify_new_my'] 			= empty($_POST['notify_new_my']) ? 0 : 1;
    $_SESSION['new']['notify_reply_unassigned'] = empty($_POST['notify_reply_unassigned']) || ! $can_view_unassigned ? 0 : 1;
    $_SESSION['new']['notify_reply_my']			= empty($_POST['notify_reply_my']) ? 0 : 1;
    $_SESSION['new']['notify_assigned']			= empty($_POST['notify_assigned']) ? 0 : 1;
    $_SESSION['new']['notify_note']				= empty($_POST['notify_note']) ? 0 : 1;
    $_SESSION['new']['notify_pm']				= empty($_POST['notify_pm']) ? 0 : 1;

    /* Any errors? */
    if (strlen($hesk_error_buffer))
    {
		/* Process the session variables */
		$_SESSION['new'] = hesk_stripArray($_SESSION['new']);

		$hesk_error_buffer = $hesklang['rfm'].'<br /><br /><ul>'.$hesk_error_buffer.'</ul>';
		hesk_process_messages($hesk_error_buffer,'NOREDIRECT');
    }
    else
    {
		/* Update database */
		hesk_dbQuery(
        "UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."users` SET
	    `name`='".hesk_dbEscape($_SESSION['new']['name'])."',
	    `email`='".hesk_dbEscape($_SESSION['new']['email'])."',
		`signature`='".hesk_dbEscape($_SESSION['new']['signature'])."'
        $sql_username
		$sql_pass ,
	    `afterreply`='".intval($_SESSION['new']['afterreply'])."' ,
        `autostart`='".intval($_SESSION['new']['autostart'])."' ,
	    `notify_new_unassigned`='".intval($_SESSION['new']['notify_new_unassigned'])."' ,
        `notify_new_my`='".intval($_SESSION['new']['notify_new_my'])."' ,
        `notify_reply_unassigned`='".intval($_SESSION['new']['notify_reply_unassigned'])."' ,
        `notify_reply_my`='".intval($_SESSION['new']['notify_reply_my'])."' ,
        `notify_assigned`='".intval($_SESSION['new']['notify_assigned'])."' ,
        `notify_pm`='".intval($_SESSION['new']['notify_pm'])."',
        `notify_note`='".intval($_SESSION['new']['notify_note'])."'
	    WHERE `id`='".intval($_SESSION['id'])."' LIMIT 1"
        );

		/* Process the session variables */
		$_SESSION['new'] = hesk_stripArray($_SESSION['new']);

        /* Update session variables */
        foreach ($_SESSION['new'] as $k => $v)
        {
        	$_SESSION[$k] = $v;
        }
        unset($_SESSION['new']);

	    hesk_process_messages($hesklang['profile_updated_success'],'profile.php','SUCCESS');
    }
} // End update_profile()

?>
