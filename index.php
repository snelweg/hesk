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

// What should we do?
$action = hesk_REQUEST('a');

switch ($action)
{
	case 'add':
		hesk_session_start();
        print_add_ticket();
	    break;

	case 'forgot_tid':
		hesk_session_start();
        forgot_tid();
	    break;

	default:
		print_start();
}

// Print footer
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();

/*** START FUNCTIONS ***/

function print_add_ticket()
{
	global $hesk_settings, $hesklang;

	// Auto-focus first empty or error field
	define('AUTOFOCUS', true);

	// Varibles for coloring the fields in case of errors
	if ( ! isset($_SESSION['iserror']))
	{
		$_SESSION['iserror'] = array();
	}

	if ( ! isset($_SESSION['isnotice']))
	{
		$_SESSION['isnotice'] = array();
	}

    if ( ! isset($_SESSION['c_category']))
    {
    	$_SESSION['c_category'] = 0;
    }

	hesk_cleanSessionVars('already_submitted');

	// Print header
	$hesk_settings['tmp_title'] = $hesk_settings['hesk_title'] . ' - ' . $hesklang['submit_ticket'];
	require_once(HESK_PATH . 'inc/header.inc.php');
	?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="3"><img src="img/headerleftsm.jpg" width="3" height="25" alt="" /></td>
<td class="headersm"><?php hesk_showTopBar($hesklang['submit_ticket']); ?></td>
<td width="3"><img src="img/headerrightsm.jpg" width="3" height="25" alt="" /></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td><span class="smaller"><a href="<?php echo $hesk_settings['site_url']; ?>" class="smaller"><?php echo $hesk_settings['site_title']; ?></a> &gt;
<a href="<?php echo $hesk_settings['hesk_url']; ?>" class="smaller"><?php echo $hesk_settings['hesk_title']; ?></a>
&gt; <?php echo $hesklang['submit_ticket']; ?></span></td>
</tr>
</table>

</td>
</tr>
<tr>
<td>

<?php
// This will handle error, success and notice messages
hesk_handle_messages();
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>
    <!-- START FORM -->

	<p style="text-align:center"><?php echo $hesklang['use_form_below']; ?> <font class="important"> *</font></p>

	<form method="post" action="submit_ticket.php?submit=1" name="form1" enctype="multipart/form-data">

	<!-- Contact info -->
	<table border="0" width="100%">
	<tr>
	<td style="text-align:right" width="150"><?php echo $hesklang['name']; ?>: <font class="important">*</font></td>
	<td width="80%"><input type="text" name="name" size="40" maxlength="30" value="<?php if (isset($_SESSION['c_name'])) {echo stripslashes(hesk_input($_SESSION['c_name']));} ?>" <?php if (in_array('name',$_SESSION['iserror'])) {echo ' class="isError" ';} ?> /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="150"><?php echo $hesklang['email']; ?>: <font class="important">*</font></td>
	<td width="80%"><input type="text" name="email" size="40" maxlength="255" value="<?php if (isset($_SESSION['c_email'])) {echo stripslashes(hesk_input($_SESSION['c_email']));} ?>" <?php if (in_array('email',$_SESSION['iserror'])) {echo ' class="isError" ';} elseif (in_array('email',$_SESSION['isnotice'])) {echo ' class="isNotice" ';} ?> <?php if($hesk_settings['detect_typos']) { echo ' onblur="Javascript:hesk_suggestEmail(0)"'; } ?> /></td>
	</tr>
    <?php
    if ($hesk_settings['confirm_email'])
    {
	    ?>
		<tr>
		<td style="text-align:right" width="150"><?php echo $hesklang['confemail']; ?>: <font class="important">*</font></td>
		<td width="80%"><input type="text" name="email2" size="40" maxlength="255" value="<?php if (isset($_SESSION['c_email2'])) {echo stripslashes(hesk_input($_SESSION['c_email2']));} ?>" <?php if (in_array('email2',$_SESSION['iserror'])) {echo ' class="isError" ';} ?> /></td>
		</tr>
	    <?php
    } // End if $hesk_settings['confirm_email']
    ?>
	</table>

	<div id="email_suggestions"></div>

	<hr />

	<!-- Department and priority -->

    <?php
    $is_table = 0;

	hesk_load_database_functions();

    // Get categories
	hesk_dbConnect();
	$res = hesk_dbQuery("SELECT `id`, `name` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."categories` WHERE `type`='0' ORDER BY `cat_order` ASC");

    if (hesk_dbNumRows($res) == 1)
    {
    	// Only 1 public category, no need for a select box
    	$row = hesk_dbFetchAssoc($res);
		echo '<input type="hidden" name="category" value="'.$row['id'].'" />';
    }
    elseif (hesk_dbNumRows($res) < 1)
    {
    	// No public categories, set it to default one
		echo '<input type="hidden" name="category" value="1" />';
    }
    else
    {
		// Is the category ID preselected?
		if ( ! empty($_GET['catid']) )
		{
			$_SESSION['c_category'] = intval( hesk_GET('catid') );
		}

		// List available categories
		$is_table = 1;
		?>
		<table border="0" width="100%">
		<tr>
		<td style="text-align:right" width="150"><?php echo $hesklang['category']; ?>: <font class="important">*</font></td>
		<td width="80%"><select name="category" <?php if (in_array('category',$_SESSION['iserror'])) {echo ' class="isError" ';} ?> >
		<?php
		while ($row = hesk_dbFetchAssoc($res))
		{
			echo '<option value="' . $row['id'] . '"' . (($_SESSION['c_category'] == $row['id']) ? ' selected="selected"' : '') . '>' . $row['name'] . '</option>';
		}
		?>
		</select></td>
		</tr>
        <?php
    }

	/* Can customer assign urgency? */
	if ($hesk_settings['cust_urgency'])
	{
		if ( ! $is_table)
		{
			echo '<table border="0" width="100%">';
            $is_table = 1;
		}
		?>
		<tr>
		<td style="text-align:right" width="150"><?php echo $hesklang['priority']; ?>: <font class="important">*</font></td>
		<td width="80%"><select name="priority" <?php if (in_array('priority',$_SESSION['iserror'])) {echo ' class="isError" ';} ?> >
		<option value="3" <?php if(isset($_SESSION['c_priority']) && $_SESSION['c_priority']==3) {echo 'selected="selected"';} ?>><?php echo $hesklang['low']; ?></option>
		<option value="2" <?php if(isset($_SESSION['c_priority']) && $_SESSION['c_priority']==2) {echo 'selected="selected"';} ?>><?php echo $hesklang['medium']; ?></option>
		<option value="1" <?php if(isset($_SESSION['c_priority']) && $_SESSION['c_priority']==1) {echo 'selected="selected"';} ?>><?php echo $hesklang['high']; ?></option>
		</select></td>
		</tr>
		<?php
	}

	/* Need to close the table? */
	if ($is_table)
	{
		echo '</table> <hr />';
	}
	?>
	<!-- START CUSTOM BEFORE -->
	<?php

	/* custom fields BEFORE comments */

	$print_table = 0;

	foreach ($hesk_settings['custom_fields'] as $k=>$v)
	{
		if ($v['use'] && $v['place']==0)
	    {
	    	if ($print_table == 0)
	        {
	        	echo '<table border="0" width="100%">';
	        	$print_table = 1;
	        }

			$v['req'] = $v['req'] ? '<font class="important">*</font>' : '';

			if ($v['type'] == 'checkbox')
            {
            	$k_value = array();
                if (isset($_SESSION["c_$k"]) && is_array($_SESSION["c_$k"]))
                {
	                foreach ($_SESSION["c_$k"] as $myCB)
	                {
	                	$k_value[] = stripslashes(hesk_input($myCB));
	                }
                }
            }
            elseif (isset($_SESSION["c_$k"]))
            {
            	$k_value  = stripslashes(hesk_input($_SESSION["c_$k"]));
            }
            else
            {
            	$k_value  = '';
            }

	        switch ($v['type'])
	        {
	        	/* Radio box */
	        	case 'radio':
					echo '
					<tr>
					<td style="text-align:right" width="150" valign="top">'.$v['name'].': '.$v['req'].'</td>
	                <td width="80%">';

	            	$options = explode('#HESK#',$v['value']);
                    $cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

	                foreach ($options as $option)
	                {

		            	if (strlen($k_value) == 0 || $k_value == $option)
		                {
	                    	$k_value = $option;
							$checked = 'checked="checked"';
	                    }
	                    else
	                    {
	                    	$checked = '';
	                    }

	                	echo '<label><input type="radio" name="'.$k.'" value="'.$option.'" '.$checked.' '.$cls.' /> '.$option.'</label><br />';
	                }

	                echo '</td>
					</tr>
					';
	            break;

	            /* Select drop-down box */
	            case 'select':

                	$cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

					echo '
					<tr>
					<td style="text-align:right" width="150">'.$v['name'].': '.$v['req'].'</td>
	                <td width="80%"><select name="'.$k.'" '.$cls.'>';

	            	$options = explode('#HESK#',$v['value']);

	                foreach ($options as $option)
	                {

		            	if (strlen($k_value) == 0 || $k_value == $option)
		                {
	                    	$k_value = $option;
	                        $selected = 'selected="selected"';
		                }
	                    else
	                    {
	                    	$selected = '';
	                    }

	                	echo '<option '.$selected.'>'.$option.'</option>';
	                }

	                echo '</select></td>
					</tr>
					';
	            break;

	            /* Checkbox */
	        	case 'checkbox':
					echo '
					<tr>
					<td style="text-align:right" width="150" valign="top">'.$v['name'].': '.$v['req'].'</td>
	                <td width="80%">';

	            	$options = explode('#HESK#',$v['value']);
                    $cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

	                foreach ($options as $option)
	                {

		            	if (in_array($option,$k_value))
		                {
							$checked = 'checked="checked"';
	                    }
	                    else
	                    {
	                    	$checked = '';
	                    }

	                	echo '<label><input type="checkbox" name="'.$k.'[]" value="'.$option.'" '.$checked.' '.$cls.' /> '.$option.'</label><br />';
	                }

	                echo '</td>
					</tr>
					';
	            break;

	            /* Large text box */
	            case 'textarea':
	                $size = explode('#',$v['value']);
                    $size[0] = empty($size[0]) ? 5 : intval($size[0]);
                    $size[1] = empty($size[1]) ? 30 : intval($size[1]);

                    $cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

					echo '
					<tr>
					<td style="text-align:right" width="150" valign="top">'.$v['name'].': '.$v['req'].'</td>
					<td width="80%"><textarea name="'.$k.'" rows="'.$size[0].'" cols="'.$size[1].'" '.$cls.'>'.$k_value.'</textarea></td>
					</tr>
	                ';
	            break;

	            /* Default text input */
	            default:
                	if (strlen($k_value) != 0)
                    {
                    	$v['value'] = $k_value;
                    }

                    $cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

					echo '
					<tr>
					<td style="text-align:right" width="150">'.$v['name'].': '.$v['req'].'</td>
					<td width="80%"><input type="text" name="'.$k.'" size="40" maxlength="'.$v['maxlen'].'" value="'.$v['value'].'" '.$cls.' /></td>
					</tr>
					';
	        }
	    }
	}

	/* If table was started we need to close it */
	if ($print_table)
	{
		echo '</table> <hr />';
		$print_table = 0;
	}
	?>
	<!-- END CUSTOM BEFORE -->

	<!-- ticket info -->
	<table border="0" width="100%">
	<tr>
	<td style="text-align:right" width="150"><?php echo $hesklang['subject']; ?>: <font class="important">*</font></td>
	<td width="80%"><input type="text" name="subject" size="40" maxlength="40" value="<?php if (isset($_SESSION['c_subject'])) {echo stripslashes(hesk_input($_SESSION['c_subject']));} ?>" <?php if (in_array('subject',$_SESSION['iserror'])) {echo ' class="isError" ';} ?> /></td>
	</tr>
	<tr>
	<td style="text-align:right" width="150" valign="top"><?php echo $hesklang['message']; ?>: <font class="important">*</font></td>
	<td width="80%"><textarea name="message" rows="12" cols="60" <?php if (in_array('message',$_SESSION['iserror'])) {echo ' class="isError" ';} ?> ><?php if (isset($_SESSION['c_message'])) {echo stripslashes(hesk_input($_SESSION['c_message']));} ?></textarea>

		<!-- START KNOWLEDGEBASE SUGGEST -->
		<?php
		if ($hesk_settings['kb_enable'] && $hesk_settings['kb_recommendanswers'])
		{
			?>
			<div id="kb_suggestions" style="display:none">
            <br />&nbsp;<br />
			<img src="img/loading.gif" width="24" height="24" alt="" border="0" style="vertical-align:text-bottom" /> <i><?php echo $hesklang['lkbs']; ?></i>
			</div>

			<script language="Javascript" type="text/javascript"><!--
			hesk_suggestKB();
			//-->
			</script>
			<?php
		}
		?>
		<!-- END KNOWLEDGEBASE SUGGEST -->
    </td>
	</tr>
	</table>

	<!-- START CUSTOM AFTER -->
	<?php
	/* custom fields AFTER comments */
	$print_table = 0;

	foreach ($hesk_settings['custom_fields'] as $k=>$v)
	{
		if ($v['use'] && $v['place'])
	    {
	    	if ($print_table == 0)
	        {
	        	echo '
                <hr />
                <table border="0" width="100%">
                ';
	        	$print_table = 1;
	        }

			$v['req'] = $v['req'] ? '<font class="important">*</font>' : '';

			if ($v['type'] == 'checkbox')
            {
            	$k_value = array();
                if (isset($_SESSION["c_$k"]) && is_array($_SESSION["c_$k"]))
                {
	                foreach ($_SESSION["c_$k"] as $myCB)
	                {
	                	$k_value[] = stripslashes(hesk_input($myCB));
	                }
                }
            }
            elseif (isset($_SESSION["c_$k"]))
            {
            	$k_value  = stripslashes(hesk_input($_SESSION["c_$k"]));
            }
            else
            {
            	$k_value  = '';
            }


	        switch ($v['type'])
	        {
	        	/* Radio box */
	        	case 'radio':
					echo '
					<tr>
					<td style="text-align:right" width="150" valign="top">'.$v['name'].': '.$v['req'].'</td>
	                <td width="80%">';

	            	$options = explode('#HESK#',$v['value']);
                    $cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

	                foreach ($options as $option)
	                {

		            	if (strlen($k_value) == 0 || $k_value == $option)
		                {
	                    	$k_value = $option;
							$checked = 'checked="checked"';
	                    }
	                    else
	                    {
	                    	$checked = '';
	                    }

	                	echo '<label><input type="radio" name="'.$k.'" value="'.$option.'" '.$checked.' '.$cls.' /> '.$option.'</label><br />';
	                }

	                echo '</td>
					</tr>
					';
	            break;

	            /* Select drop-down box */
	            case 'select':

                	$cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

					echo '
					<tr>
					<td style="text-align:right" width="150">'.$v['name'].': '.$v['req'].'</td>
	                <td width="80%"><select name="'.$k.'" '.$cls.'>';

	            	$options = explode('#HESK#',$v['value']);

	                foreach ($options as $option)
	                {

		            	if (strlen($k_value) == 0 || $k_value == $option)
		                {
	                    	$k_value = $option;
	                        $selected = 'selected="selected"';
		                }
	                    else
	                    {
	                    	$selected = '';
	                    }

	                	echo '<option '.$selected.'>'.$option.'</option>';
	                }

	                echo '</select></td>
					</tr>
					';
	            break;

	            /* Checkbox */
	        	case 'checkbox':
					echo '
					<tr>
					<td style="text-align:right" width="150" valign="top">'.$v['name'].': '.$v['req'].'</td>
	                <td width="80%">';

	            	$options = explode('#HESK#',$v['value']);
                    $cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

	                foreach ($options as $option)
	                {

		            	if (in_array($option,$k_value))
		                {
							$checked = 'checked="checked"';
	                    }
	                    else
	                    {
	                    	$checked = '';
	                    }

	                	echo '<label><input type="checkbox" name="'.$k.'[]" value="'.$option.'" '.$checked.' '.$cls.' /> '.$option.'</label><br />';
	                }

	                echo '</td>
					</tr>
					';
	            break;

	            /* Large text box */
	            case 'textarea':
	                $size = explode('#',$v['value']);
                    $size[0] = empty($size[0]) ? 5 : intval($size[0]);
                    $size[1] = empty($size[1]) ? 30 : intval($size[1]);

                    $cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

					echo '
					<tr>
					<td style="text-align:right" width="150" valign="top">'.$v['name'].': '.$v['req'].'</td>
					<td width="80%"><textarea name="'.$k.'" rows="'.$size[0].'" cols="'.$size[1].'" '.$cls.'>'.$k_value.'</textarea></td>
					</tr>
	                ';
	            break;

	            /* Default text input */
	            default:
                	if (strlen($k_value) != 0)
                    {
                    	$v['value'] = $k_value;
                    }

                    $cls = in_array($k,$_SESSION['iserror']) ? ' class="isError" ' : '';

					echo '
					<tr>
					<td style="text-align:right" width="150">'.$v['name'].': '.$v['req'].'</td>
					<td width="80%"><input type="text" name="'.$k.'" size="40" maxlength="'.$v['maxlen'].'" value="'.$v['value'].'" '.$cls.' /></td>
					</tr>
					';
	        }
	    }
	}

	/* If table was started we need to close it */
	if ($print_table)
	{
		echo '</table>';
		$print_table = 0;
	}
	?>
	<!-- END CUSTOM AFTER -->

	<?php
	/* attachments */
	if ($hesk_settings['attachments']['use'])
    {
	?>
    <hr />

	<table border="0" width="100%">
	<tr>
	<td style="text-align:right" width="150" valign="top"><?php echo $hesklang['attachments']; ?>:</td>
	<td width="80%" valign="top">
	<?php
	for ($i=1;$i<=$hesk_settings['attachments']['max_number'];$i++)
    {
    	$cls = ($i == 1 && in_array('attachments',$_SESSION['iserror'])) ? ' class="isError" ' : '';
		echo '<input type="file" name="attachment['.$i.']" size="50" '.$cls.' /><br />';
	}
	?>
	<a href="file_limits.php" target="_blank" onclick="Javascript:hesk_window('file_limits.php',250,500);return false;"><?php echo $hesklang['ful']; ?></a>

	</td>
	</tr>
	</table>
	<?php
	}

	if ($hesk_settings['question_use'] || $hesk_settings['secimg_use'])
    {
		?>

        <hr />

        <!-- Security checks -->
		<table border="0" width="100%">
		<?php
		if ($hesk_settings['question_use'])
	    {
			?>
			<tr>
			<td style="text-align:right;vertical-align:top" width="150"><?php echo $hesklang['verify_q']; ?> <font class="important">*</font></td>
			<td width="80%">
            <?php
        	$value = '';
        	if (isset($_SESSION['c_question']))
            {
	        	$value = stripslashes(hesk_input($_SESSION['c_question']));
            }
            $cls = in_array('question',$_SESSION['iserror']) ? ' class="isError" ' : '';
		    echo $hesk_settings['question_ask'].'<br /><input type="text" name="question" size="20" value="'.$value.'" '.$cls.'  />';
            ?><br />&nbsp;
	        </td>
			</tr>
            <?php
		}

		if ($hesk_settings['secimg_use'])
	    {
			?>
			<tr>
			<td style="text-align:right;vertical-align:top" width="150"><?php echo $hesklang['verify_i']; ?> <font class="important">*</font></td>
			<td width="80%">
			<?php
			// SPAM prevention verified for this session
			if (isset($_SESSION['img_verified']))
			{
				echo '<img src="'.HESK_PATH.'img/success.png" width="16" height="16" border="0" alt="" style="vertical-align:text-bottom" /> '.$hesklang['vrfy'];
			}
			// Not verified yet, should we use Recaptcha?
			elseif ($hesk_settings['recaptcha_use'])
			{
				?>
				<script type="text/javascript">
				var RecaptchaOptions = {
				theme : '<?php echo ( isset($_SESSION['iserror']) && in_array('mysecnum',$_SESSION['iserror']) ) ? 'red' : 'white'; ?>',
				custom_translations : {
					visual_challenge : "<?php echo hesk_slashJS($hesklang['visual_challenge']); ?>",
					audio_challenge : "<?php echo hesk_slashJS($hesklang['audio_challenge']); ?>",
					refresh_btn : "<?php echo hesk_slashJS($hesklang['refresh_btn']); ?>",
					instructions_visual : "<?php echo hesk_slashJS($hesklang['instructions_visual']); ?>",
					instructions_context : "<?php echo hesk_slashJS($hesklang['instructions_context']); ?>",
					instructions_audio : "<?php echo hesk_slashJS($hesklang['instructions_audio']); ?>",
					help_btn : "<?php echo hesk_slashJS($hesklang['help_btn']); ?>",
					play_again : "<?php echo hesk_slashJS($hesklang['play_again']); ?>",
					cant_hear_this : "<?php echo hesk_slashJS($hesklang['cant_hear_this']); ?>",
					incorrect_try_again : "<?php echo hesk_slashJS($hesklang['incorrect_try_again']); ?>",
					image_alt_text : "<?php echo hesk_slashJS($hesklang['image_alt_text']); ?>",
				},
				};
				</script>
				<?php
				require(HESK_PATH . 'inc/recaptcha/recaptchalib.php');
				echo recaptcha_get_html($hesk_settings['recaptcha_public_key'], null, $hesk_settings['recaptcha_ssl']);
			}
			// At least use some basic PHP generated image (better than nothing)
			else
			{
				$cls = in_array('mysecnum',$_SESSION['iserror']) ? ' class="isError" ' : '';

				echo $hesklang['sec_enter'].'<br />&nbsp;<br /><img src="print_sec_img.php?'.rand(10000,99999).'" width="150" height="40" alt="'.$hesklang['sec_img'].'" title="'.$hesklang['sec_img'].'" border="1" name="secimg" style="vertical-align:text-bottom" /> '.
				'<a href="javascript:void(0)" onclick="javascript:document.form1.secimg.src=\'print_sec_img.php?\'+ ( Math.floor((90000)*Math.random()) + 10000);"><img src="img/reload.png" height="24" width="24" alt="'.$hesklang['reload'].'" title="'.$hesklang['reload'].'" border="0" style="vertical-align:text-bottom" /></a>'.
				'<br />&nbsp;<br /><input type="text" name="mysecnum" size="20" maxlength="5" '.$cls.' />';
			}
			?>
			</td>
			</tr>
			<?php
		}
		?>
		</table>

    <?php
    }
	?>

	<!-- Submit -->
    <?php
    if ($hesk_settings['submit_notice'])
    {
	    ?>

	    <hr />

		<div align="center">
		<table border="0">
		<tr>
		<td>

	    <b><?php echo $hesklang['before_submit']; ?></b>
	    <ul>
	    <li><?php echo $hesklang['all_info_in']; ?>.</li>
		<li><?php echo $hesklang['all_error_free']; ?>.</li>
	    </ul>


		<b><?php echo $hesklang['we_have']; ?>:</b>
	    <ul>
	    <li><?php echo hesk_htmlspecialchars($_SERVER['REMOTE_ADDR']).' '.$hesklang['recorded_ip']; ?></li>
		<li><?php echo $hesklang['recorded_time']; ?></li>
		</ul>

		<p align="center"><input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
	    <input type="submit" value="<?php echo $hesklang['sub_ticket']; ?>" class="orangebutton"  onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /></p>

	    </td>
		</tr>
		</table>
		</div>
	    <?php
    } // End IF submit_notice
    else
    {
	    ?>
        &nbsp;<br />&nbsp;<br />
		<table border="0" width="100%">
		<tr>
		<td style="text-align:right" width="150">&nbsp;</td>
		<td width="80%"><input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
	    <input type="submit" value="<?php echo $hesklang['sub_ticket']; ?>" class="orangebutton"  onmouseover="hesk_btn(this,'orangebuttonover');" onmouseout="hesk_btn(this,'orangebutton');" /><br />
	    &nbsp;<br />&nbsp;</td>
		</tr>
		</table>
	    <?php
    } // End ELSE submit_notice
    ?>

	</form>

    <!-- END FORM -->
	</td>
	<td class="roundcornersright">&nbsp;</td>
</tr>
<tr>
	<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>

<?php

hesk_cleanSessionVars('iserror');
hesk_cleanSessionVars('isnotice');

} // End print_add_ticket()


function print_start()
{
	global $hesk_settings, $hesklang;

	if ($hesk_settings['kb_enable'])
	{
        require(HESK_PATH . 'inc/knowledgebase_functions.inc.php');
		hesk_load_database_functions();
	    hesk_dbConnect();
	}

	/* Print header */
	require_once(HESK_PATH . 'inc/header.inc.php');

	?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="3"><img src="img/headerleftsm.jpg" width="3" height="25" alt="" /></td>
<td class="headersm"><?php hesk_showTopBar($hesk_settings['hesk_title']); ?></td>
<td width="3"><img src="img/headerrightsm.jpg" width="3" height="25" alt="" /></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td><span class="smaller"><a href="<?php echo $hesk_settings['site_url']; ?>" class="smaller"><?php echo $hesk_settings['site_title']; ?></a> &gt;
<?php echo $hesk_settings['hesk_title']; ?></span>
</td>

	<?php
    // Print small search box
    if ($hesk_settings['kb_enable'])
    {
    	hesk_kbSearchSmall();
    }
	?>
    
</tr>
</table>

</td>
</tr>
<tr>
<td>

	<?php
	// Print large search box
    if ($hesk_settings['kb_enable'])
    {
		hesk_kbSearchLarge();
    }
    // Knowledgebase disabled, print an empty line for formatting
    else
    {
    	echo '&nbsp;';
    }
	?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%">
<!-- START SUBMIT -->
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
		<td class="roundcornersleft">&nbsp;</td>
		<td>
	    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	    <tr>
	    	<td width="1"><img src="img/newticket.png" alt="" width="60" height="60" /></td>
	        <td>
	        <p><b><a href="index.php?a=add"><?php echo $hesklang['sub_support']; ?></a></b><br />
            <?php echo $hesklang['open_ticket']; ?></p>
	        </td>
	    </tr>
	    </table>
		</td>
		<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
		<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornersbottom"></td>
		<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
	</table>
<!-- END SUBMIT -->
</td>
<td width="1"><img src="img/blank.gif" width="5" height="1" alt="" /></td>
<td width="50%">
<!-- START VIEW -->
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
		<td class="roundcornersleft">&nbsp;</td>
		<td>
	    <table width="100%" border="0" cellspacing="0" cellpadding="0">
	    <tr>
	    	<td width="1"><img src="img/existingticket.png" alt="" width="60" height="60" /></td>
	        <td>
	        <p><b><a href="ticket.php"><?php echo $hesklang['view_existing']; ?></a></b><br />
            <?php echo $hesklang['vet']; ?></p>
	        </td>
	    </tr>
	    </table>
		</td>
		<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
		<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornersbottom"></td>
		<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
	</table>
<!-- END VIEW -->
</td>
</tr>
</table>

<?php
if ($hesk_settings['kb_enable'])
{
	?>
	<br />

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornerstop"></td>
		<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
	</tr>
	<tr>
		<td class="roundcornersleft">&nbsp;</td>
		<td>

        <p><span class="homepageh3"><?php echo $hesklang['kb_text']; ?></span></p>

        <?php

        /* Get list of top articles */
        hesk_kbTopArticles($hesk_settings['kb_index_popart']);

        /* Get list of latest articles */
        hesk_kbLatestArticles($hesk_settings['kb_index_latest']);

        ?>

        <p>&raquo; <b><a href="knowledgebase.php"><?php echo $hesklang['viewkb']; ?></a></b></p>

		</td>
		<td class="roundcornersright">&nbsp;</td>
	</tr>
	<tr>
		<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
		<td class="roundcornersbottom"></td>
		<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
	</tr>
	</table>

    <br />
	<?php
}
// Knowledgebase disabled, let's just print some blank lines so page looks better
else
{
	?>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<?php
}

	// Show a link to admin panel?
	if ($hesk_settings['alink'])
	{
		?>
		<p style="text-align:center"><a href="<?php echo $hesk_settings['admin_dir']; ?>/" class="smaller"><?php echo $hesklang['ap']; ?></a></p>
		<?php
	}

} // End print_start()


function forgot_tid()
{
	global $hesk_settings, $hesklang;

	require(HESK_PATH . 'inc/email_functions.inc.php');

	$email = hesk_validateEmail( hesk_POST('email'), 'ERR' ,0) or hesk_process_messages($hesklang['enter_valid_email'],'ticket.php?remind=1');

	/* Prepare ticket statuses */
	$my_status = array(
	    0 => $hesklang['open'],
	    1 => $hesklang['wait_staff_reply'],
	    2 => $hesklang['wait_cust_reply'],
	    3 => $hesklang['closed'],
	    4 => $hesklang['in_progress'],
	    5 => $hesklang['on_hold'],
	);

	/* Get ticket(s) from database */
	hesk_load_database_functions();
	hesk_dbConnect();

    // Get tickets from the database
	$res = hesk_dbQuery('SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'tickets` FORCE KEY (`statuses`) WHERE ' . ($hesk_settings['open_only'] ? "`status` IN ('0','1','2','4','5') AND " : '') . ' ' . hesk_dbFormatEmail($email) . ' ORDER BY `status` ASC, `lastchange` DESC ');

	$num = hesk_dbNumRows($res);
	if ($num < 1)
	{
		if ($hesk_settings['open_only'])
        {
        	hesk_process_messages($hesklang['noopen'],'ticket.php?remind=1&e='.$email);
        }
        else
        {
        	hesk_process_messages($hesklang['tid_not_found'],'ticket.php?remind=1&e='.$email);
        }
	}

	$tid_list = '';
	$name = '';

    $email_param = $hesk_settings['email_view_ticket'] ? '&e='.rawurlencode($email) : '';

	while ($my_ticket=hesk_dbFetchAssoc($res))
	{
		$name = $name ? $name : hesk_msgToPlain($my_ticket['name'], 1, 0);
$tid_list .= "
$hesklang[trackID]: "	. $my_ticket['trackid'] . "
$hesklang[subject]: "	. hesk_msgToPlain($my_ticket['subject'], 1, 0) . "
$hesklang[status]: "	. $my_status[$my_ticket['status']] . "
$hesk_settings[hesk_url]/ticket.php?track={$my_ticket['trackid']}{$email_param}
";
	}

	/* Get e-mail message for customer */
	$msg = hesk_getEmailMessage('forgot_ticket_id','',0,0,1);
	$msg = str_replace('%%NAME%%',			$name,												$msg);
	$msg = str_replace('%%NUM%%',			$num,												$msg);
	$msg = str_replace('%%LIST_TICKETS%%',	$tid_list,											$msg);
	$msg = str_replace('%%SITE_TITLE%%',	hesk_msgToPlain($hesk_settings['site_title'], 1),	$msg);
	$msg = str_replace('%%SITE_URL%%',		$hesk_settings['site_url'],							$msg);

    $subject = hesk_getEmailSubject('forgot_ticket_id');

	/* Send e-mail */
	hesk_mail($email, $subject, $msg);

	/* Show success message */
	$tmp  = '<b>'.$hesklang['tid_sent'].'!</b>';
	$tmp .= '<br />&nbsp;<br />'.$hesklang['tid_sent2'].'.';
	$tmp .= '<br />&nbsp;<br />'.$hesklang['check_spambox'];
	hesk_process_messages($tmp,'ticket.php?e='.$email,'SUCCESS');
	exit();

	/* Print header */
	$hesk_settings['tmp_title'] = $hesk_settings['hesk_title'] . ' - ' . $hesklang['tid_sent'];
	require_once(HESK_PATH . 'inc/header.inc.php');
	?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="3"><img src="img/headerleftsm.jpg" width="3" height="25" alt="" /></td>
<td class="headersm"><?php hesk_showTopBar($hesklang['tid_sent']); ?></td>
<td width="3"><img src="img/headerrightsm.jpg" width="3" height="25" alt="" /></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td><span class="smaller"><a href="<?php echo $hesk_settings['site_url']; ?>" class="smaller"><?php echo $hesk_settings['site_title']; ?></a> &gt;
<a href="<?php echo $hesk_settings['hesk_url']; ?>" class="smaller"><?php echo $hesk_settings['hesk_title']; ?></a>
&gt; <?php echo $hesklang['tid_sent']; ?></span></td>
</tr>
</table>

</td>
</tr>
<tr>
<td>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td width="7" height="7"><img src="img/roundcornerslt.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornerstop"></td>
	<td><img src="img/roundcornersrt.jpg" width="7" height="7" alt="" /></td>
</tr>
<tr>
	<td class="roundcornersleft">&nbsp;</td>
	<td>

	<p>&nbsp;</p>
	<p align="center"><?php echo $hesklang['tid_sent2']; ?></p>
	<p align="center"><b><?php echo $hesklang['check_spambox']; ?></b></p>
	<p>&nbsp;</p>
	<p align="center"><a href="<?php echo $hesk_settings['hesk_url']; ?>"><?php echo $hesk_settings['hesk_title']; ?></a></p>
	<p>&nbsp;</p>

	</td>
	<td class="roundcornersright">&nbsp;</td>
</tr>
<tr>
	<td><img src="img/roundcornerslb.jpg" width="7" height="7" alt="" /></td>
	<td class="roundcornersbottom"></td>
	<td width="7" height="7"><img src="img/roundcornersrb.jpg" width="7" height="7" alt="" /></td>
</tr>
</table>

	<?php

    } // End forgot_tid()

?>
