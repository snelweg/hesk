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

// Get all the required files and functions
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
hesk_load_database_functions();

// Is rating enabled?
if ( ! $hesk_settings['rating'])
{
	die($hesklang['rdis']);
}

// Rating value
$rating = intval( hesk_GET('rating', 0) );

// Rating can only be 1 or 5
if ($rating != 1 && $rating != 5)
{
	die($hesklang['attempt']);
}

// Reply ID
$reply_id = intval( hesk_GET('id', 0) ) or die($hesklang['attempt']);

// Ticket tracking ID
$trackingID = hesk_cleanID() or die($hesklang['attempt']);

// Connect to database
hesk_dbConnect();

// Get reply info to verify tickets match
$result = hesk_dbQuery("SELECT `replyto`,`rating`,`staffid` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."replies` WHERE `id`='{$reply_id}' LIMIT 1");
$reply  = hesk_dbFetchAssoc($result);

// Does the ticket ID match the one in the request?
$result = hesk_dbQuery("SELECT `trackid` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` WHERE `id`='{$reply['replyto']}' LIMIT 1");
// -> Ticket found?
if (hesk_dbNumRows($result) != 1)
{
	die($hesklang['attempt']);
}
// -> Does the tracking ID match?
$ticket = hesk_dbFetchAssoc($result);
if ($ticket['trackid'] != $trackingID)
{
	die($hesklang['attempt']);
}

// OK, tracking ID matches. Now check if this reply has already been rated
if ( ! empty($reply['rating']))
{
	die($hesklang['ar']);
}

// Update reply rating
hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."replies` SET `rating`='{$rating}' WHERE `id`='{$reply_id}' LIMIT 1");

// Also update staff rating
hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."users` SET `rating`=((`rating`*(`ratingpos`+`ratingneg`))+{$rating})/(`ratingpos`+`ratingneg`+1), " .
			($rating == 5 ? '`ratingpos`=`ratingpos`+1 ' : '`ratingneg`=`ratingneg`+1 ') .
            "WHERE `id`='{$reply['staffid']}'");

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header('Content-type: text/plain');
if ($rating == 5)
{
	echo $hesklang['rh'];
}
else
{
	echo $hesklang['rnh'];
}
exit();
?>
