<?php declare(strict_types=1);

/**
 *
 * BBCode Permissions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\bbcodepermissions\controller;

use kasimi\bbcodepermissions\helper\permission_helper;
use kasimi\bbcodepermissions\state;
use phpbb\language\language;
use phpbb\log\log_interface;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\user;

class acp_bbcode_messages extends base
{
	/** @var user */
	protected $user;

	/** @var language */
	protected $language;

	/** @var request_interface */
	protected $request;

	/** @var template */
	protected $template;

	/** @var log_interface */
	protected $log;

	/** @var permission_helper */
	protected $permission_helper;

	public function __construct(
		user $user,
		language $language,
		request_interface $request,
		template $template,
		log_interface $log,
		permission_helper $permission_helper
	)
	{
		$this->user					= $user;
		$this->language				= $language;
		$this->request				= $request;
		$this->template				= $template;
		$this->log					= $log;
		$this->permission_helper	= $permission_helper;
	}

	public function main(string $id, string $mode, string $u_action): void
	{
		$ext_name = 'kasimi/bbcodepermissions';

		$this->language->add_lang('acp/posting');
		$this->language->add_lang('acp', $ext_name);

		$this->tpl_name = 'acp_bbcodepermissions_body';
		$this->page_title = 'ACP_BBCODEPERMISSIONS_MESSAGES';

		$action	= $this->request->variable('action', '');
		$bbcode_tag = $this->request->variable('bbcode', '');

		$modes = $this->permission_helper->get_permission_modes();

		switch ($action)
		{
			case 'edit':

				add_form_key($ext_name);

				$this->template->assign_vars([
					'S_EDIT_BBCODE_MESSAGE'	=> true,
					'BBCODE_TAG'			=> $bbcode_tag,
					'U_BACK'				=> $u_action,
					'U_ACTION'				=> $u_action . '&amp;action=modify' . ($bbcode_tag ? ('&amp;bbcode=' . $bbcode_tag) : ''),
				]);

				foreach ($modes as $mode)
				{
					$message = $this->permission_helper->get_message($mode, $bbcode_tag, true);

					$this->template->assign_block_vars('modes', [
						'MODE'		=> $mode,
						'MESSAGE'	=> $message,
					]);
				}

			break;

			case 'modify':

				if (!check_form_key($ext_name))
				{
					trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($u_action), E_USER_WARNING);
				}

				foreach ($modes as $mode)
				{
					$message = $this->request->variable('message_' . $mode, '', true);

					$success = $this->permission_helper->set_message($mode, $bbcode_tag, $message);

					if (!$success)
					{
						trigger_error($this->language->lang('BBCODE_NOT_EXIST') . adm_back_link($u_action), E_USER_WARNING);
					}
				}

				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BBCODEPERMISSIONS_MESSAGES_EDIT', false, [$bbcode_tag]);

				trigger_error($this->language->lang('ACP_BBCODEPERMISSIONS_MESSAGE_EDITED') . adm_back_link($u_action));

			break;

			default:

				$bbcodes = [];
				$message_modes = [];

				foreach ($modes as $mode)
				{
					$bbcodes[$mode] = $this->permission_helper->get_bbcode_permissions(new state($mode), true);

					foreach ($bbcodes[$mode] as $bbcode)
					{
						if ($bbcode['message'])
						{
							$message_modes[$bbcode['bbcode_tag']][] = $this->language->lang('BBCODEPERMISSIONS_' . strtoupper($mode));
						}
					}
				}

				$mode = reset($modes);

				foreach ($bbcodes[$mode] as $bbcode)
				{
					$bbcode_tag = $bbcode['bbcode_tag'];

					$this->template->assign_block_vars('bbcodes', [
						'BBCODE_TAG'	=> $bbcode_tag,
						'MESSAGE_MODES'	=> $message_modes[$bbcode_tag] ?? [],
						'U_EDIT'		=> $u_action . '&amp;action=edit&amp;bbcode=' . rawurlencode($bbcode_tag),
					]);
				}

				$this->template->assign_vars([
					'U_ACTION' => $u_action . '&amp;action=add',
				]);

			break;
		}
	}
}
