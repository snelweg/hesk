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

/* Check permissions for this feature */
hesk_checkPermission('can_man_canned');

/* What should we do? */
if ( $action = hesk_REQUEST('a') )
{
	if ( defined('HESK_DEMO') )  {hesk_process_messages($hesklang['ddemo'], 'manage_canned.php', 'NOTICE');}
	elseif ($action == 'new')    {new_saved();}
	elseif ($action == 'edit')   {edit_saved();}
	elseif ($action == 'remove') {remove();}
	elseif ($action == 'order')  {order_saved();}
}

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

/* Print main manage users page */
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');
?>

</td>
</tr>
<tr>
<td>

<script language="javascript" type="text/javascript"><!--
function confirm_delete()
{
if (confirm('<?php echo addslashes($hesklang['delete_saved']); ?>')) {return true;}
else {return false;}
}

function hesk_insertTag(tag) {
var text_to_insert = '%%'+tag+'%%';
hesk_insertAtCursor(document.form1.msg, text_to_insert);
document.form1.msg.focus();
}

function hesk_insertAtCursor(myField, myValue) {
if (document.selection) {
myField.focus();
sel = document.selection.createRange();
sel.text = myValue;
}
else if (myField.selectionStart || myField.selectionStart == '0') {
var startPos = myField.selectionStart;
var endPos = myField.selectionEnd;
myField.value = myField.value.substring(0, startPos)
+ myValue
+ myField.value.substring(endPos, myField.value.length);
} else {
myField.value += myValue;                                             
}
}
//-->
</script>

<?php
/* This will handle error, success and notice messages */
hesk_handle_messages();
?>

<h3 style="padding-bottom:5px"><?php echo $hesklang['manage_saved']; ?> [<a href="javascript:void(0)" onclick="javascript:alert('<?php echo hesk_makeJsString($hesklang['manage_intro']); ?>')">?</a>]</h3>

&nbsp;<br />

