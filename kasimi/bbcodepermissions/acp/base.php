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

use kasimi\bbcodepermissions\controller\base as controller_base;

abstract class base
{
	/** @var string */
	public $u_action;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $page_title;

	/** @var controller_base */
	protected $controller;

	protected abstract function get_controller_service_id(): string;

	public function main(string $id, string $mode): void
	{
		global $phpbb_container;

		$controller_service  = $this->get_controller_service_id();
		$this->controller = $phpbb_container->get($controller_service);
		$this->controller->main($id, $mode, $this->u_action);

		$this->tpl_name = $this->controller->get_tpl_name();
		$this->page_title = $this->controller->get_page_title();
	}
}
