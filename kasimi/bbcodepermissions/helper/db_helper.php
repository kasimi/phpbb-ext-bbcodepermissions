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

use phpbb\db\driver\driver_interface as db_interface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class db_helper
{
	/** @const array */
	const CORE_BBCODES = [
		'quote'			=> BBCODE_ID_QUOTE,
		'b'				=> BBCODE_ID_B,
		'i'				=> BBCODE_ID_I,
		'url'			=> BBCODE_ID_URL,
		'img'			=> BBCODE_ID_IMG,
		'size'			=> BBCODE_ID_SIZE,
		'color'			=> BBCODE_ID_COLOR,
		'u'				=> BBCODE_ID_U,
		'code'			=> BBCODE_ID_CODE,
		'list'			=> BBCODE_ID_LIST,
		'email'			=> BBCODE_ID_EMAIL,
		'flash'			=> BBCODE_ID_FLASH,
		'attachment'	=> BBCODE_ID_ATTACH,
	];

	/** @var db_interface */
	protected $db;

	/** @var string */
	protected $table_bbcodes;

	/** @var string */
	protected $table_posts;

	/** @var string */
	protected $table_acl_roles;

	/** @var string */
	protected $table_bbcode_permissions;

	/** @var array */
	protected $table_bbcode_permissions_rows;

	public function __construct(
		db_interface $db,
		$table_bbcodes,
		$table_posts,
		$table_bbcode_permissions
	)
	{
		$this->db						= $db;
		$this->table_bbcodes			= $table_bbcodes;
		$this->table_posts				= $table_posts;
		$this->table_bbcode_permissions	= $table_bbcode_permissions;
	}

	/**
	 * Creates and returns a new instance of this class without requiring this extension to be enabled
	 */
	public static function get_instance(ContainerInterface $container): db_helper
	{
		return new static(
			$container->get('dbal.conn'),
			$container->getParameter('tables.bbcodes'),
			$container->getParameter('tables.posts'),
			$container->getParameter('core.table_prefix') . 'bbcode_permissions'
		);
	}

	public function insert_bbcode_permission(array $bbcode_permission): int
	{
		$sql = 'INSERT INTO ' . $this->table_bbcode_permissions . ' ' . $this->db->sql_build_array('INSERT', $bbcode_permission);
		$this->db->sql_query($sql);
		return (int) $this->db->sql_nextid();
	}

	public function get_bbcode_permission_rows(string $mode): array
	{
		if (!$this->table_bbcode_permissions_rows)
		{
			$sql = 'SELECT permission_id, permission_mode, bbcode_tag, message
				FROM ' . $this->table_bbcode_permissions;
			$result = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			foreach ($rows as $row)
			{
				$this->table_bbcode_permissions_rows[$row['permission_mode']][] = $row;
			}
		}

		return $this->table_bbcode_permissions_rows[$mode] ?? [];
	}

	public function update_bbcode_permission_tag(string $bbcode_tag): void
	{
		$sql = 'UPDATE ' . $this->table_bbcode_permissions . "
			SET bbcode_tag = '" . $this->db->sql_escape($bbcode_tag) . "'
			WHERE bbcode_id = " . (int) $this->get_bbcode_id($bbcode_tag);
		$this->db->sql_query($sql);
	}

	public function get_bbcode_id(string $bbcode_tag): int
	{
		if (isset(self::CORE_BBCODES[$bbcode_tag]))
		{
			return self::CORE_BBCODES[$bbcode_tag];
		}

		$sql = 'SELECT bbcode_id
			FROM ' . $this->table_bbcodes . "
			WHERE bbcode_tag = '" . $this->db->sql_escape($bbcode_tag) . "'";
		$result = $this->db->sql_query($sql);
		$bbcode_id = $this->db->sql_fetchfield('bbcode_id', false, $result);
		$this->db->sql_freeresult($result);

		return (int) $bbcode_id;
	}

	public function delete_bbcode_permissions(array $permission_ids = []): void
	{
		$sql = 'DELETE FROM ' . $this->table_bbcode_permissions . '
			WHERE ' . $this->db->sql_in_set('permission_id', $permission_ids, false, true);
		$this->db->sql_query($sql);
	}

	public function update_message(string $mode, string $bbcode_tag, string $message): int
	{
		$sql = 'UPDATE ' . $this->table_bbcode_permissions . "
			SET message = '" . $this->db->sql_escape($message) . "'
			WHERE bbcode_tag = '" . $this->db->sql_escape($bbcode_tag) . "'
				AND permission_mode = '" . $this->db->sql_escape($mode) . "'";
		$this->db->sql_query($sql);
		return (int) $this->db->sql_affectedrows();
	}

	public function inject_disallowed_bbcodes(array $sql_ary, array $disallowed_bbcode_tags): array
	{
		if ($disallowed_bbcode_tags)
		{
			$sql_ary['WHERE'] = empty($sql_ary['WHERE']) ? '' : ($sql_ary['WHERE'] . ' AND ');
			$sql_ary['WHERE'] .= $this->db->sql_in_set('b.bbcode_tag', array_values($disallowed_bbcode_tags), true);
		}

		return $sql_ary;
	}
}
