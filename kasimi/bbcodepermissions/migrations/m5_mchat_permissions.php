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

use kasimi\bbcodepermissions\event\mchat_listener;

class m5_mchat_permissions extends add_permissions
{
	public static function depends_on(): array
	{
		return ['\kasimi\bbcodepermissions\migrations\m4_pm_permissions'];
	}

	protected function get_permission_modes(): array
	{
		return [
			mchat_listener::PERMISSION_MODE_MCHAT_USE,
			mchat_listener::PERMISSION_MODE_MCHAT_VIEW,
		];
	}
}
