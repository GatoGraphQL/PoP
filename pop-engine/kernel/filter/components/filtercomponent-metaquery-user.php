<?php

class GD_FilterComponent_Metaquery_User extends GD_FilterComponent_Metaquery {
	
	function get_metaquery_key() {
	
		return GD_MetaManager::get_meta_key($this->get_meta_key(), GD_META_TYPE_USER);
	}

	function get_meta_key() {
	
		return null;
	}
}
