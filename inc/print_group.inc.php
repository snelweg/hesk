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

/* Group tickets into tables */
if ($group == 'owner')
{
	if ($ticket['owner'] != $group_tmp)
	{
		$group_tmp = $ticket['owner'];

		if ($is_table)
		{
			echo '</table></div>';
		}

		if ($space)
		{
			echo '&nbsp;<br />';
		}

		if (empty($group_tmp) || ! isset($admins[$group_tmp]))
		{
			echo '<p>'.$hesklang['gbou'].'</p>';
			$space++;
		}
		else
		{
			if ($group_tmp == $_SESSION['id'])
			{
				echo '<p>'.$hesklang['gbom'].'</p>';
				$space++;
			}
			else
			{
				echo '<p>'.sprintf($hesklang['gboo'],$admins[$group_tmp]).'</p>';
				$space++;
			}
		}

		hesk_print_list_head();
		$is_table = 1;
	}
} // END if 'owner'

elseif ($group == 'priority')
{
	switch ($ticket['priority'])
	{
		case 0:
			$tmp = '<font class="critical">'.$hesklang['critical'].'</font>';
			break;
		case 1:
			$tmp =  '<font class="important">'.$hesklang['high'].'</font>';
			break;
		case 2:
			$tmp =  '<font class="medium">'.$hesklang['medium'].'</font>';
			break;
		default:
			$tmp =  $hesklang['low'];
	}

	if ($ticket['priority'] != $group_tmp)
	{
		$group_tmp = $ticket['priority'];

		if ($is_table)
		{
			echo '</table></div>';
		}

		if ($space)
		{
			echo '&nbsp;<br />';
		}

		echo '<p>'.$hesklang['priority'].': <b>'.$tmp.'</b></p>';
		$space++;

		hesk_print_list_head();
		$is_table = 1;
	}
} // END elseif 'priority'

else
{
	if ($ticket['category'] != $group_tmp)
	{
		$group_tmp = $ticket['category'];

		if ($is_table)
		{
			echo '</table></div>';
		}

		if ($space)
		{
			echo '&nbsp;<br />';
		}


        $tmp = isset($hesk_settings['categories'][$group_tmp]) ? $hesk_settings['categories'][$group_tmp] : '('.$hesklang['unknown'].')';

		echo '<p>'.$hesklang['category'].': <b>'.$tmp.'</b></p>';
		$space++;

		hesk_print_list_head();
		$is_table = 1;
	}
} // END else ('category')
