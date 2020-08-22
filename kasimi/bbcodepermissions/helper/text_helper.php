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

use kasimi\bbcodepermissions\state;
use phpbb\language\language;
use phpbb\textformatter\s9e\parser;

class text_helper
{
	/** @var parser */
	protected $parser;

	/** @var language */
	protected $language;

	/** @var permission_helper */
	protected $permission_helper;

	public function __construct(
		parser $parser,
		language $language,
		permission_helper $permission_helper
	)
	{
		$this->parser				= $parser;
		$this->language				= $language;
		$this->permission_helper	= $permission_helper;
	}

	public function process_row(string $mode, array $row, string $text_key): array
	{
		$rows = $this->process_rowset($mode, [$row], $text_key);
		return reset($rows);
	}

	public function process_rowset(string $mode, array $rowset, string $text_key): array
	{
		$disallowed_bbcodes = [];

		foreach ($rowset as $post_id => $row)
		{
			$forum_id = (int) ($row['forum_id'] ?? 0);

			if (!isset($disallowed_bbcodes[$forum_id]))
			{
				$disallowed_bbcodes[$forum_id] = $this->permission_helper->get_bbcode_permissions(new state($mode, $forum_id));
			}

			$rowset[$post_id][$text_key] = $this->process_xml($row[$text_key], $disallowed_bbcodes[$forum_id]);
		}

		return $rowset;
	}

	public function process_xml(string $xml, array $disallowed_bbcodes): string
	{
		if ($disallowed_bbcodes && !preg_match('#^<t[ />]#', $xml))
		{
			$dom = new \DOMDocument;
			$dom->loadXML($xml);
			$dom_is_modified = false;

			foreach ($disallowed_bbcodes as $bbcode)
			{
				$nodes = $dom->getElementsByTagName(strtoupper($bbcode['bbcode_tag']));

				for ($i = $nodes->length; --$i >= 0;)
				{
					$dom_is_modified = true;
					$node = $nodes->item($i);

					if ($bbcode['message'] !== '')
					{
						$message = htmlspecialchars_decode($bbcode['message'], ENT_QUOTES);
						$message = $this->language->lang($message);

						$fragment = $dom->createDocumentFragment();
						$xml = $this->parser->parse($message);
						$fragment->appendXML($xml);
						$node->parentNode->replaceChild($fragment, $node);
					}
					else
					{
						$node->parentNode->removeChild($node);
					}
				}
			}

			if ($dom_is_modified)
			{
				$xml = $dom->saveXML($dom->documentElement);
			}
		}

		return $xml;
	}
}
