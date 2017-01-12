<?php
/**---------------------------------------------------------------------------------------------------------------
 *
 * Template Manager (Handlebars)
 *
 * ---------------------------------------------------------------------------------------------------------------*/

define ('GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP', PoP_ServerUtils::get_template_definition('block-locationposts-scrollmap'));
define ('GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP', PoP_ServerUtils::get_template_definition('block-locationposts-horizontalscrollmap'));
define ('GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP', PoP_ServerUtils::get_template_definition('block-authorlocationposts-scrollmap'));
define ('GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP', PoP_ServerUtils::get_template_definition('block-authorlocationposts-horizontalscrollmap'));
define ('GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP', PoP_ServerUtils::get_template_definition('block-taglocationposts-scrollmap'));
define ('GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP', PoP_ServerUtils::get_template_definition('block-taglocationposts-horizontalscrollmap'));


class GD_Custom_Template_Processor_CustomScrollMapSectionBlocks extends GD_EM_Template_Processor_ScrollMapBlocksBase {

	function get_templates_to_process() {
	
		return array(
			GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP,
			GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP,
			GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP,	
			GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP,
			GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP,	
			GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP,
		);
	}

	protected function is_postmap_block($template_id) {

		switch ($template_id) {
			
			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				return true;
		}

		return parent::is_postmap_block($template_id);
	}

	protected function get_block_inner_template($template_id) {

		$inner_templates = array(

			GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP => GD_TEMPLATE_SCROLL_LOCATIONPOSTS_MAP,		
			GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP => GD_TEMPLATE_SCROLL_LOCATIONPOSTS_HORIZONTALMAP,
			GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP => GD_TEMPLATE_SCROLL_LOCATIONPOSTS_MAP,
			GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP => GD_TEMPLATE_SCROLL_LOCATIONPOSTS_HORIZONTALMAP,
			GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP => GD_TEMPLATE_SCROLL_LOCATIONPOSTS_MAP,
			GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP => GD_TEMPLATE_SCROLL_LOCATIONPOSTS_HORIZONTALMAP,
		);

		return $inner_templates[$template_id];
	}

	function get_title($template_id) {
	
		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			// case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				return GD_Template_Processor_CustomSectionBlocksUtils::get_author_title();

			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:

				return gd_navigation_menu_item(POPTHEME_WASSUP_SECTIONPROCESSORS_PAGE_LOCATIONPOSTS, true).sprintf(
					sprintf(
						__('%s tagged with “#%s”', 'poptheme-wassup'), 
						gd_get_categoryname(POPTHEME_WASSUP_SECTIONPROCESSORS_CAT_LOCATIONPOSTS, 'plural')
					), //__('Location posts tagged with “#%s”', 'poptheme-wassup'),
					single_tag_title('', false)
				);
				// return '<i class="fa fa-fw fa-hashtag"></i>'.single_tag_title('', false);
		}
		
