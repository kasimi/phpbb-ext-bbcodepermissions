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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class pm_listener implements EventSubscriberInterface
{
	/** @const string */
	const PERMISSION_MODE_PM_USE = 'pm_use';

	/** @const string */
	const PERMISSION_MODE_PM_VIEW = 'pm_view';

	/** @var state */
	protected $bbcode_use_state;

	/** @var text_helper */
	protected $text_helper;

	public function __construct(
		state $bbcode_use_state,
		text_helper $text_helper
	)
	{
		$this->bbcode_use_state	= $bbcode_use_state;
		$this->text_helper		= $text_helper;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'core.ucp_pm_compose_modify_data'						=> 'ucp_pm_compose_modify_data',
			'core.ucp_pm_view_message_before'						=> 'ucp_pm_view_message_before',
			'core.message_history_modify_rowset'					=> 'message_history_modify_rowset',
			'core.ucp_pm_compose_compose_pm_basic_info_query_after'	=> 'ucp_pm_compose_compose_pm_basic_info_query_after',
		];
	}

	public function ucp_pm_compose_modify_data(data $event): void
	{
		$this->bbcode_use_state->set_mode(self::PERMISSION_MODE_PM_USE);
	}

	public function ucp_pm_view_message_before(data $event): void
	{
		$event['message_row'] = $this->text_helper->process_row(self::PERMISSION_MODE_PM_VIEW, $event['message_row'], 'message_text');
	}

	public function message_history_modify_rowset(data $event): void
	{
		$event['rowset'] = $this->text_helper->process_rowset(self::PERMISSION_MODE_PM_VIEW, $event['rowset'], 'message_text');
	}

	public function ucp_pm_compose_compose_pm_basic_info_query_after(data $event): void
	{
		$event['post'] = $this->text_helper->process_row(self::PERMISSION_MODE_PM_VIEW, $event['post'], 'message_text');
	}
}

