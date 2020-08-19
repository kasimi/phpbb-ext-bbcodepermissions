<?php declare(strict_types=1);

/**
 *
 * BBCode Permissions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\bbcodepermissions\helper;

use kasimi\bbcodepermissions\event\mchat_listener;
use kasimi\bbcodepermissions\event\pm_listener;
use kasimi\bbcodepermissions\event\post_listener;
use kasimi\bbcodepermissions\state;
use phpbb\auth\auth;
use phpbb\db\migration\exception;
use phpbb\db\migration\tool\permission as permission_tool;
use phpbb\extension\manager as ext_manager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class permission_helper
{
	/** @var auth */
	protected $auth;

	/** @var ext_manager */
	protected $ext_manager;

	/** @var permission_tool */
	protected $permission_tool;

	/** @var db_helper */
	protected $db_helper;

	public function __construct(
		auth $auth,
		ext_manager $ext_manager,
		permission_tool $permission_tool,
		db_helper $db_helper
	)
	{
		$this->auth				= $auth;
		$this->ext_manager		= $ext_manager;
		$this->permission_tool	= $permission_tool;
		$this->db_helper		= $db_helper;
	}

	/**
	 * Creates and returns a new instance of this class without requiring this extension to be enabled
	 */
	public static function get_instance(ContainerInterface $container): permission_helper
	{
		return new static(
			$container->get('auth'),
			$container->get('ext.manager'),
			$container->get('migrator.tool.permission'),
			db_helper::get_instance($container)
		);
	}

	public function get_permission_modes(bool $force_all = false): array
	{
		$modes = [
			post_listener::PERMISSION_MODE_POST_USE,
			post_listener::PERMISSION_MODE_POST_VIEW,

			pm_listener::PERMISSION_MODE_PM_USE,
			pm_listener::PERMISSION_MODE_PM_VIEW,
		];

		if ($force_all || $this->ext_manager->is_enabled('dmzx/mchat'))
		{
			$modes = array_merge($modes, [
				mchat_listener::PERMISSION_MODE_MCHAT_USE,
				mchat_listener::PERMISSION_MODE_MCHAT_VIEW,
			]);
		}

		return $modes;
	}

	protected function is_global(string $mode): bool
	{
		return strpos($mode, 'post_') === false;
	}

	protected function get_permission_prefix(string $mode): string
	{
		return $this->is_global($mode) ? 'u_' : 'f_';
	}

	protected function get_permission_name(string $mode, int $permission_id): string
	{
		return $this->get_permission_prefix($mode) . 'bbcode_permission_' . $permission_id . '_' . $mode;
	}

	public function add_permission(array $modes, string $bbcode_tag): void
	{
		$this->add_permissions($modes, [$bbcode_tag]);
	}

	public function add_permissions(array $modes, array $bbcode_tags): void
	{
		if (!$modes)
		{
			$modes = $this->get_permission_modes();
		}

		foreach ($bbcode_tags as $bbcode_tag)
		{
			// Skip old fashioned BBCodes á la url= quote= etc
			if (utf8_strpos($bbcode_tag, '=') !== false)
			{
				continue;
			}

			$bbcode_id = $this->db_helper->get_bbcode_id($bbcode_tag);

			foreach ($modes as $mode)
			{
				$permission_id = $this->db_helper->insert_bbcode_permission([
					'permission_mode'	=> $mode,
					'bbcode_id'			=> $bbcode_id,
					'bbcode_tag'		=> $bbcode_tag,
					'message'			=> '',
				]);

				$permission_name = $this->get_permission_name($mode, $permission_id);
				$is_global = $this->is_global($mode);
				$this->permission_tool->add($permission_name, $is_global);
				$this->set_default_permissions($mode, $permission_name);
			}
		}
	}

	public function set_default_permissions(string $mode, string $permission_name): void
	{
		$role_name = $this->is_global($mode) ? 'ROLE_USER_STANDARD' : 'ROLE_FORUM_STANDARD';

		try
		{
			$this->permission_tool->permission_set($role_name, $permission_name, 'role', true);
		}
		catch (exception $e)
		{
			// The role does not exist, don't do anything else
		}
	}

	public function edit_permission(string $bbcode_tag): void
	{
		$this->db_helper->update_bbcode_permission_tag($bbcode_tag);
	}

	public function delete_permission(string $bbcode_tag): void
	{
		$this->delete_permissions([], [$bbcode_tag]);
	}

	public function delete_permissions(array $modes, array $bbcode_tags = []): void
	{
		if (!$modes)
		{
			$modes = $this->get_permission_modes();
		}

		$ids = $this->get_permission_ids($modes, $bbcode_tags, true);

		foreach ($ids as $id)
		{
			foreach ($modes as $mode)
			{
				$permission_name = $this->get_permission_name($mode, $id);
				$is_global = $this->is_global($mode);
				$this->permission_tool->remove($permission_name, $is_global);
			}
		}

		$this->db_helper->delete_bbcode_permissions($ids);
	}

	public function get_bbcode_permissions(state $state, bool $ignore_permission_value = false): array
	{
		$bbcode_permissions = [];

		$forum_id = $this->is_global($state->get_mode()) ? 0 : $state->get_forum_id();

		$bbcode_permission_rows = $this->db_helper->get_bbcode_permission_rows($state->get_mode());

		foreach ($bbcode_permission_rows as $row)
		{
			$permission_name = $this->get_permission_name($state->get_mode(), (int) $row['permission_id']);

			if ($ignore_permission_value || $this->auth->acl_get('!' . $permission_name, $forum_id))
			{
				$bbcode_permissions[$permission_name] = $row;
			}
		}

		uasort($bbcode_permissions, function ($a, $b)
		{
			return $a['bbcode_tag'] <=> $b['bbcode_tag'];
		});

		return $bbcode_permissions;
	}

	public function get_permission_ids(array $modes, array $bbcode_tags = [], bool $ignore_permission_value = false): array
	{
		$permission_ids = [];

		foreach ($modes as $mode)
		{
			$bbcode_permissions = $this->get_bbcode_permissions(new state($mode), $ignore_permission_value);

			foreach ($bbcode_permissions as $bbcode_permission)
			{
				if (!$bbcode_tags || in_array($bbcode_permission['bbcode_tag'], $bbcode_tags))
				{
					$permission_ids[] = (int) $bbcode_permission['permission_id'];
				}
			}
		}

		return $permission_ids;
	}

	public function get_message(string $mode, string $bbcode_tag, bool $ignore_permission_value = false): string
	{
		$bbcode_permissions = $this->get_bbcode_permissions(new state($mode), $ignore_permission_value);

		foreach ($bbcode_permissions as $bbcode_permission)
		{
			if ($bbcode_tag == $bbcode_permission['bbcode_tag'])
			{
				return $bbcode_permission['message'];
			}
		}

		return '';
	}

	public function set_message(string $mode, string $bbcode_tag, string $message): int
	{
		return $this->db_helper->update_message($mode, $bbcode_tag, $message);
	}

	public function filter_bbcodes_with_messages(array $disallowed_bbcodes): array
	{
		$disallowed_bbcode_tags = [];

		foreach ($disallowed_bbcodes as $bbcode)
		{
			if ($bbcode['message'] !== '')
			{
				$disallowed_bbcode_tags[] = $bbcode;
			}
		}

		return $disallowed_bbcode_tags;
	}
}
