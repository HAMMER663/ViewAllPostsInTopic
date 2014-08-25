<?php
/**
*
* @package View All Posts In Topic
* @copyright (c) 2014 HAMMER663
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace hammer663\ViewAllPostsInTopic\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{

	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\extension\manager */
	protected $phpbb_extension_manager;

	/** @var \phpbb\request\request */
	protected $request;
	
	/** @var \phpbb\content_visibility */
	protected $phpbb_content_visibility;

	/** @var string */
	protected $phpbb_root_path;
	protected $php_ext;
	protected $pagination;
	
	/**
	* Constructor
	* 

	* @param \phpbb\config\config $config
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\db\driver\driver $db
	* @param \phpbb\extension\manager $phpbb_extension_manager
	* @param \phpbb\request\request $request
	* @param \phpbb\content_visibility $phpbb_content_visibility
	* @param string $phpbb_root_path Root path
	* @param string $phpbb_ext
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\extension\manager $phpbb_extension_manager, \phpbb\request\request $request, \phpbb\content_visibility $phpbb_content_visibility, $phpbb_root_path, $php_ext, \phpbb\pagination $pagination)
	{
		
		$this->config = $config;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_extension_manager = $phpbb_extension_manager;
		$this->request = $request;
		$this->phpbb_content_visibility = $phpbb_content_visibility;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->pagination = $pagination;
	}


	

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
//			'core.viewtopic_modify_post_row'	=>	'view_all_post',
//			'core.viewtopic_modify_post_data'	=>	'view_all_post',
//			'core.posting_modify_template_vars'		=>	'view_all_post',
//			'core.viewtopic_modify_page_title'			=>	'view_all_post',
			'core.viewtopic_assign_template_vars_before'	=>	'view_all_post',
//			'core.display_forums_modify_forum_rows'		=>	'enable_view_all_post',			
			'core.acp_manage_forums_request_data'			=>	'acp_forums_request_data_view_all_post',			
			'core.acp_manage_forums_initialise_data'		=>	'acp_forums_initialise_data_view_all_post',
			'core.acp_manage_forums_display_form'			=>	'acp_forums_display_form_view_all_post',
		);
	}
	
	
	
	/**
	* All posts on viewtopic page
	*
	* @return null
	* @access public
	*/
	public function view_all_post($event)
	{
	
		$this->user->add_lang_ext('hammer663\ViewAllPostsInTopic', 'ViewAllPostsInTopic');

		$total_posts = $event['total_posts'];
		$forum_id = $event['forum_id'];
	//	$topic_id = $event['topic_id'];
		$topic_data = $event['topic_data'];	
		$topic_id = $topic_data['topic_id'];
		$forum_id = $topic_data['forum_id'];	
		$enable_vapit = $topic_data['enable_view_all_post'];

		$per_page = $this->config['posts_per_page'];
		$start = $event['start'];
		$start = $this->pagination->validate_start($start, $this->config['posts_per_page'], $total_posts);

	
		$page = request_var('page', '');	
		//echo $page  . '<br />';
		$base_url = append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext?f=$forum_id&amp;t=$topic_id&amp;page=all");
		///////////// All post on page
		$max_total_posts = 200;
		if (($total_posts <= $max_total_posts) && $enable_vapit)
	//	if ($total_posts <= $max_total_posts)


		if ($page == 'all')
		{
		
			//$this->pagination->get_on_page($total_posts, $per_page, 0);
			$per_page = $total_posts;
			$start = 0;	
			
			$event['start'] = $start;
			
			$this->config['posts_per_page']= $per_page;
			$this->pagination->generate_template_pagination($base_url, 'pagination' , 'start', $total_posts, $this->config['posts_per_page'], $start);

		}


		$this->template->assign_vars(array(
			'U_TOPIC_ALL'	=> append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext?f=$forum_id&amp;t=$topic_id&amp;page=all"),
		//	'S_TOPIC_ALL'	=> ($total_posts <= $max_total_posts ) ? true : false 
			'S_TOPIC_ALL'	=> (($total_posts <= $max_total_posts ) && $enable_vapit) ? true : false 
		));
	}
//////////////

	
	public function acp_forums_request_data_view_all_post($event)
	{
		$forum_data = $event['forum_data'];

		$forum_data += array(
			'enable_view_all_post'	=> $this->request->variable('enable_view_all_post', false),
		);
		
		$template_data = array(
			'S_ENABLE_VIEW_ALL_POST'			=> ($forum_data['enable_view_all_post']) ? true : false,
		);
		$event['forum_data'] = $forum_data;
	
	
	}
	public function acp_forums_initialise_data_view_all_post($event)
	{
		$this->user->add_lang_ext('hammer663/ViewAllPostsInTopic', 'info_acp_ViewAllPostsInTopic');

		$forum_data = $event['forum_data'];
		$forum_data += array(
			'enable_view_all_post'	=> false,
		);

		$event['forum_data'] = $forum_data;
	}

	public function acp_forums_display_form_view_all_post($event)
	{
		$forum_data = $event['forum_data'];
		$template_data = $event['template_data'];

		$template_data += array(
			'S_ENABLE_VIEW_ALL_POST'	=> ($forum_data['enable_view_all_post']) ? true : false,
		);

		$event['template_data'] = $template_data;
	}
	
//////////////	
}







