<?php

class PoP_ModuleManager_Vars {

	public static $vars = array();
	public static $query;

	public static function reset() {

		self::$vars = array();

		// Allow WP to set the new $query
		do_action('PoP_ModuleManager_Utils:reset');
	}

	public static function set_query($query) {

		self::$query = $query;
	}

	public static function get_modulepaths() {

		$ret = array();
		if ($paths = $_REQUEST[GD_URLPARAM_MODULEPATHS]) {

			if (!is_array($paths)) {
				$paths = array($paths);
			}

			// If any path is a substring from another one, then it is its root, and only this one will be taken into account, so remove its substrings
			// Eg: toplevel.pagesection-top is substring of toplevel, so if passing these 2 modulepaths, keep only toplevel
			// Check that the last character is ".", to avoid toplevel1 to be removed
			$paths = array_filter($paths, function($item) use($paths) {

				foreach ($paths as $path) {

					if (strlen($item) > strlen($path) && strpos($item, $path) === 0 && $item[strlen($path)] == POP_CONSTANT_MODULESTARTPATH_SEPARATOR) {

						return false;
					}
				}

				return true;
			});

			foreach ($paths as $path) {

				// Each path must be converted to an array of the modules
				$ret[] = PoP_ModulePathManager_Utils::recast_module_path($path);
			}
		}

		return $ret;
	}

	public static function get_hierarchy($query) {

		$cmsapi = PoP_CMS_FunctionAPI_Factory::get_instance();
		
		// Check all the available hierarchies, and analyze the query against each. 
		// The first one that matches, then that's the hierarchy
		foreach (PoPEngine_Module_HierarchyManager::get_hierarchies() as $maybe_hierarchy) {

			if ($cmsapi->query_is_hierarchy($query, $maybe_hierarchy)) {
				return $maybe_hierarchy;
			}
		}

		// Default one
		return GD_SETTINGS_HIERARCHY_PAGE;
	}

	public static function get_query_object($query) {

		// If there's no query, set the global one
		if (!$query) {

			$cmsapi = PoP_CMS_FunctionAPI_Factory::get_instance();
			return $cmsapi->get_global_query();
		}

		return $query;
	}

