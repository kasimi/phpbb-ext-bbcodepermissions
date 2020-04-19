<?php declare(strict_types=1);

/**
 *
 * BBCode Permissions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, kasimi, https://kasimi.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kasimi\bbcodepermissions\acp;

class bbcode_messages_module extends base
{
	protected function get_controller_service_id(): string
	{
		return 'kasimi.bbcodepermissions.controller.acp.bbcode_messages';
	}
}
