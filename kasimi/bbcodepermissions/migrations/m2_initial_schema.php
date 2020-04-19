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

class m2_initial_schema extends migration
{
	public static function depends_on(): array
	{
		return ['\kasimi\bbcodepermissions\migrations\m1_acp_module'];
	}

	public function update_schema(): array
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'bbcode_permissions' => [
					'COLUMNS' => [
						'permission_id'		=> ['USINT', null, 'auto_increment'],
						'permission_mode'	=> ['VCHAR', ''],
						'bbcode_id'			=> ['USINT', 0],
						'bbcode_tag'		=> ['VCHAR:16', ''],
						'message'			=> ['TEXT_UNI', ''],
					],
					'PRIMARY_KEY' => 'permission_id',
				],
			],
		];
	}

	public function revert_schema(): array
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'bbcode_permissions',
			],
		];
	}
}