<?php
$result = hesk_dbQuery('SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'std_replies` ORDER BY `reply_order` ASC');
$options='';
$javascript_messages='';
$javascript_titles='';

$i=1;
$j=0;
$num = hesk_dbNumRows($result);

if ($num < 1)
{
    echo '<p>'.$hesklang['no_saved'].'</p>';
}
else
{
	?>
	<div align="center">
	<table border="0" cellspacing="1" cellpadding="3" class="white" width="100%">
	<tr>
	<th class="admin_white" style="text-align:left"><b><i><?php echo $hesklang['saved_title']; ?></i></b></th>
	<th class="admin_white" style="width:80px"><b><i>&nbsp;<?php echo $hesklang['opt']; ?>&nbsp;</i></b></th>
	</tr>
	<?php

    while ($mysaved=hesk_dbFetchAssoc($result))
    {
    	$j++;

		if (isset($_SESSION['canned']['selcat2']) && $mysaved['id'] == $_SESSION['canned']['selcat2'])
		{
			$color = 'admin_green';
			unset($_SESSION['canned']['selcat2']);
		}
		else
		{
			$color = $i ? 'admin_white' : 'admin_gray';
		}
        
		$tmp   = $i ? 'White' : 'Blue';
	    $style = 'class="option'.$tmp.'OFF" onmouseover="this.className=\'option'.$tmp.'ON\'" onmouseout="this.className=\'option'.$tmp.'OFF\'"';
	    $i     = $i ? 0 : 1;

        $options .= '<option value="'.$mysaved['id'].'"';
        $options .= (isset($_SESSION['canned']['id']) && $_SESSION['canned']['id'] == $mysaved['id']) ? ' selected="selected" ' : '';
        $options .= '>'.$mysaved['title'].'</option>';


        $javascript_messages.='myMsgTxt['.$mysaved['id'].']=\''.str_replace("\r\n","\\r\\n' + \r\n'", addslashes($mysaved['message']) )."';\n";
        $javascript_titles.='myTitle['.$mysaved['id'].']=\''.addslashes($mysaved['title'])."';\n";

	    echo '
	    <tr>
	    <td class="'.$color.'" style="text-align:left">'.$mysaved['title'].'</td>
        <td class="'.$color.'" style="text-align:center; white-space:nowrap;">
        ';

        if ($num > 1)
        {
        	if ($j == 1)
            {
            	echo'<img src="../img/blank.gif" width="16" height="16" alt="" style="padding:3px;border:none;" /> <a href="manage_canned.php?a=order&amp;replyid='.$mysaved['id'].'&amp;move=15&amp;token='.hesk_token_echo(0).'"><img src="../img/move_down.png" width="16" height="16" alt="'.$hesklang['move_dn'].'" title="'.$hesklang['move_dn'].'" '.$style.' /></a>';
            }
            elseif ($j == $num)
            {
            	echo'<a href="manage_canned.php?a=order&amp;replyid='.$mysaved['id'].'&amp;move=-15&amp;token='.hesk_token_echo(0).'"><img src="../img/move_up.png" width="16" height="16" alt="'.$hesklang['move_up'].'" title="'.$hesklang['move_up'].'" '.$style.' /></a> <img src="../img/blank.gif" width="16" height="16" alt="" style="padding:3px;border:none;" />';
            }
            else
            {
            	echo'
			    <a href="manage_canned.php?a=order&amp;replyid='.$mysaved['id'].'&amp;move=-15&amp;token='.hesk_token_echo(0).'"><img src="../img/move_up.png" width="16" height="16" alt="'.$hesklang['move_up'].'" title="'.$hesklang['move_up'].'" '.$style.' /></a>
			    <a href="manage_canned.php?a=order&amp;replyid='.$mysaved['id'].'&amp;move=15&amp;token='.hesk_token_echo(0).'"><img src="../img/move_down.png" width="16" height="16" alt="'.$hesklang['move_dn'].'" title="'.$hesklang['move_dn'].'" '.$style.' /></a>
                ';
            }
        }
        else
        {
        	echo '';
        }

        echo '
        <a href="manage_canned.php?a=remove&amp;id='.$mysaved['id'].'&amp;token='.hesk_token_echo(0).'" onclick="return confirm_delete();"><img src="../img/delete.png" width="16" height="16" alt="'.$hesklang['remove'].'" title="'.$hesklang['remove'].'" '.$style.' /></a>&nbsp;</td>
	    </tr>
		';
    } // End while

    ?>
	</table>
	</div>
    <?php
}

?>

<script language="javascript" type="text/javascript"><!--
var myMsgTxt = new Array();
myMsgTxt[0]='';
var myTitle = new Array();
myTitle[0]='';

<?php
echo $javascript_titles;
echo $javascript_messages;
?>

function setMessage(msgid) {
    if (document.getElementById) {
        document.getElementById('HeskMsg').innerHTML='<textarea name="msg" rows="15" cols="70">'+myMsgTxt[msgid]+'</textarea>';
        document.getElementById('HeskTitle').innerHTML='<input type="text" name="name" size="40" maxlength="50" value="'+myTitle[msgid]+'">';
    } else {
        document.form1.msg.value=myMsgTxt[msgid];
        document.form1.name.value=myTitle[msgid];
    }

    if (msgid==0) {
        document.form1.a[0].checked=true;
    } else {
        document.form1.a[1].checked=true;
    }
}
//-->
</script>

<p>&nbsp;</p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<form action="manage_canned.php" method="post" name="form1">
	<h3 align="center"><?php echo $hesklang['new_saved']; ?></h3>

	<div align="center">
	<table border="0">
	<tr>
	<td>

	<?php
    if ($num > 0)
    {
	    ?>
		<p>
	    <label><input type="radio" name="a" value="new" <?php echo (!isset($_SESSION['canned']['what']) || $_SESSION['canned']['what'] != 'EDIT') ? 'checked="checked"' : ''; ?> /> <?php echo $hesklang['canned_add']; ?></label><br />
	    <label><input type="radio" name="a" value="edit" <?php echo (isset($_SESSION['canned']['what']) && $_SESSION['canned']['what'] == 'EDIT') ? 'checked="checked"' : ''; ?> /> <?php echo $hesklang['canned_edit']; ?></label>:
		<select name="saved_replies" onchange="setMessage(this.value)"><option value="0"> - <?php echo $hesklang['select_empty']; ?> - </option><?php echo $options; ?></select>
	    </p>
	    <?php
    }
    else
    {
    	echo '<p><input type="hidden" name="a" value="new" /> ' . $hesklang['canned_add'] . '</label></p>';
    }
    ?>

	<p><b><?php echo $hesklang['saved_title']; ?>:</b> <span id="HeskTitle"><input type="text" name="name" size="40" maxlength="50" <?php if (isset($_SESSION['canned']['name'])) {echo ' value="'.stripslashes($_SESSION['canned']['name']).'" ';} ?> /></span></p>

	<p><b><?php echo $hesklang['message']; ?>:</b><br />
	<span id="HeskMsg"><textarea name="msg" rows="15" cols="70"><?php
    if (isset($_SESSION['canned']['msg']))
    {
    	echo stripslashes($_SESSION['canned']['msg']);
    }
    ?></textarea></span><br />

	<?php echo $hesklang['insert_special']; ?>:<br />
	<a href="javascript:void(0)" onclick="hesk_insertTag('HESK_NAME')"><?php echo $hesklang['name']; ?></a> |
	<a href="javascript:void(0)" onclick="hesk_insertTag('HESK_EMAIL')"><?php echo $hesklang['email']; ?></a>
	<?php
	    foreach ($hesk_settings['custom_fields'] as $k=>$v)
	    {
	        if ($v['use'])
	        {
	            echo '| <a href="javascript:void(0)" onclick="hesk_insertTag(\'HESK_'.$k.'\')">'.$v['name'].'</a> ';
	        }
	    }
	?>
	</p>

	</td>
	</tr>
	</table>
	</div>

	<p align="center">
    <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
    <input type="submit" value="<?php echo $hesklang['save_reply']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" />
    </p>
	</form>

    </td>
	<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
	<td><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
</table>

<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


/*** START FUNCTIONS ***/

function edit_saved()
{
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check('POST');

    $hesk_error_buffer = '';

	$id = intval( hesk_POST('saved_replies') ) or $hesk_error_buffer .= '<li>' . $hesklang['selcan'] . '</li>';
	$savename = hesk_input( hesk_POST('name') ) or $hesk_error_buffer .= '<li>' . $hesklang['ent_saved_title'] . '</li>';
	$msg = hesk_input( hesk_POST('msg') ) or $hesk_error_buffer .= '<li>' . $hesklang['ent_saved_msg'] . '</li>';

	$_SESSION['canned']['what'] = 'EDIT';
    $_SESSION['canned']['id'] = $id;
    $_SESSION['canned']['name'] = $savename;
    $_SESSION['canned']['msg'] = $msg;

    /* Any errors? */
    if (strlen($hesk_error_buffer))
    {
    	$hesk_error_buffer = $hesklang['rfm'].'<br /><br /><ul>'.$hesk_error_buffer.'</ul>';
    	hesk_process_messages($hesk_error_buffer,'manage_canned.php?saved_replies='.$id);
    }

	$result = hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."std_replies` SET `title`='".hesk_dbEscape($savename)."',`message`='".hesk_dbEscape($msg)."' WHERE `id`='".intval($id)."' LIMIT 1");

	unset($_SESSION['canned']['what']);
    unset($_SESSION['canned']['id']);
    unset($_SESSION['canned']['name']);
    unset($_SESSION['canned']['msg']);

    hesk_process_messages($hesklang['your_saved'],'manage_canned.php?saved_replies='.$id,'SUCCESS');
} // End edit_saved()