		return parent::get_title($template_id);
	}

	function get_submenu($template_id) {

		// Do not add for the quickview
		$vars = GD_TemplateManager_Utils::get_vars();
		if (!$vars['fetching-json-quickview']) {

			switch ($template_id) {

				case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
				// case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:

					return GD_TEMPLATE_SUBMENU_AUTHOR;
			}
		}
		
		return parent::get_submenu($template_id);
	}

	protected function show_fetchmore($template_id) {

		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			// case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			// case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			
				return true;
		}

		return parent::show_fetchmore($template_id);
	}

	protected function get_title_link($template_id) {
	
		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				global $author;
				$url = get_author_posts_url($author);
				$page = $this->get_block_page($template_id);
				return GD_TemplateManager_Utils::add_tab($url, $page);

			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				$url = get_tag_link(get_queried_object_id());
				$page = $this->get_block_page($template_id);
				return GD_TemplateManager_Utils::add_tab($url, $page);
		}

		return parent::get_title_link($template_id);
	}

	function get_filter_template($template_id) {

		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				return GD_TEMPLATE_FILTER_LOCATIONPOSTS;

			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				return GD_TEMPLATE_FILTER_AUTHORLOCATIONPOSTS;

			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				return GD_TEMPLATE_FILTER_TAGLOCATIONPOSTS;
		}
		
		return parent::get_filter_template($template_id);
	}

	function get_dataload_source($template_id, $atts) {

		global $gd_template_settingsmanager;
		
		switch ($template_id) {

			// These are the Profile Blocks, they will always be used inside an is_author() page
			// Then, point them not the is_page() page, but to the author url (mesym.com/p/mesym) and
			// an attr "tab" indicating this page through its path. This way, users can go straight to their 
			// information by typing their url: mesym.com/p/mesym?tab=events. Also good for future API
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				$ret = GD_Template_Processor_CustomSectionBlocksUtils::get_author_dataloadsource($template_id);
				break;

			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				$ret = GD_Template_Processor_CustomSectionBlocksUtils::get_tag_dataloadsource($template_id);
				break;

			default:

				$ret = parent::get_dataload_source($template_id, $atts);
				break;
		}

		$maps = array(
			GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP,
			GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP,
			GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP,
		);
		$horizontalmaps = array(
			GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP,
			GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP,
			GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP,
		);

		if (in_array($template_id, $maps)) {
			
			$format = GD_TEMPLATEFORMAT_MAP;
		}
		elseif (in_array($template_id, $horizontalmaps)) {
			
			$format = GD_TEMPLATEFORMAT_HORIZONTALMAP;
		}

		if ($format) {

			$ret = add_query_arg(GD_URLPARAM_FORMAT, $format, $ret);
		}
	
		return $ret;
	}

	protected function get_block_page($template_id) {

		global $gd_template_settingsmanager;

		switch ($template_id) {

			// These are the Profile Blocks, they will always be used inside an is_author() page
			// Then, point them not the is_page() page, but to the author url (mesym.com/p/mesym) and
			// an attr "tab" indicating this page through its path. This way, users can go straight to their 
			// information by typing their url: mesym.com/p/mesym?tab=events. Also good for future API
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				if ($page = $gd_template_settingsmanager->get_block_page($template_id, GD_SETTINGS_HIERARCHY_AUTHOR)) {

					return $page;
				}
				break;

			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				if ($page = $gd_template_settingsmanager->get_block_page($template_id, GD_SETTINGS_HIERARCHY_TAG)) {

					return $page;
				}
				break;
		}
	
		return parent::get_block_page($template_id);
	}

	protected function get_dataload_query_args($template_id, $atts) {

		$ret = parent::get_dataload_query_args($template_id, $atts);
		
		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				$ret['cat'] = POPTHEME_WASSUP_SECTIONPROCESSORS_CAT_LOCATIONPOSTS;
				break;
		}

		// If they are horizontal, then bring all the results, until we fix how to place the load more button inside of the horizontal scrolling div
		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				$ret['limit'] = '-1';
				break;
		}

		return $ret;
	}

	protected function get_runtime_dataload_query_args($template_id, $atts) {

		$ret = parent::get_runtime_dataload_query_args($template_id, $atts);
		
		switch ($template_id) {

			// Filter by the Profile/Community
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				GD_Template_Processor_CustomSectionBlocksUtils::add_dataloadqueryargs_authorcontent($ret);
				break;

			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				GD_Template_Processor_CustomSectionBlocksUtils::add_dataloadqueryargs_tagcontent($ret);
				break;
		}

		return $ret;
	}

	protected function get_controlgroup_top($template_id) {

		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:

				// Allow URE to add the ContentSource switch
				return GD_TEMPLATE_CONTROLGROUP_BLOCKAUTHORPOSTLIST;

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			
				return GD_TEMPLATE_CONTROLGROUP_BLOCKPOSTLIST;

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:
		
				return GD_TEMPLATE_CONTROLGROUP_BLOCKMAPPOSTLIST;
		}

		return parent::get_controlgroup_top($template_id);
	}

	function get_latestcount_template($template_id) {
	
		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:

				return GD_TEMPLATE_LATESTCOUNT_LOCATIONPOSTS;

			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				return GD_TEMPLATE_LATESTCOUNT_AUTHOR_LOCATIONPOSTS;

			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				return GD_TEMPLATE_LATESTCOUNT_TAG_LOCATIONPOSTS;
		}

		return parent::get_latestcount_template($template_id);
	}

	protected function get_messagefeedback($template_id) {
	
		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				return GD_TEMPLATE_MESSAGEFEEDBACK_LOCATIONPOSTS;
		}

		return parent::get_messagefeedback($template_id);
	}

	protected function get_messagefeedback_position($template_id) {

		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			// case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			// case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:

				return 'bottom';
		}

		return parent::get_messagefeedback_position($template_id);
	}

	protected function get_iohandler($template_id) {
		
		return GD_DATALOAD_IOHANDLER_LIST;
	}

	function get_dataloader($template_id) {

		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				return GD_DATALOADER_POSTLIST;
		}

		return parent::get_dataloader($template_id);
	}

	function get_map_direction($template_id, $atts) {

		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			
				return 'horizontal';
		}

		return parent::get_map_direction($template_id, $atts);
	}

	function get_data_setting($template_id, $atts) {

		$ret = parent::get_data_setting($template_id, $atts);
	
		$maps = array(
			GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP,
			GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP,
			GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP,
		);
		$horizontalmaps = array(
			GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP,
			GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP,
			GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP,
		);

		// Important: set always this value, because the IOHandler used by all different blocks is the same!
		// So if not restarting, the display will be the same as the previous one, and sometimes it doesn't need the display
		// (Eg: tables)
		// $ret[GD_URLPARAM_FORMAT] = '';
		if (in_array($template_id, $maps)) {
			
			$format = GD_TEMPLATEFORMAT_MAP;
		}
		elseif (in_array($template_id, $horizontalmaps)) {
			
			$format = GD_TEMPLATEFORMAT_HORIZONTALMAP;
		}

		if ($format) {
			$ret['iohandler-atts'][GD_URLPARAM_FORMAT] = $format;
		}
		
		return $ret;
	}

	function init_atts($template_id, &$atts) {

		switch ($template_id) {

			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_LOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_AUTHORLOCATIONPOSTS_HORIZONTALSCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_SCROLLMAP:
			case GD_TEMPLATE_BLOCK_TAGLOCATIONPOSTS_HORIZONTALSCROLLMAP:

				$this->append_att($template_id, $atts, 'class', 'block-locationposts-scrollmap');
				break;
		}
	
		return parent::init_atts($template_id, $atts);
	}

}


/**---------------------------------------------------------------------------------------------------------------
 * Initialization
 * ---------------------------------------------------------------------------------------------------------------*/
new GD_Custom_Template_Processor_CustomScrollMapSectionBlocks();