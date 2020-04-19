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

use kasimi\bbcodepermissions\helper\text_helper;
use kasimi\bbcodepermissions\state;
use phpbb\event\data;
use phpbb\exception\http_exception;
use phpbb\language\language;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class mchat_listener implements EventSubscriberInterface
{
	/** @const string */
	const PERMISSION_MODE_MCHAT_USE = 'mchat_use';

	/** @const string */
	const PERMISSION_MODE_MCHAT_VIEW = 'mchat_view';

	/** @var state */
	protected $bbcode_use_state;

	/** @var text_helper */
	protected $text_helper;

	/** @var language */
	protected $language;

	public function __construct(
		state $bbcode_use_state,
		text_helper $text_helper,
		language $language
	)
	{
		$this->bbcode_use_state	= $bbcode_use_state;
		$this->text_helper		= $text_helper;
		$this->language			= $language;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'dmzx.mchat.assign_bbcodes_smilies_before'	=> 'set_use_mode',
			'dmzx.mchat.process_message_before'			=> 'set_use_mode',
			'dmzx.mchat.action_before'					=> 'mchat_action_before',
			'dmzx.mchat.get_messages_modify_rowset'		=> 'mchat_get_messages_modify_rowset',
		];
	}

	public function set_use_mode(data $event): void
	{
		$this->bbcode_use_state->set_mode(self::PERMISSION_MODE_MCHAT_USE);
	}

	public function mchat_action_before(data $event): void
	{
		if (in_array($event['action'], ['add', 'edit']))
		{
			$errors = $this->bbcode_use_state->get_errors();

			if ($errors)
			{
				$lang_errors = [];

				foreach ($errors as $error)
				{
					$lang_errors[] = $this->language->lang(...$error);
				}

				throw new http_exception(400, implode('<br>', $lang_errors));
			}
		}
	}

	public function mchat_get_messages_modify_rowset(data $event): void
	{
		$event['rows'] = $this->text_helper->process_rowset(self::PERMISSION_MODE_MCHAT_VIEW, $event['rows'], 'message');
	}
}
