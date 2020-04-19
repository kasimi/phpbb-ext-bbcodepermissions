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

use kasimi\bbcodepermissions\event\post_listener;

class m3_post_permissions extends add_permissions
{
	public static function depends_on(): array
	{
		return ['\kasimi\bbcodepermissions\migrations\m2_initial_schema'];
	}

	protected function get_permission_modes(): array
	{
		return [
			post_listener::PERMISSION_MODE_POST_USE,
			post_listener::PERMISSION_MODE_POST_VIEW,
		];
	}
}
