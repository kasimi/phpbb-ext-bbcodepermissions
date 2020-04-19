<?php declare(strict_types=1);

/**
 *
 * BBCode Permissions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\bbcodepermissions;

use phpbb\extension\base;

class ext extends base
{
	public function is_enableable(): bool
	{
		return phpbb_version_compare(PHP_VERSION, '7.2.0', '>=')
			&& phpbb_version_compare(PHPBB_VERSION, '3.2.4', '>=')
		;
	}
}
