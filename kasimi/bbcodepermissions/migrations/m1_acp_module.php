<?php declare(strict_types=1);

/**
 *
 * BBCode Permissions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\bbcodepermissions\migrations;

use phpbb\db\migration\migration;

class m1_acp_module extends migration
{
	public static function depends_on(): array
	{
		return ['\phpbb\db\migration\data\v32x\v324'];
	}

	public function update_data(): array
	{
		return [
			['module.add', [
				'acp',
				'ACP_MESSAGES',
				[
					'module_basename'	=> '\kasimi\bbcodepermissions\acp\bbcode_messages_module',
					'modes'				=> ['main'],
				],
			]],
		];
	}
}
