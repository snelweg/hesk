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

define('CALENDAR',1);

/* Check permissions for this feature */
hesk_checkPermission('can_view_tickets');

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
?>

<table style="width:100%;border:none;border-collapse:collapse;"><tr>
<td style="width:25%">&nbsp;</td>
<td style="width:50%;text-align:center"><h3><?php echo $hesklang['tickets']; ?></h3></td>
<td style="width:25%;text-align:right"><a href="new_ticket.php"><?php echo $hesklang['nti']; ?></a></td>
</tr>
</table>

<?php
/* Print the list of tickets */
$is_search = 1;
require_once(HESK_PATH . 'inc/print_tickets.inc.php');

/* Update staff default settings? */
if ( ! empty($_GET['def']))
{
	hesk_updateStaffDefaults();
}
?>

&nbsp;<br />

<?php
/* Print forms for listing and searching tickets */
require_once(HESK_PATH . 'inc/show_search_form.inc.php');
?>

<p>&nbsp;</p>
<?php

/* Print footer */
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();

?>
