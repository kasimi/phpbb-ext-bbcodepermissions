<?php declare(strict_types=1);

/**
 *
 * BBCode Permissions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters for use
// ’ » “ ” …

$lang = array_merge($lang, [
	'ACP_BBCODEPERMISSIONS_MESSAGES_EXPLAIN'			=> 'BBCode messages are displayed to users who don’t have permission to use or view BBCodes.',
	'ACP_BBCODEPERMISSIONS_MESSAGE_MODES'				=> 'BBCode message for modes',
	'ACP_BBCODEPERMISSIONS_MESSAGE_EDITED'				=> 'BBCode message edited successfully.',

	// Posts
	'BBCODEPERMISSIONS_POST_USE'						=> 'post use',
	'BBCODEPERMISSIONS_MESSAGE_POST_USE'				=> 'Message when using BBCode [%s] in posts',
	'BBCODEPERMISSIONS_MESSAGE_POST_USE_EXPLAIN'		=> 'Enter a message a user sees in addition to phpBB’s default warning message when <strong>using</strong> the BBCode without permission. Leave it empty to not display any additional message.<br><em><br>» BBCodes are <strong>not</strong> supported.<br>» HTML is supported.<br>» Language keys are supported.</em>',
	'BBCODEPERMISSIONS_POST_VIEW'						=> 'post view',
	'BBCODEPERMISSIONS_MESSAGE_POST_VIEW'				=> 'Message when viewing BBCode [%s] in posts',
	'BBCODEPERMISSIONS_MESSAGE_POST_VIEW_EXPLAIN'		=> 'Enter a message a user sees when <strong>viewing</strong> the BBCode without permission. Leave it empty to hide the BBCode and its contents from the user entirely.<br><em><br>» BBCodes are supported.<br>» HTML is <strong>not</strong> supported.<br>» Language keys are supported.</em>',

	// Private messages
	'BBCODEPERMISSIONS_PM_USE'							=> 'PM use',
	'BBCODEPERMISSIONS_MESSAGE_PM_USE'					=> 'Message when using BBCode [%s] in PMs',
	'BBCODEPERMISSIONS_MESSAGE_PM_USE_EXPLAIN'			=> 'Enter a message a user sees in addition to phpBB’s default warning message when <strong>using</strong> the BBCode without permission. Leave it empty to not display any additional message.<br><em><br>» BBCodes are <strong>not</strong> supported.<br>» HTML is supported.<br>» Language keys are supported.</em>',
	'BBCODEPERMISSIONS_PM_VIEW'							=> 'PM view',
	'BBCODEPERMISSIONS_MESSAGE_PM_VIEW'					=> 'Message when viewing BBCode [%s] in PMs',
	'BBCODEPERMISSIONS_MESSAGE_PM_VIEW_EXPLAIN'			=> 'Enter a message a user sees when <strong>viewing</strong> the BBCode without permission. Leave it empty to hide the BBCode and its contents from the user entirely.<br><em><br>» BBCodes are supported.<br>» HTML is <strong>not</strong> supported.<br>» Language keys are supported.</em>',
]);
