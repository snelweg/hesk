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
hesk_checkPermission('can_view_tickets');
hesk_checkPermission('can_reply_tickets');

/* A security check */
hesk_token_check();

/* Ticket ID */
$trackingID = hesk_cleanID() or die($hesklang['int_error'].': '.$hesklang['no_trackID']);

/* Valid statuses */
$status_options = array(
	0 => $hesklang['open'],
	1 => $hesklang['wait_reply'],
	2 => $hesklang['replied'],
	3 => $hesklang['closed'],
	4 => $hesklang['in_progress'],
	5 => $hesklang['on_hold'],
);

/* New status */
$status = intval( hesk_REQUEST('s') );
if ( ! isset($status_options[$status]))
{
	hesk_process_messages($hesklang['instat'],'admin_ticket.php?track='.$trackingID.'&Refresh='.mt_rand(10000,99999),'NOTICE');
}

$locked = 0;

if ($status == 3) // Closed
{
	$action = $hesklang['ticket_been'] . ' ' . $hesklang['closed'];
    $revision = sprintf($hesklang['thist3'],hesk_date(),$_SESSION['name'].' ('.$_SESSION['user'].')');

    if ($hesk_settings['custopen'] != 1)
    {
    	$locked = 1;
    }
}
elseif ($status == 1 || $status == 2 || $status == 4 || $status == 5)
{
	$action = sprintf($hesklang['tsst'],$status_options[$status]);
    $revision = sprintf($hesklang['thist9'],hesk_date(),$status_options[$status],$_SESSION['name'].' ('.$_SESSION['user'].')');
}
else // Opened
{
	$action = $hesklang['ticket_been'] . ' ' . $hesklang['opened'];
    $revision = sprintf($hesklang['thist4'],hesk_date(),$_SESSION['name'].' ('.$_SESSION['user'].')');
}

hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` SET `status`='{$status}', `locked`='{$locked}', `history`=CONCAT(`history`,'".hesk_dbEscape($revision)."') WHERE `trackid`='".hesk_dbEscape($trackingID)."' LIMIT 1");

if (hesk_dbAffectedRows() != 1)
{
	hesk_error("$hesklang[int_error]: $hesklang[trackID_not_found].");
}

hesk_process_messages($action,'admin_ticket.php?track='.$trackingID.'&Refresh='.rand(10000,99999),'SUCCESS');
?>
