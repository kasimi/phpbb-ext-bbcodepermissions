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

class state
{
	/** @var string */
	protected $mode;

	/** @var int */
	protected $forum_id;

	/** @var array */
	protected $errors = [];

	public function __construct(
		string $mode = '',
		int $forum_id = 0
	)
	{
		$this->mode		= $mode;
		$this->forum_id	= $forum_id;
	}

	public function is_active(): bool
	{
		return !empty($this->mode);
	}

	public function get_mode(): string
	{
		return $this->mode;
	}

	public function set_mode(string $mode): void
	{
		$this->mode = $mode;
	}

	public function get_forum_id(): int
	{
		return $this->forum_id;
	}

	public function set_forum_id(int $forum_id)
	{
		$this->forum_id = $forum_id;
	}

	public function get_errors(): array
	{
		return $this->errors;
	}

	public function set_errors(array $errors): void
	{
		$this->errors = $errors;
	}
}
