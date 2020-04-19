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

abstract class base
{
	/** @var string */
	protected $tpl_name;

	/** @var string */
	protected $page_title;

	public abstract function main(string $id, string $mode, string $u_action): void;

	public function get_tpl_name(): string
	{
		return $this->tpl_name;
	}

	public function get_page_title(): string
	{
		return $this->page_title;
	}
}
