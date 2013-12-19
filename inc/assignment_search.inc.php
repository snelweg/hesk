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

/* Assignment */
// -> SELF
$s_my[$fid] = empty($_GET['s_my']) ? 0 : 1;
// -> OTHERS
$s_ot[$fid] = empty($_GET['s_ot']) ? 0 : 1;
// -> UNASSIGNED
$s_un[$fid] = empty($_GET['s_un']) ? 0 : 1;

// -> Setup SQL based on selected ticket assignments

/* Make sure at least one is chosen */
if ( ! $s_my[$fid] && ! $s_ot[$fid] && ! $s_un[$fid])
{
	$s_my[$fid] = 1;
	$s_ot[$fid] = 1;
	$s_un[$fid] = 1;
	if (!defined('MAIN_PAGE'))
	{
		hesk_show_notice($hesklang['e_nose']);
	}
}

/* If the user doesn't have permission to view assigned to others block those */
if ( ! hesk_checkPermission('can_view_ass_others',0))
{
	$s_ot[$fid] = 0;
}

/* If the user doesn't have permission to view unassigned tickets block those */
if ( ! hesk_checkPermission('can_view_unassigned',0))
{
	$s_un[$fid] = 0;
}

/* Process assignments */
if ( ! $s_my[$fid] || ! $s_ot[$fid] || ! $s_un[$fid])
{
	if ($s_my[$fid] && $s_ot[$fid])
    {
    	// All but unassigned
    	$sql .= " AND `owner` > 0 ";
    }
    elseif ($s_my[$fid] && $s_un[$fid])
    {
    	// My tickets + unassigned
    	$sql .= " AND `owner` IN ('0', '" . intval($_SESSION['id']) . "') ";
    }
    elseif ($s_ot[$fid] && $s_un[$fid])
    {
    	// Assigned to others + unassigned
    	$sql .= " AND `owner` != '" . intval($_SESSION['id']) . "' ";
    }
    elseif ($s_my[$fid])
    {
    	// Assigned to me only
    	$sql .= " AND `owner` = '" . intval($_SESSION['id']) . "' ";
    }
    elseif ($s_ot[$fid])
    {
    	// Assigned to others
    	$sql .= " AND `owner` NOT IN ('0', '" . intval($_SESSION['id']) . "') ";
    }
    elseif ($s_un[$fid])
    {
    	// Only unassigned
    	$sql .= " AND `owner` = 0 ";
    }
}
