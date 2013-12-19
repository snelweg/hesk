#!/usr/bin/php -q
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
define('HESK_PATH', dirname(dirname(dirname(__FILE__))) . '/');

// Get required files and functions
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');

// Is this feature enabled?
if (empty($hesk_settings['pop3']))
{
	die($hesklang['pfd']);
}

// Email piping is enabled, get other required includes
require(HESK_PATH . 'inc/pipe_functions.inc.php');

// Get POP3 class
require(HESK_PATH . 'inc/mail/pop3.php');

// Uncomment when using SASL authentication mechanisms
# require(HESK_PATH . 'inc/mail/sasl/sasl.php');

// If a pop3 wrapper is registered un register it, we need our custom wrapper
if ( in_array('pop3', stream_get_wrappers() ) )
{
    stream_wrapper_unregister('pop3');
}

// Register the pop3 stream handler class
stream_wrapper_register('pop3', 'pop3_stream');

// Setup required variables
$pop3 = new pop3_class;
$pop3->hostname	= $hesk_settings['pop3_host_name'];
$pop3->port		= $hesk_settings['pop3_host_port'];
$pop3->tls		= $hesk_settings['pop3_tls'];
$pop3->debug	= 0;
$pop3->join_continuation_header_lines = 1;

// Connect to POP3
if(($error=$pop3->Open())=="")
{
	echo $hesk_settings['debug_mode'] ? "<pre>Connected to the POP3 server &quot;" . $pop3->hostname . "&quot;.</pre>\n" : '';

	// Authenticate
	if(($error=$pop3->Login($hesk_settings['pop3_user'], hesk_htmlspecialchars_decode($hesk_settings['pop3_password'])))=="")
	{
		echo $hesk_settings['debug_mode'] ? "<pre>User &quot;" . $hesk_settings['pop3_user'] . "&quot; logged in.</pre>\n" : '';

		// Get number of messages and total size
		if(($error=$pop3->Statistics($messages,$size))=="")
		{
			echo $hesk_settings['debug_mode'] ? "<pre>There are $messages messages in the mail box with a total of $size bytes.</pre>\n" : '';

			// If we have any messages, process them
			if($messages>0)
			{
				// Connect to the database
				hesk_dbConnect();

				for ($message = 1; $message <= $messages; $message++)
				{
					echo $hesk_settings['debug_mode'] ? "<pre>Parsing message $message of $messages.</pre>\n" : '';

					$pop3->GetConnectionName($connection_name);
					$message_file = 'pop3://'.$connection_name.'/'.$message;

					// Parse the incoming email
					$results = parser($message_file);

					// Convert email into a ticket (or new reply)
					if ( $id = hesk_email2ticket($results, 1) )
					{
						echo $hesk_settings['debug_mode'] ? "<pre>Ticket $id created/updated.</pre>\n" : '';

					}
					else
					{
						echo $hesk_settings['debug_mode'] ? "<pre>Ticket NOT inserted - may be duplicate, blocked or an error.</pre>\n" : '';
					}

					// Queue message to be deleted on connection close
					if ( ! $hesk_settings['pop3_keep'])
                    {
                    	$pop3->DeleteMessage($message);
                    }

					echo $hesk_settings['debug_mode'] ? "<br /><br />\n\n" : '';
				}
			}

			// Disconnect from the server - this also deletes queued messages
			if($error == "" && ($error=$pop3->Close()) == "")
			{
				echo $hesk_settings['debug_mode'] ? "<pre>Disconnected from the POP3 server &quot;" . $pop3->hostname . "&quot;.</pre>\n" : '';
			}
		}
	}
}

// Any error messages?
if($error != '')
{
	echo "<h2>Error: " . hesk_htmlspecialchars($error) . "</h2>";
}

return NULL;
