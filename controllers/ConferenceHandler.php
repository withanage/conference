<?php

/**
 * @file ConferenceHandler.php
 *
 * Copyright (c) 2003-2024 Simon Fraser University
 * Copyright (c) 2003-2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class ConferenceHandler
 * @brief Handles request for Conference plugin.
 */

namespace APP\plugins\generic\conference\controllers;

use APP\handler\Handler;
use APP\plugins\generic\conference\Conference;
use PKP\plugins\PluginRegistry;
use PKP\security\Role;

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
