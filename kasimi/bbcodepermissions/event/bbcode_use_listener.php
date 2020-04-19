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

use kasimi\bbcodepermissions\helper\db_helper;
use kasimi\bbcodepermissions\helper\permission_helper;
use kasimi\bbcodepermissions\state;
use phpbb\event\data;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\textformatter\s9e\parser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class bbcode_use_listener implements EventSubscriberInterface
{
	/** @var template */
	protected $template;

	/** @var language */
	protected $language;

	/** @var state */
	protected $bbcode_use_state;

	/** @var permission_helper */
	protected $permission_helper;

	/** @var db_helper */
	protected $db_helper;

	public function __construct(
		template $template,
		language $language,
		state $bbcode_use_state,
		permission_helper $permission_helper,
		db_helper $db_helper
	)
	{
		$this->template				= $template;
		$this->language				= $language;
		$this->bbcode_use_state		= $bbcode_use_state;
		$this->permission_helper	= $permission_helper;
		$this->db_helper			= $db_helper;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'core.display_custom_bbcodes_modify_sql'	=> 'display_custom_bbcodes_modify_sql',
			'core.message_parser_check_message'			=> 'message_parser_check_message',
			'core.text_formatter_s9e_parse_before'		=> 'text_formatter_s9e_parse_before',
			'core.text_formatter_s9e_get_errors'		=> 'text_formatter_s9e_get_errors',
		];
	}

	public function display_custom_bbcodes_modify_sql(data $event): void
	{
		if (!$this->bbcode_use_state->is_active())
		{
			return;
		}

		$disallowed_bbcodes = $this->permission_helper->get_bbcode_permissions($this->bbcode_use_state);

		if ($disallowed_bbcodes)
		{
			$disallowed_bbcode_tags = array_column($disallowed_bbcodes, 'bbcode_tag');
			$class_names = array_map($this->wrapper('bbcode-'), $disallowed_bbcode_tags);

			// Core BBCodes (hidden with CSS)
			$this->template->assign_var('DISALLOWED_BBCODES', $class_names);

			// Custom BBCodes
			$event['sql_ary'] = $this->db_helper->inject_disallowed_bbcodes($event['sql_ary'], $disallowed_bbcode_tags);
		}
	}

	public function message_parser_check_message(data $event): void
	{
		if (!$this->bbcode_use_state->is_active())
		{
			return;
		}

		$disallowed_bbcodes = $this->permission_helper->get_bbcode_permissions($this->bbcode_use_state);

		$additional_bbcode_tags = [
			'img',
			'flash',
			'quote',
			'url',
		];

		$disallowed_bbcode_tags = array_column($disallowed_bbcodes, 'bbcode_tag');

		foreach (array_intersect($additional_bbcode_tags, $disallowed_bbcode_tags) as $disallowed_bbcode_tag)
		{
			$event['allow_' . $disallowed_bbcode_tag . '_bbcode'] = false;
		}
	}

	public function text_formatter_s9e_parse_before(data $event): void
	{
		if (!$this->bbcode_use_state->is_active())
		{
			return;
		}

		$disallowed_bbcodes = $this->permission_helper->get_bbcode_permissions($this->bbcode_use_state);

		$disallowed_bbcode_tags = array_column($disallowed_bbcodes, 'bbcode_tag');

		/** @var parser $parser */
		$parser = $event['parser'];

		foreach ($disallowed_bbcode_tags as $disallowed_bbcode_tag)
		{
			$parser->disable_bbcode($disallowed_bbcode_tag);
		}
	}

	public function text_formatter_s9e_get_errors(data $event)
	{
		if (!$this->bbcode_use_state->is_active())
		{
			return;
		}

		$errors = $event['errors'];

		// The parser didn't find any disallowed use of BBCodes
		if (!$errors)
		{
			return;
		}

		$disallowed_bbcodes = $this->permission_helper->get_bbcode_permissions($this->bbcode_use_state);

		$disallowed_bbcodes_with_messages = $this->permission_helper->filter_bbcodes_with_messages($disallowed_bbcodes);

		// There are disallowed BBCodes used, but we don't need to display a custom message for them
		if (!$disallowed_bbcodes_with_messages)
		{
			return;
		}

		$bbcode_messages = array_combine(
			array_map('strtolower', array_column($disallowed_bbcodes_with_messages, 'bbcode_tag')),
			array_column($disallowed_bbcodes_with_messages, 'message')
		);

		foreach ($errors as $key => $error)
		{
			if ($error[0] == 'UNAUTHORISED_BBCODE')
			{
				$disallowed_bbcode = trim($error[1], '[]');

				if (isset($bbcode_messages[$disallowed_bbcode]))
				{
					$message = htmlspecialchars_decode($bbcode_messages[$disallowed_bbcode], ENT_QUOTES);
					$errors[$key] = [$this->language->lang($message)];
				}
			}
		}

		$this->bbcode_use_state->set_errors($errors);

		$event['errors'] = $errors;
	}

	protected function wrapper(string $prefix, string $postfix = ''): \Closure
	{
		return function($string) use ($prefix, $postfix)
		{
			return $prefix . $string . $postfix;
		};
	}
}
