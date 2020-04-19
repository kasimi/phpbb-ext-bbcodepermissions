<?php declare(strict_types=1);

/**
 *
 * BBCode Permissions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\bbcodepermissions\acp;

class bbcode_messages_info
{
	public function module(): array
	{
		return [
			'filename'	=> '\kasimi\bbcodepermissions\acp\bbcode_messages_module',
			'title'		=> 'ACP_BBCODEPERMISSIONS_MESSAGES',
			'modes'		=> [
				'main'		=> [
					'title'		=> 'ACP_BBCODEPERMISSIONS_MESSAGES',
					'auth'		=> 'ext_kasimi/bbcodepermissions && acl_a_bbcode',
					'cat'		=> ['ACP_MESSAGES'],
					'after'		=> 'ACP_BBCODES',
				],
			],
		];
	}
}
