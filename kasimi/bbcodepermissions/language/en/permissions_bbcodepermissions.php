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
	'ACL_CAT_BBCODE_PERMISSIONS_POST_USE'		=> 'Use BBCodes in posts',
	'ACL_BBCODE_PERMISSION_CAN_POST_USE'		=> 'Can use <strong>[%s]</strong>',

	'ACL_CAT_BBCODE_PERMISSIONS_POST_VIEW'		=> 'View BBCodes in posts',
	'ACL_BBCODE_PERMISSION_CAN_POST_VIEW'		=> 'Can view <strong>[%s]</strong>',

	'ACL_CAT_BBCODE_PERMISSIONS_PM_USE'			=> 'Use BBCodes in PMs',
	'ACL_BBCODE_PERMISSION_CAN_PM_USE'			=> 'Can use <strong>[%s]</strong>',

	'ACL_CAT_BBCODE_PERMISSIONS_PM_VIEW'		=> 'View BBCodes in PMs',
	'ACL_BBCODE_PERMISSION_CAN_PM_VIEW'			=> 'Can view <strong>[%s]</strong>',

	'ACL_CAT_BBCODE_PERMISSIONS_MCHAT_USE'		=> 'Use BBCodes in mChat',
	'ACL_BBCODE_PERMISSION_CAN_MCHAT_USE'		=> 'Can use <strong>[%s]</strong>',

	'ACL_CAT_BBCODE_PERMISSIONS_MCHAT_VIEW'		=> 'View BBCodes in mChat',
	'ACL_BBCODE_PERMISSION_CAN_MCHAT_VIEW'		=> 'Can view <strong>[%s]</strong>',
]);
