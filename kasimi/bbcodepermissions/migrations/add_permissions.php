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

use kasimi\bbcodepermissions\helper\permission_helper;
use phpbb\db\migration\container_aware_migration;

abstract class add_permissions extends container_aware_migration
{
	public function update_data(): array
	{
		return [
			['custom', [[$this, 'install_bbcode_permissions']]],
		];
	}

	public function revert_data(): array
	{
		return [
			['custom', [[$this, 'uninstall_bbcode_permissions']]],
		];
	}

	abstract protected function get_permission_modes(): array;

	public function install_bbcode_permissions(): void
	{
		$modes = $this->get_permission_modes();
		$installed_bbcodes = $this->get_installed_bbcodes();
		$permission_helper = permission_helper::get_instance($this->container);
		$permission_helper->add_permissions($modes, $installed_bbcodes);
	}

	public function uninstall_bbcode_permissions(): void
	{
		$modes = $this->get_permission_modes();
		$permission_helper = permission_helper::get_instance($this->container);
		$permission_helper->delete_permissions($modes);
	}

	protected function get_installed_bbcodes(): array
	{
		if (!class_exists('\bbcode_firstpass'))
		{
			include($this->phpbb_root_path . 'includes/message_parser.' . $this->php_ext);
		}

		$bbcode_firstpass = new \bbcode_firstpass();
		$bbcode_firstpass->bbcode_init(true);

		return array_keys($bbcode_firstpass->bbcodes);
	}
}
