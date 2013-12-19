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

/* Check if this is a valid include */
if (!defined('IN_SCRIPT')) {die('Invalid attempt');} 

$num_mail = hesk_checkNewMail();
$num_mail = $num_mail ? '<b>'.$num_mail.'</b>' : 0;
?>

<div align="center">
<table width="100%" border="0" cellspacing="0" cellpadding="0">

	<tr>
	<td style="width:4px; height:4px"><img src="../img/header_up_left.png" width="4" height="4" alt="" /></td>
	<td style="background-image:url(../img/header_top.png); background-repeat:repeat-x; background-position:top; height:4px"></td>
	<td style="width:4px; height:4px"><img src="../img/header_up_right.png" width="4" height="4" alt="" /></td>
	</tr>

	<tr>
	<td style="width:4px; background-image:url(../img/header_left.png); background-repeat:repeat-y; background-position:left;"></td>
	<td>

    <!-- START MENU LINKS -->

		<table border="0" class="header" cellspacing="0" cellpadding="3">
		<tr>
		<td align="left">

		<table border="0" align="left" cellpadding="0" cellspacing="0">
		<tr>

			<td><a href="admin_main.php"><img src="../img/ico_home.gif" width="26" height="26" border="0" alt="<?php echo $hesklang['main_page']; ?>" title="<?php echo $hesklang['main_page']; ?>" /><br /><?php echo $hesklang['main_page']; ?></a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>

		<?php
		if (hesk_checkPermission('can_man_users',0))
		{
			echo '
			<td><a href="manage_users.php"><img src="../img/ico_users.gif" width="26" height="26" border="0" alt="'.$hesklang['menu_users'].'" title="'.$hesklang['menu_users'].'" /><br />'.$hesklang['menu_users'].'</a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
            ';
		}
		if (hesk_checkPermission('can_man_cat',0))
		{
			echo '
			<td><a href="manage_categories.php"><img src="../img/ico_categories.gif" width="26" height="26" border="0" alt="'.$hesklang['menu_cat'].'" title="'.$hesklang['menu_cat'].'" /><br />'.$hesklang['menu_cat'].'</a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			';
		}
		if (hesk_checkPermission('can_man_canned',0))
		{
			echo '
			<td><a href="manage_canned.php"><img src="../img/ico_canned.gif" width="26" height="26" border="0" alt="'.$hesklang['menu_can'].'" title="'.$hesklang['menu_can'].'" /><br />'.$hesklang['menu_can'].'</a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			';
		}
		if ($hesk_settings['kb_enable'])
		{
        	if (hesk_checkPermission('can_man_kb',0))
            {
			echo '
			<td><a href="manage_knowledgebase.php"><img src="../img/ico_kb.gif" width="26" height="26" border="0" alt="'.$hesklang['menu_kb'].'" title="'.$hesklang['menu_kb'].'" /><br />'.$hesklang['menu_kb'].'</a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			';
            }
            else
            {
			echo '
			<td><a href="knowledgebase_private.php"><img src="../img/ico_kb.gif" width="26" height="26" border="0" alt="'.$hesklang['menu_kb'].'" title="'.$hesklang['menu_kb'].'" /><br />'.$hesklang['menu_kb'].'</a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			';
            }
		}
		if (hesk_checkPermission('can_run_reports',0))
		{
			echo '
			<td><a href="reports.php"><img src="../img/ico_reports.gif" width="26" height="26" border="0" alt="'.$hesklang['reports'].'"  title="'.$hesklang['reports'].'" /><br />'.$hesklang['reports'].'</a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			';
		}
		elseif (hesk_checkPermission('can_export',0))
		{
			echo '
			<td><a href="export.php"><img src="../img/ico_reports.gif" width="26" height="26" border="0" alt="'.$hesklang['reports'].'"  title="'.$hesklang['reports'].'" /><br />'.$hesklang['reports'].'</a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			';
		}
		if (hesk_checkPermission('can_man_settings',0))
		{
			echo '
			<td><a href="admin_settings.php"><img src="../img/ico_settings.gif" width="26" height="26" border="0" alt="'.$hesklang['settings'].'"  title="'.$hesklang['settings'].'" /><br />'.$hesklang['settings'].'</a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			';
		}
		?>

			<td><a href="profile.php"><img src="../img/ico_profile.gif" width="26" height="26" border="0" alt="<?php echo $hesklang['menu_profile']; ?>" title="<?php echo $hesklang['menu_profile']; ?>" /><br /><?php echo $hesklang['menu_profile']; ?></a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td><a href="mail.php"><img src="../img/ico_mail.gif" width="26" height="26" border="0" alt="<?php echo $hesklang['menu_msg']; ?>" title="<?php echo $hesklang['menu_msg']; ?>" /><br /><?php echo $hesklang['menu_msg']; ?> (<?php echo $num_mail; unset($num_mail); ?>)</a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td><a href="index.php?a=logout&amp;token=<?php echo hesk_token_echo(); ?>"><img src="../img/ico_logout.gif" width="26" height="26" border="0" alt="<?php echo $hesklang['logout']; ?>" title="<?php echo $hesklang['logout']; ?>" /><br /><?php echo $hesklang['logout']; ?></a><br /><img src="../img/blank.gif" width="50" height="1" alt="" /></td>
			</tr>
			</table>

		</td>
		</tr>
		</table>

    <!-- END MENU LINKS -->

	</td>
	<td style="width:4px; background-image: url(../img/header_right.png); background-repeat:repeat-y; background-position:right;"></td>
	</tr>

	<tr>
	<td style="width:4px; height:4px"><img src="../img/header_bottom_left.png" width="4" height="4" alt="" /></td>
	<td style="background-image:url(../img/header_bottom.png); background-repeat:repeat-x; background-position:bottom; height:4px"></td>
	<td style="width:4px; height:4px"><img src="../img/header_bottom_right.png" width="4" height="4" alt="" /></td>
	</tr>

</table>
</div>