function new_saved()
{
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check('POST');

    $hesk_error_buffer = '';
	$savename = hesk_input( hesk_POST('name') ) or $hesk_error_buffer .= '<li>' . $hesklang['ent_saved_title'] . '</li>';
	$msg = hesk_input( hesk_POST('msg') ) or $hesk_error_buffer .= '<li>' . $hesklang['ent_saved_msg'] . '</li>';

	$_SESSION['canned']['what'] = 'NEW';
    $_SESSION['canned']['name'] = $savename;
    $_SESSION['canned']['msg'] = $msg;

    /* Any errors? */
    if (strlen($hesk_error_buffer))
    {
    	$hesk_error_buffer = $hesklang['rfm'].'<br /><br /><ul>'.$hesk_error_buffer.'</ul>';
    	hesk_process_messages($hesk_error_buffer,'manage_canned.php');
    }

	/* Get the latest reply_order */
	$result = hesk_dbQuery('SELECT `reply_order` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'std_replies` ORDER BY `reply_order` DESC LIMIT 1');
	$row = hesk_dbFetchRow($result);
	$my_order = $row[0]+10;

	hesk_dbQuery("INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."std_replies` (`title`,`message`,`reply_order`) VALUES ('".hesk_dbEscape($savename)."','".hesk_dbEscape($msg)."','".intval($my_order)."')");

	unset($_SESSION['canned']['what']);
    unset($_SESSION['canned']['name']);
    unset($_SESSION['canned']['msg']);

    hesk_process_messages($hesklang['your_saved'],'manage_canned.php','SUCCESS');
} // End new_saved()


function remove()
{
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check();

	$mysaved = intval( hesk_GET('id') ) or hesk_error($hesklang['id_not_valid']);

	hesk_dbQuery("DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."std_replies` WHERE `id`='".intval($mysaved)."' LIMIT 1");
	if (hesk_dbAffectedRows() != 1)
    {
    	hesk_error("$hesklang[int_error]: $hesklang[reply_not_found].");
    }

    hesk_process_messages($hesklang['saved_rem_full'],'manage_canned.php','SUCCESS');
} // End remove()


function order_saved()
{
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check();

	$replyid = intval( hesk_GET('replyid') ) or hesk_error($hesklang['reply_move_id']);
    $_SESSION['canned']['selcat2'] = $replyid;

	$reply_move = intval( hesk_GET('move') );

	hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."std_replies` SET `reply_order`=`reply_order`+".intval($reply_move)." WHERE `id`='".intval($replyid)."' LIMIT 1");
	if (hesk_dbAffectedRows() != 1) {hesk_error("$hesklang[int_error]: $hesklang[reply_not_found].");}

	/* Update all category fields with new order */
	$result = hesk_dbQuery('SELECT `id` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'std_replies` ORDER BY `reply_order` ASC');

	$i = 10;
	while ($myreply=hesk_dbFetchAssoc($result))
	{
	    hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."std_replies` SET `reply_order`=".intval($i)." WHERE `id`='".intval($myreply['id'])."' LIMIT 1");
	    $i += 10;
	}

	header('Location: manage_canned.php');
	exit();
} // End order_saved()

?>
