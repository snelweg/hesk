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

if ( ! isset($status) )
{
	$status = array(
	0 => 'NEW',
	1 => 'WAITING REPLY',
	2 => 'REPLIED',
	#3 => 'RESOLVED (CLOSED)',
	4 => 'IN PROGRESS',
	5 => 'ON HOLD',
	);
}

if ( ! isset($priority) )
{
	$priority = array(
	0 => 'CRITICAL',
	1 => 'HIGH',
	2 => 'MEDIUM',
	3 => 'LOW',
	);
}

if ( ! isset($what) )
{
	$what = 'trackid';
}

if ( ! isset($date_input) )
{
	$date_input = '';
}

/* Can view tickets that are unassigned or assigned to others? */
$can_view_ass_others = hesk_checkPermission('can_view_ass_others',0);
$can_view_unassigned = hesk_checkPermission('can_view_unassigned',0);

/* Category options */
$category_options = '';
if ( isset($hesk_settings['categories']) && count($hesk_settings['categories']) )
{
	foreach ($hesk_settings['categories'] as $row['id'] => $row['name'])
	{
		$row['name'] = (strlen($row['name']) > 30) ? substr($row['name'],0,30) . '...' : $row['name'];
		$selected = ($row['id'] == $category) ? 'selected="selected"' : '';
		$category_options .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['name'].'</option>';
	}
}
else
{
	$res2 = hesk_dbQuery('SELECT `id`, `name` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'categories` WHERE ' . hesk_myCategories('id') . ' ORDER BY `cat_order` ASC');
	while ($row=hesk_dbFetchAssoc($res2))
	{
		$row['name'] = (strlen($row['name']) > 30) ? substr($row['name'],0,30) . '...' : $row['name'];
		$selected = ($row['id'] == $category) ? 'selected="selected"' : '';
		$category_options .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['name'].'</option>';
	}
}

$more = empty($_GET['more']) ? 0 : 1;
$more2 = empty($_GET['more2']) ? 0 : 1;

#echo "SQL: $sql";
?>

<!-- ** START SHOW TICKET FORM ** -->

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
<td class="roundcornerstop"></td>
<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
<td class="roundcornersleft">&nbsp;</td>
<td valign="top">
<form name="showt" action="show_tickets.php" method="get">
<h3 style="margin-bottom:5px">&raquo; <?php echo $hesklang['show_tickets']; ?></h3>
<table border="0" cellpadding="3" cellspacing="0" width="100%">
<tr>
<td width="20%" class="alignTop"><b><?php echo $hesklang['status']; ?></b>: &nbsp; </td>
<td width="80%">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td width="34%"><label><input type="checkbox" name="s0" value="1" <?php if (isset($status[0])) {echo 'checked="checked"';} ?> /> <span class="open"><?php echo $hesklang['open']; ?></span></label></td>
<td width="33%"><label><input type="checkbox" name="s2" value="1" <?php if (isset($status[2])) {echo 'checked="checked"';} ?> /> <span class="replied"><?php echo $hesklang['replied']; ?></span></label></td>
<td width="33%"><label><input type="checkbox" name="s4" value="1" <?php if (isset($status[4])) {echo 'checked="checked"';} ?> /> <span class="inprogress"><?php echo $hesklang['in_progress']; ?></span></label></td>
</tr>
<tr>
<td width="34%"><label><input type="checkbox" name="s1" value="1" <?php if (isset($status[1])) {echo 'checked="checked"';} ?> /> <span class="waitingreply"><?php echo $hesklang['wait_reply']; ?></span></label></td>
<td width="33%"><label><input type="checkbox" name="s3" value="1" <?php if (isset($status[3])) {echo 'checked="checked"';} ?> /> <span class="resolved"><?php echo $hesklang['closed']; ?></span></label></td>
<td width="33%"><label><input type="checkbox" name="s5" value="1" <?php if (isset($status[5])) {echo 'checked="checked"';} ?>  /> <span class="onhold"><?php echo $hesklang['on_hold']; ?></span></td>
</tr>
</table>

</td>
</tr>
</table>

<div id="topSubmit" style="display:<?php echo $more ? 'none' : 'block' ; ?>">
&nbsp;<br />
<input type="submit" value="<?php echo $hesklang['show_tickets']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" />
|
<a href="javascript:void(0)" onclick="Javascript:hesk_toggleLayerDisplay('divShow');Javascript:hesk_toggleLayerDisplay('topSubmit');document.showt.more.value='1';"><?php echo $hesklang['mopt']; ?></a>
<br />&nbsp;<br />
</div>

<div id="divShow" style="display:<?php echo $more ? 'block' : 'none' ; ?>">

<table border="0" cellpadding="3" cellspacing="0" width="100%">
<tr>
<td width="20%" class="borderTop alignTop"><b><?php echo $hesklang['priority']; ?></b>: &nbsp; </td>
<td width="80%" class="borderTop alignTop">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td width="34%"><label><input type="checkbox" name="p0" value="1" <?php if (isset($priority[0])) {echo 'checked="checked"';} ?> /> <span class="critical"><?php echo $hesklang['critical']; ?></span></label></td>
<td width="33%"><label><input type="checkbox" name="p2" value="1" <?php if (isset($priority[2])) {echo 'checked="checked"';} ?> /> <span class="medium"><?php echo $hesklang['medium']; ?></span></label></td>
<td width="33%">&nbsp;</td>
</tr>
<tr>
<td width="34%"><label><input type="checkbox" name="p1" value="1" <?php if (isset($priority[1])) {echo 'checked="checked"';} ?> /> <span class="important"><?php echo $hesklang['high']; ?></span></label></td>
<td width="33%"><label><input type="checkbox" name="p3" value="1" <?php if (isset($priority[3])) {echo 'checked="checked"';} ?> /> <span class="normal"><?php echo $hesklang['low']; ?></span></label></td>
<td width="33%">&nbsp;</td>
</tr>
</table>

</td>
</tr>

<tr>
<td class="borderTop alignTop"><b><?php echo $hesklang['show']; ?></b>: &nbsp; </td>
<td class="borderTop">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td width="34%" class="alignTop">
<label><input type="checkbox" name="s_my" value="1" <?php if ($s_my[1]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_my']; ?></label>
<?php
if ($can_view_unassigned)
{
	?>
    <br />
	<label><input type="checkbox" name="s_un" value="1" <?php if ($s_un[1]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_un']; ?></label>
	<?php
}
?>
</td>
<td width="33%" class="alignTop">
<?php
if ($can_view_ass_others)
{
	?>
	<label><input type="checkbox" name="s_ot" value="1" <?php if ($s_ot[1]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_ot']; ?></label>
    <br />
	<?php
}
?>
<label><input type="checkbox" name="archive" value="1" <?php if ($archive[1]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['disp_only_archived']; ?></label></td>
<td width="33%">&nbsp;</td>
</tr>
</table>

</td>
</tr>

<tr>
<td class="borderTop alignTop"><b><?php echo $hesklang['sort_by']; ?></b>: &nbsp; </td>
<td class="borderTop">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td width="34%"><label><input type="radio" name="sort" value="priority"  <?php if ($sort == 'priority') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['priority']; ?></label></td>
<td width="33%"><label><input type="radio" name="sort" value="lastchange" <?php if ($sort == 'lastchange') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['last_update']; ?></label></td>
<td width="33%"><label><input type="radio" name="sort" value="name" <?php if ($sort == 'name') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['name']; ?></label></td>
</tr>
<tr>
<td width="34%"><label><input type="radio" name="sort" value="subject" <?php if ($sort == 'subject') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['subject']; ?></label></td>
<td width="33%"><label><input type="radio" name="sort" value="status" <?php if ($sort == 'status') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['status']; ?></label></td>
<td width="33%">&nbsp;</td>
</tr>
</table>

</td>
</tr>

<tr>
<td class="borderTop alignTop"><b><?php echo $hesklang['gb']; ?></b>: &nbsp; </td>
<td class="borderTop">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td width="34%"><label><input type="radio" name="g" value=""  <?php if ( ! $group) {echo 'checked="checked"';} ?> /> <?php echo $hesklang['dg']; ?></label></td>
<td width="33%"><?php
if ($can_view_unassigned || $can_view_ass_others)
{
	?>
	<label><input type="radio" name="g" value="owner" <?php if ($group == 'owner') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['owner']; ?></label>
	<?php
}
else
{
	echo '&nbsp;';
}
?>
</td>
<td width="33%">&nbsp;</td>
</tr>
<tr>
<td width="34%"><label><input type="radio" name="g" value="category" <?php if ($group == 'category') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['category']; ?></label></td>
<td width="33%"><label><input type="radio" name="g" value="priority" <?php if ($group == 'priority') {echo 'checked="checked"';} ?> /> <?php echo $hesklang['priority']; ?></label></td>
<td width="33%">&nbsp;</td>
</tr>
</table>

</td>
</tr>

<tr>
<td class="borderTop alignMiddle"><b><?php echo $hesklang['category']; ?></b>: &nbsp; </td>
<td class="borderTop alignMiddle">
<select name="category">
<option value="0" ><?php echo $hesklang['any_cat']; ?></option>
<?php echo $category_options; ?>
</select>
</td>
</tr>

<tr>
<td class="borderTop"><b><?php echo $hesklang['display']; ?></b>: &nbsp; </td>
<td class="borderTop"><input type="text" name="limit" value="<?php echo $maxresults; ?>" size="4" /> <?php echo $hesklang['tickets_page']; ?></td>
</tr>
<tr>
<td class="borderTop alignMiddle"><b><?php echo $hesklang['order']; ?></b>: &nbsp; </td>
<td class="borderTop alignMiddle">
<label><input type="radio" name="asc" value="1" <?php if ($asc) {echo 'checked="checked"';} ?> /> <?php echo $hesklang['ascending']; ?></label>
|
<label><input type="radio" name="asc" value="0" <?php if (!$asc) {echo 'checked="checked"';} ?> /> <?php echo $hesklang['descending']; ?></label></td>
</tr>

<tr>
<td class="borderTop alignTop"><b><?php echo $hesklang['opt']; ?></b>: &nbsp; </td>
<td class="borderTop">

<label><input type="checkbox" name="cot" value="1" <?php if ($cot) {echo 'checked="checked"';} ?> /> <?php echo $hesklang['cot']; ?></label><br />
<label><input type="checkbox" name="def" value="1" /> <?php echo $hesklang['def']; ?></label> (<a href="admin_main.php?reset=1&amp;token=<?php echo hesk_token_echo(0); ?>"><?php echo $hesklang['redv']; ?></a>)

</td>

</table>

<p><input type="submit" value="<?php echo $hesklang['show_tickets']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" />
|
<input type="hidden" name="more" value="<?php echo $more ? 1 : 0 ; ?>" /><a href="javascript:void(0)" onclick="Javascript:hesk_toggleLayerDisplay('divShow');Javascript:hesk_toggleLayerDisplay('topSubmit');document.showt.more.value='0';"><?php echo $hesklang['lopt']; ?></a></p>

</div>

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

<!-- ** END SHOW TICKET FORM ** -->

<br />&nbsp;<br />

<!-- ** START SEARCH TICKETS FORM ** -->

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="7" height="7"><img src="../img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
<td class="roundcornerstop"></td>
<td><img src="../img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
<td class="roundcornersleft">&nbsp;</td>
<td valign="top">

<form action="find_tickets.php" method="get" name="findby" id="findby">
<h3 style="margin-bottom:5px">&raquo; <?php echo $hesklang['find_ticket_by']; ?></h3>


<table border="0" cellpadding="3" cellspacing="0">
<tr>
<td stlye="text-align:left">
<b><?php echo $hesklang['s_for']; ?></b>:<br />
<input type="text" name="q" size="30" <?php if (isset($q)) {echo 'value="'.$q.'"';} ?> />
</td>
<td stlye="text-align:left">
<b><?php echo $hesklang['s_in']; ?></b>:<br />
<select name="what">
<option value="trackid" <?php if ($what=='trackid') {echo 'selected="selected"';} ?> ><?php echo $hesklang['trackID']; ?></option>
<?php
if ($hesk_settings['sequential'])
{
?>
<option value="seqid" <?php if ($what=='seqid') {echo 'selected="selected"';} ?> ><?php echo $hesklang['seqid']; ?></option>
<?php
}
?>
<option value="name"    <?php if ($what=='name') {echo 'selected="selected"';} ?> ><?php echo $hesklang['name']; ?></option>
<option value="email"	<?php if ($what=='email') {echo 'selected="selected"';} ?> ><?php echo $hesklang['email']; ?></option>
<option value="subject" <?php if ($what=='subject') {echo 'selected="selected"';} ?> ><?php echo $hesklang['subject']; ?></option>
<option value="message" <?php if ($what=='message') {echo 'selected="selected"';} ?> ><?php echo $hesklang['message']; ?></option>
<?php
foreach ($hesk_settings['custom_fields'] as $k=>$v)
{
$selected = ($what == $k) ? 'selected="selected"' : '';
if ($v['use'])
{
$v['name'] = (strlen($v['name']) > 30) ? substr($v['name'],0,30) . '...' : $v['name'];
echo '<option value="'.$k.'" '.$selected.'>'.$v['name'].'</option>';
}
}
?>
<option value="notes" <?php if ($what=='notes') {echo 'selected="selected"';} ?> ><?php echo $hesklang['notes']; ?></option>
</select>
</td>
</tr>
</table>

<div id="topSubmit2" style="display:<?php echo $more2 ? 'none' : 'block' ; ?>">
&nbsp;<br />
<input type="submit" value="<?php echo $hesklang['find_ticket']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" />
|
<a href="javascript:void(0)" onclick="Javascript:hesk_toggleLayerDisplay('divShow2');Javascript:hesk_toggleLayerDisplay('topSubmit2');document.findby.more2.value='1';"><?php echo $hesklang['mopt']; ?></a>
<br />&nbsp;<br />
</div>

<div id="divShow2" style="display:<?php echo $more2 ? 'block' : 'none' ; ?>">

&nbsp;<br />

<table border="0" cellpadding="3" cellspacing="0" width="100%">
<tr>
<td class="borderTop alignMiddle" width="20%"><b><?php echo $hesklang['category']; ?></b>: &nbsp; </td>
<td class="borderTop alignMiddle" width="80%">
<select name="category">
<option value="0" ><?php echo $hesklang['any_cat']; ?></option>
<?php echo $category_options; ?>
</select>
</td>
</tr>
<tr>
<td class="borderTop alignMiddle"><b><?php echo $hesklang['date']; ?></b>: &nbsp; </td>
<td class="borderTop alignMiddle">
<input type="text" name="dt" id="dt" size="10" class="tcal" <?php if ($date_input) {echo 'value="'.$date_input.'"';} ?> />
</td>
</tr>
<tr>
<td class="borderTop alignTop"><b><?php echo $hesklang['s_incl']; ?></b>: &nbsp; </td>
<td class="borderTop">
<label><input type="checkbox" name="s_my" value="1" <?php if ($s_my[2]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_my']; ?></label>
<?php
if ($can_view_ass_others)
{
	?>
    <br />
	<label><input type="checkbox" name="s_ot" value="1" <?php if ($s_ot[2]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_ot']; ?></label>
	<?php
}

if ($can_view_unassigned)
{
	?>
    <br />
	<label><input type="checkbox" name="s_un" value="1" <?php if ($s_un[2]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['s_un']; ?></label>
	<?php
}
?>
<br />
<label><input type="checkbox" name="archive" value="1" <?php if ($archive[2]) echo 'checked="checked"'; ?> /> <?php echo $hesklang['disp_only_archived']; ?></label>
</td>
</tr>
<tr>
<td class="borderTop"><b><?php echo $hesklang['display']; ?></b>: &nbsp; </td>
<td class="borderTop"><input type="text" name="limit" value="<?php echo $maxresults; ?>" size="4" /> <?php echo $hesklang['results_page']; ?></td>
</tr>
</table>

<p><input type="submit" value="<?php echo $hesklang['find_ticket']; ?>" class="orangebutton" onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" />
|
<input type="hidden" name="more2" value="<?php echo $more2 ? 1 : 0 ; ?>" /><a href="javascript:void(0)" onclick="Javascript:hesk_toggleLayerDisplay('divShow2');Javascript:hesk_toggleLayerDisplay('topSubmit2');document.findby.more2.value='0';"><?php echo $hesklang['lopt']; ?></a></p>

</div>

</form>
</td>
<td class="roundcornersright">&nbsp;</td>
</tr>
<tr>
<td width="6"><img src="../img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
<td class="roundcornersbottom"></td>
<td width="7" height="7"><img src="../img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>

<!-- ** END SEARCH TICKETS FORM ** -->