	public static function get_vars() {

		if (self::$vars) {
			return self::$vars;
		}

		// From the query object we are able to obtain the hierarchy for the current request. Based on the global $wp_query object
		self::$query = apply_filters(
			'PoP_ModuleManager_Utils:query',
			self::get_query_object(self::$query)
		);

		// The hierarchy is a concept taken from WordPress. It depends on the structure of the URL
		// By default is a page, since everything is a page unless the URL suits a more specialized hierarchy
		$hierarchy = apply_filters(
			'PoP_ModuleManager_Utils:hierarchy',
			self::get_hierarchy(self::$query)
		);

		$output = $_REQUEST[GD_URLPARAM_OUTPUT];
		$dataoutputitems = $_REQUEST[GD_URLPARAM_DATAOUTPUTITEMS];
		$datasources = $_REQUEST[GD_URLPARAM_DATASOURCES];
		$datastructure = $_REQUEST[GD_URLPARAM_DATASTRUCTURE];
		$dataoutputmode = $_REQUEST[GD_URLPARAM_DATAOUTPUTMODE];
		$target = $_REQUEST[GD_URLPARAM_TARGET];
		$mangled = PoP_ServerUtils::is_mangled() ? '' : GD_URLPARAM_MANGLED_NONE;
		$tab = $_REQUEST[GD_URLPARAM_TAB];
		$action = $_REQUEST[GD_URLPARAM_ACTION];

		$outputs = apply_filters(
			'PoP_ModuleManager_Utils:outputs',
			array(
				GD_URLPARAM_OUTPUT_HTML,
				GD_URLPARAM_OUTPUT_JSON,
			)
		);
		if (!in_array($output, $outputs)) {
			$output = GD_URLPARAM_OUTPUT_HTML;
		}

		// Target/Module default values (for either empty, or if the user is playing around with the url)
		$alldatasources = array(
			GD_URLPARAM_DATASOURCES_ONLYMODEL,
			GD_URLPARAM_DATASOURCES_MODELANDREQUEST,
		);
		if (!in_array($datasources, $alldatasources)) {
			$datasources = GD_URLPARAM_DATASOURCES_MODELANDREQUEST;
		}

		$dataoutputmodes = array(
			GD_URLPARAM_DATAOUTPUTMODE_SPLITBYSOURCES,
			GD_URLPARAM_DATAOUTPUTMODE_COMBINED,
		);
		if (!in_array($dataoutputmode, $dataoutputmodes)) {
			$dataoutputmode = GD_URLPARAM_DATAOUTPUTMODE_SPLITBYSOURCES;
		}

		if ($dataoutputitems && !is_array($dataoutputitems)) {
			$dataoutputitems = array($dataoutputitems);
		}
		$alldataoutputitems = apply_filters(
			'PoP_ModuleManager_Utils:dataoutputitems',
			array(
				GD_URLPARAM_DATAOUTPUTITEMS_MODULESETTINGS,
	            GD_URLPARAM_DATAOUTPUTITEMS_MODULEDATA,
	            GD_URLPARAM_DATAOUTPUTITEMS_DATABASES,
	            GD_URLPARAM_DATAOUTPUTITEMS_SESSION,
	        )
		);
		$dataoutputitems = array_intersect(
			$dataoutputitems ?? array(),
			$alldataoutputitems
		);
		if (!$dataoutputitems) {
			$dataoutputitems = $alldataoutputitems;
		}
		
		// If not target, or invalid, reset it to "main"
		// We allow an empty target if none provided, so that we can generate the settings cache when no target is provided
		// (ie initial load) and when target is provided (ie loading pageSection)
		$targets = apply_filters(
			'PoP_ModuleManager_Utils:targets',
			array(
				POP_TARGET_MAIN,
			)
		);
		if (!in_array($target, $targets)) {
			$target = POP_TARGET_MAIN;
		}
		
		$modulefilter_manager = PoP_ModuleFilterManager_Factory::get_instance();
		$modulefilter = $modulefilter_manager->get_selected_filter_name();
		
		$fetching_site = is_null($modulefilter);
		$loading_site = $fetching_site && $output == GD_URLPARAM_OUTPUT_HTML;

		// Format: if not set, then use the default one: "settings-format" can be defined at the settings, and persists as the default option
		// Use 'format' the first time to set the 'settingsformat' value. So if "loading_site()" is true, take the value from 'format'
		if ($loading_site) {
			$settingsformat = $_REQUEST[GD_URLPARAM_FORMAT];
		}
		else {
			$settingsformat = $_REQUEST[GD_URLPARAM_SETTINGSFORMAT];
		}
		$format = isset($_REQUEST[GD_URLPARAM_FORMAT]) ? $_REQUEST[GD_URLPARAM_FORMAT] : $settingsformat;
		// Comment Leo 13/11/2017: If there is not format, then set it to 'default'
		// This is needed so that the /generate/ generated configurations under a $model_instance_id (based on the value of $vars)
		// can match the same $model_instance_id when visiting that page
		if (!$format) {
			$format = POP_VALUES_DEFAULT;
		}
		global $gd_theme_manager;
		self::$vars = array(
			'hierarchy' => $hierarchy,
			'output' => $output,
			'modulefilter' => $modulefilter,
			'actionpath' => $_REQUEST[GD_URLPARAM_ACTIONPATH],
			'target' => $target,
			'dataoutputitems' => $dataoutputitems,
			'datasources' => $datasources,
			'datastructure' => $datastructure,
			'dataoutputmode' => $dataoutputmode,
			'mangled' => $mangled,
			'format' => $format,
			'settingsformat' => $settingsformat,
			'tab' => $tab,
			'action' => $action,
			'theme' => $gd_theme_manager->get_theme() ? $gd_theme_manager->get_theme()->get_name() : '',
			'thememode' => $gd_theme_manager->get_thememode() ? $gd_theme_manager->get_thememode()->get_name() : '',
			'themestyle' => $gd_theme_manager->get_themestyle() ? $gd_theme_manager->get_themestyle()->get_name() : '',
			'theme-isdefault' => $gd_theme_manager->is_default_theme(),
			'thememode-isdefault' => $gd_theme_manager->is_default_thememode(),
			'themestyle-isdefault' => $gd_theme_manager->is_default_themestyle(),
			'theme-path' => $gd_theme_manager->get_theme_path(),
			'loading-site' => $loading_site,
			'fetching-site' => $fetching_site,
		);

		if ($modulefilter == POP_MODULEFILTER_MODULEPATHS) {
			
			self::$vars['modulepaths'] = self::get_modulepaths();
		}
		elseif ($modulefilter == POP_MODULEFILTER_HEADMODULE) {
			
			self::$vars['headmodule'] = $_REQUEST[GD_URLPARAM_HEADMODULE];
		}
		

		if (PoP_ServerUtils::enable_config_by_params()) {

			self::$vars['config'] = $_REQUEST[POP_URLPARAM_CONFIG];
		}

		// The global state below, will need to be hooked in by pop-application
		self::calculate_and_set_vars_state(true);

		// Allow for plug-ins to add their own vars
		do_action(
			'PoP_ModuleManager_Utils:add_vars', 
			array(&self::$vars), 
			self::$query
		);

		return self::$vars;
	}

