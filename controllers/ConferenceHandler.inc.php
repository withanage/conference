<?php

import('classes.handler.Handler');

class ConferenceHandler extends Handler
{
	static  $plugin;

	function __construct() {

		parent::__construct();

		$this->_plugin = PluginRegistry::getPlugin('generic', 'conference');
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_AUTHOR),

		);
	}
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}



}
