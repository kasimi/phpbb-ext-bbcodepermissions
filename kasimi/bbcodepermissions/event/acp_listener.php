<?php declare(strict_types=1);

/**
 *
 * BBCode Permissions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\bbcodepermissions\event;

use kasimi\bbcodepermissions\helper\permission_helper;
use kasimi\bbcodepermissions\state;
use phpbb\event\data;
use phpbb\language\language;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class acp_listener implements EventSubscriberInterface
{
	/** @var language */
	protected $language;

	/** @var permission_helper */
	protected $permission_helper;

	public function __construct(
		language $lang,
		permission_helper $permission_helper
	)
	{
		$this->language				= $lang;
		$this->permission_helper	= $permission_helper;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'core.acp_bbcodes_modify_create_after'	=> 'acp_bbcodes_modify_create_after',
			'core.acp_bbcodes_delete_after'			=> 'acp_bbcodes_delete_after',
			'core.permissions'						=> 'permissions',
		];
	}

	public function acp_bbcodes_modify_create_after(data $event): void
	{
		if ($event['action'] == 'create')
		{
			$modes = $this->permission_helper->get_permission_modes();
			$this->permission_helper->add_permission($modes, $event['sql_ary']['bbcode_tag']);
		}
		else if ($event['action'] == 'modify')
		{
			$this->permission_helper->edit_permission($event['sql_ary']['bbcode_tag']);
		}
	}

	public function acp_bbcodes_delete_after(data $event): void
	{
		$this->permission_helper->delete_permission($event['bbcode_tag']);
	}

	public function permissions(data $event): void
	{
		// Temporary fix for PHPBB-16453, to be removed once phpBB 3.2.10 and 3.3.1 are minimum requirements
		$this->language->add_lang('permissions_bbcodepermissions', 'kasimi/bbcodepermissions');

		$modes = $this->permission_helper->get_permission_modes();

		foreach ($modes as $mode)
		{
			$event->update_subarray('categories', 'bbcode_permissions_' . $mode, 'ACL_CAT_BBCODE_PERMISSIONS_' . strtoupper($mode));

			$bbcode_permissions = $this->permission_helper->get_bbcode_permissions(new state($mode), true);

			foreach ($bbcode_permissions as $permission_name => $bbcode)
			{
				$event->update_subarray('permissions', $permission_name, [
					'lang'	=> $this->language->lang('ACL_BBCODE_PERMISSION_CAN_' . strtoupper($mode), $bbcode['bbcode_tag']),
					'cat'	=> 'bbcode_permissions_' . $mode,
				]);
			}
		}
	}
}