	public static function set_hierarchy_in_global_state() {

		$hierarchy = self::$vars['hierarchy'];
		self::$vars['global-state']['is-page'] = $hierarchy == GD_SETTINGS_HIERARCHY_PAGE;
		self::$vars['global-state']['is-home'] = $hierarchy == GD_SETTINGS_HIERARCHY_HOME;
		self::$vars['global-state']['is-tag'] = $hierarchy == GD_SETTINGS_HIERARCHY_TAG;
		self::$vars['global-state']['is-single'] = $hierarchy == GD_SETTINGS_HIERARCHY_SINGLE;
		self::$vars['global-state']['is-author'] = $hierarchy == GD_SETTINGS_HIERARCHY_AUTHOR;
		self::$vars['global-state']['is-404'] = $hierarchy == GD_SETTINGS_HIERARCHY_404;
		self::$vars['global-state']['is-front-page'] = $hierarchy == GD_SETTINGS_HIERARCHY_FRONTPAGE;
		self::$vars['global-state']['is-search'] = $hierarchy == GD_SETTINGS_HIERARCHY_SEARCH;
		self::$vars['global-state']['is-category'] = $hierarchy == GD_SETTINGS_HIERARCHY_CATEGORY;
		self::$vars['global-state']['is-archive'] = $hierarchy == GD_SETTINGS_HIERARCHY_ARCHIVE;
	}

	public static function calculate_and_set_vars_state($reset = true) {

		$hierarchy = self::$vars['hierarchy'];

		// Reset will set the queried object from $query. By default it's true, but when calculating the resources for the resourceloader, in which the queried-object is set manually, it must be false
		if ($reset) {

			self::$vars['global-state'] = array();
			self::set_hierarchy_in_global_state();

			do_action(
				'PoP_ModuleManager_Utils:calculate_and_set_vars_state:reset', 
				array(&self::$vars), 
				self::$query
			);
		}

		do_action(
			'PoP_ModuleManager_Utils:calculate_and_set_vars_state', 
			array(&self::$vars), 
			self::$query
		);

		// Function `get_page_module_by_most_allmatching_vars_properties` actually needs to access all values in $vars
		// Hence, calculate only at the very end
		if ($reset) {

			// If filtering module by "maincontent", then calculate which is the main content module
			if (self::$vars['modulefilter'] == POP_MODULEFILTER_MAINCONTENTMODULE) {

				$pop_module_pagemoduleprocessor_manager = PoPEngine_Module_PageModuleProcessorManager_Factory::get_instance();
				self::$vars['maincontentmodule'] = $pop_module_pagemoduleprocessor_manager->get_page_module_by_most_allmatching_vars_properties(POP_PAGEMODULEGROUPPLACEHOLDER_MAINCONTENTMODULE);
			}
		}
	}
}
