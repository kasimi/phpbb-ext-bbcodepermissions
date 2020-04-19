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

use kasimi\bbcodepermissions\event\pm_listener;

class m4_pm_permissions extends add_permissions
{
	public static function depends_on(): array
	{
		return ['\kasimi\bbcodepermissions\migrations\m3_post_permissions'];
	}

	protected function get_permission_modes(): array
	{
		return [
			pm_listener::PERMISSION_MODE_PM_USE,
			pm_listener::PERMISSION_MODE_PM_VIEW,
		];
	}
}
