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

// Get all the required files and functions
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/setup_functions.inc.php');

// Print header
header('Content-Type: text/html; charset='.$hesklang['ENCODING']);

// Demo mode?
if ( defined('HESK_DEMO') )
{
	hesk_show_notice($hesklang['ddemo']);
    exit();
}

// Test type?
$test_type = hesk_POST('test');

// Test MySQL connection
if ($test_type == 'mysql')
{
	if ( hesk_testMySQL() )
	{
		hesk_show_success($hesklang['conok']);
	}
	elseif ( ! empty($mysql_log) )
	{
		hesk_show_error($mysql_error . '<br /><br /><b>' . $hesklang['mysql_said'] . ':</b> ' . $mysql_log);
	}
	else
	{
		hesk_show_error($mysql_error);
	}
}

// Test POP3 connection
elseif ($test_type == 'pop3')
{
	if ( hesk_testPOP3() )
	{
		hesk_show_success($hesklang['conok']);
	}
	else
	{
		hesk_show_error( $pop3_error . '<br /><br /><textarea name="pop3_log" rows="10" cols="60">' . $pop3_log . '</textarea>' );
	}
}

// Test SMTP connection
elseif ($test_type == 'smtp')
{
	if ( hesk_testSMTP() )
	{
		// If no username/password add a notice
		if ($set['smtp_user'] == '' && $set['smtp_user'] == '')
		{
			$hesklang['conok'] .= '<br /><br />' . $hesklang['conokn'];
		}

		hesk_show_success($hesklang['conok']);
	}
	else
	{
		hesk_show_error( $smtp_error . '<br /><br /><textarea name="smtp_log" rows="10" cols="60">' . $smtp_log . '</textarea>' );
	}
}

// Not a valid test...
else
{
	die($hesklang['attempt']);
}

exit();
?>
