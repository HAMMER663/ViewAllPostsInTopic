<?php
/**
*
* @package View All Posts In Topic
* @copyright (c) 2014 HAMMER663
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace hammer663\ViewAllPostsInTopic\migrations\v0xx;

class v_0_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['vapit_version']) && version_compare($this->config['vapit_version'], '0.0.1', '>=');
	}

	static public function depends_on()
	{
			return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return 	array(
			'add_columns' => array(
				$this->table_prefix . 'forums' => array(
					'enable_view_all_post' => array('TINT:1', '0'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return 	array(
			'drop_columns' => array(
				$this->table_prefix . 'forums' => array('enable_view_all_post'),
			),
		);
	}

	public function update_data()
	{
		return array(
			// Add configs
		//	array('config.add', array('qr_bbcode', '1')),
		//	array('config.add', array('qr_comma', '1')),
		//	array('config.add', array('qr_quicknick', '1')),
		//	array('config.add', array('qr_quickquote', '1')),
		//	array('config.add', array('qr_smilies', '1')),
		//	array('config.add', array('qr_enable_re', '0')),
		//	array('config.add', array('qr_ctrlenter', '1')),

			// Current version
			array('config.add', array('vapit_version', '0.0.1')),

			// Add ACP modules
		//	array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_QUICKREPLY')),
		//	array('module.add', array('acp', 'ACP_QUICKREPLY', array(
		//			'module_basename'	=> '\tatiana5\quickreply\acp\quickreply_module',
		//			'module_langname'	=> 'ACP_QUICKREPLY_EXPLAIN',
		//			'module_mode'		=> 'config_quickreply',
		//			'module_auth'		=> 'acl_a_quickreply',
		//	))),

			// Add permissions
		//	array('permission.add', array('a_quickreply', true)),
		//	array('permission.add', array('f_qr_change_subject', false)),
		//	array('permission.add', array('m_qr_change_subject', true)),

			// Set permissions
		//	array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_quickreply')),
		//	array('permission.permission_set', array('ROLE_FORUM_FULL', 'f_qr_change_subject')),
		//	array('permission.permission_set', array('ROLE_FORUM_STANDARD', 'f_qr_change_subject')),
		//	array('permission.permission_set', array('ROLE_MOD_FULL', 'm_qr_change_subject')),
		);
	}
}
