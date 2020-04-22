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

class post_listener implements EventSubscriberInterface
{
	/** @const string */
	const PERMISSION_MODE_POST_USE = 'post_use';

	/** @const string */
	const PERMISSION_MODE_POST_VIEW = 'post_view';

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
			'core.posting_modify_row_data'				=> 'posting_modify_row_data',
			'core.modify_posting_auth'					=> 'modify_posting_auth',
			'core.viewtopic_modify_post_data'			=> 'process_rowset',
			'core.topic_review_modify_post_list'		=> 'process_rowset',
			'core.mcp_get_post_data_after'				=> 'process_rowset',
			'core.mcp_topic_modify_post_data'			=> 'process_rowset',
			'core.search_modify_post_row'				=> 'process_row',
			'core.feed_modify_feed_row'					=> 'process_row',
		];
	}

	public function posting_modify_row_data(data $event): void
	{
		$this->bbcode_use_state->set_mode(self::PERMISSION_MODE_POST_USE);
		$this->bbcode_use_state->set_forum_id((int) $event['forum_id']);
	}

	public function modify_posting_auth(data $event): void
	{
		if ($event['mode'] == 'quote' && !$event['submit'] && !$event['preview'] && !$event['refresh'])
		{
			$event['post_data'] = $this->text_helper->process_row(self::PERMISSION_MODE_POST_VIEW, $event['post_data'], 'post_text');
		}
	}

	public function process_rowset(data $event): void
	{
		$event['rowset'] = $this->text_helper->process_rowset(self::PERMISSION_MODE_POST_VIEW, $event['rowset'], 'post_text');
	}

	public function process_row(data $event): void
	{
		if (isset($event['row']['post_text']))
		{
			$event['row'] = $this->text_helper->process_row(self::PERMISSION_MODE_POST_VIEW, $event['row'], 'post_text');
		}
	}
}
