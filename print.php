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
define('HESK_PATH','./');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
hesk_load_database_functions();

hesk_session_start();

/* Get the tracking ID */
$trackingID = hesk_cleanID() or die("$hesklang[int_error]: $hesklang[no_trackID]");

/* Connect to database */
hesk_dbConnect();

/* Verify email address match if needed */
if ( empty($_SESSION['id']) )
{
	hesk_verifyEmailMatch($trackingID);
}

/* Get ticket info */
$res = hesk_dbQuery("SELECT `t1`.* , `t2`.name AS `repliername`
					FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` AS `t1` LEFT JOIN `".hesk_dbEscape($hesk_settings['db_pfix'])."users` AS `t2` ON `t1`.`replierid` = `t2`.`id`
					WHERE `trackid`='".hesk_dbEscape($trackingID)."' LIMIT 1");

if (hesk_dbNumRows($res) != 1)
{
	hesk_error($hesklang['ticket_not_found']);
}
$ticket = hesk_dbFetchAssoc($res);

// Demo mode
if ( defined('HESK_DEMO') )
{
	$ticket['email'] = 'hidden@demo.com';
	$ticket['ip']	 = '127.0.0.1';
}

/* Get category name and ID */
$res = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` WHERE `id`='{$ticket['category']}' LIMIT 1");

/* If this category has been deleted use the default category with ID 1 */
if (hesk_dbNumRows($res) != 1)
{
	$res = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` WHERE `id`='1' LIMIT 1");
}
$category = hesk_dbFetchAssoc($res);

/* Get replies */
$res  = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."replies` WHERE `replyto`='{$ticket['id']}' ORDER BY `id` ASC");
$replies = hesk_dbNumRows($res);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?php echo $hesk_settings['hesk_title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $hesklang['ENCODING']; ?>">
<style type="text/css">
body, table, td, p
{
    color : black;
    font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
    font-size : <?php echo $hesk_settings['print_font_size']; ?>px;
}
table
{
	border-collapse:collapse;
}
hr
{
	border: 0;
	color: #9e9e9e;
	background-color: #9e9e9e;
	height: 1px;
	width: 100%;
	text-align: left;
}
</style>
</head>
<body onload="window.print()">

<?php
/* Ticket status */
switch ($ticket['status'])
{
	case 0:
		$ticket['status']=$hesklang['open'];
		break;
	case 1:
		$ticket['status']=$hesklang['wait_staff_reply'];
		break;
	case 2:
		$ticket['status']=$hesklang['wait_cust_reply'];
		break;
	case 4:
		$ticket['status']=$hesklang['in_progress'];
		break;
	case 5:
		$ticket['status']=$hesklang['on_hold'];
		break;
	default:
		$ticket['status']=$hesklang['closed'];
}

/* Ticket priority */
switch ($ticket['priority'])
{
	case 0:
		$ticket['priority']='<b>'.$hesklang['critical'].'</b>';
		break;
	case 1:
		$ticket['priority']='<b>'.$hesklang['high'].'</b>';
		break;
	case 2:
		$ticket['priority']=$hesklang['medium'];
		break;
	default:
		$ticket['priority']=$hesklang['low'];
}

/* Set last replier name */
if ($ticket['lastreplier'])
{
	if (empty($ticket['repliername']))
	{
		$ticket['repliername'] = $hesklang['staff'];
	}
}
else
{
	$ticket['repliername'] = $ticket['name'];
}

/* Other variables that need processing */
$ticket['dt'] = hesk_date($ticket['dt']);
$ticket['lastchange'] = hesk_date($ticket['lastchange']);
$random=mt_rand(10000,99999);

// Print ticket head
echo '
<table border="0">
<tr>
	<td>' . $hesklang['subject'] . ':</td>
	<td><b>' . $ticket['subject'] . '</b></td>
</tr>
<tr>
	<td>' . $hesklang['trackID'] . ':</td>
	<td>' . $trackingID . '</td>
</tr>
<tr>
	<td>' . $hesklang['ticket_status'] . ':</td>
	<td>' . $ticket['status'] . '</td>
</tr>
<tr>
	<td>' . $hesklang['created_on'] . ':</td>
	<td>' . $ticket['dt'] . '</td>
</tr>
<tr>
	<td>' . $hesklang['last_update'] . ':</td>
	<td>' . $ticket['lastchange'] . '</td>
</tr>
';

// Assigned to?
if ($ticket['owner'] && ! empty($_SESSION['id']) )
{
	$ticket['owner'] = hesk_getOwnerName($ticket['owner']);
	echo'
	<tr>
		<td>' . $hesklang['taso3'] . '</td>
		<td>' . $ticket['owner'] . '</td>
	</tr>
	';
}

// Continue with ticket head
echo '
<tr>
	<td>' . $hesklang['last_replier'] . ':</td>
	<td>' . $ticket['repliername'] . '</td>
</tr>
<tr>
	<td>' . $hesklang['category'] . ':</td>
	<td>' . $category['name'] . '</td>
</tr>
';

// Show IP and time worked to staff
if ( ! empty($_SESSION['id']) )
{
	echo '
	<tr>
		<td>' . $hesklang['ts'] . ':</td>
		<td>' . $ticket['time_worked'] . '</td>
	</tr>
	<tr>
		<td>' . $hesklang['ip'] . ':</td>
		<td>' . $ticket['ip'] . '</td>
	</tr>
	<tr>
		<td>' . $hesklang['email'] . ':</td>
		<td>' . $ticket['email'] . '</td>
	</tr>
	';
}

echo '
	<tr>
		<td>' . $hesklang['name'] . ':</td>
		<td>' . $ticket['name'] . '</td>
	</tr>
    ';

// Custom fields
foreach ($hesk_settings['custom_fields'] as $k=>$v)
{
	if ($v['use'])
	{
	?>
	<tr>
		<td><?php echo $v['name']; ?>:</td>
		<td><?php echo hesk_unhortenUrl($ticket[$k]); ?></td>
	</tr>
	<?php
	}
}

// Close ticket head table
echo '</table>';

// Print initial ticket message
echo '<p>' . hesk_unhortenUrl($ticket['message']) . '</p>';

// Print replies
while ($reply = hesk_dbFetchAssoc($res))
{
	$reply['dt'] = hesk_date($reply['dt']);

    echo '
    <hr />

	<table border="0">
	<tr>
		<td>' . $hesklang['date'] . ':</td>
		<td>' . $reply['dt'] . '</td>
	</tr>
	<tr>
		<td>' . $hesklang['name'] . ':</td>
		<td>' . $reply['name'] . '</td>
	</tr>
	</table>

    <p>' . hesk_unhortenUrl($reply['message']) . '</p>
    ';
}

// Print "end of ticket" message
echo $hesklang['end_ticket'];
?>

</body>
</html>
