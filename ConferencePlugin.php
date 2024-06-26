<?php

/**
 * @file plugins/generic/conference/ConferencePlugin.php
 *
 * Copyright (c) 2017-2020 Simon Fraser University
 * Copyright (c) 2017-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ConferencePlugin
 *
 * @ingroup plugins_generic_conference
 *
 * @brief Conference plugin class.
 */

namespace APP\plugins\generic\conference;

use APP\core\Application;
use APP\i18n\AppLocale;
use APP\plugins\generic\conference\controllers\ConferenceHandler;
use APP\template\TemplateManager;
use Exception;
use PKP\core\PKPApplication;
use PKP\core\PKPRequest;
use PKP\facades\Locale;
use PKP\file\ContextFileManager;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use stdClass;

class ConferencePlugin extends GenericPlugin
{
	public function __construct()
        {
                parent::__construct();
                $this->application = Application::get()->getName();
        }

	/**
         * @copydoc Plugin::register()
	 * @param $category
         * @param $path
         * @param $mainContextId
         * @return bool
         */

	public function register($category, $path, $mainContextId = NULL)
	{
		$success = parent::register($category, $path, $mainContextId);
		if ($success && $this->getEnabled($mainContextId)) {
			// issue metadata extension
			Hook::add('Templates::Editor::Issues::IssueData::AdditionalMetadata', array($this, 'metadataFieldEdit'));
			Hook::add('issueform::execute', array($this, 'formExecute'));
			Hook::add('issuedao::getAdditionalFieldNames', array($this, 'handleAdditionalFieldNames'));
			Hook::add('LoadComponentHandler', array($this, 'setupHandler'));
			Hook::add('TemplateResource::getFilename', array($this, '_overridePluginTemplates'));

			$locale = AppLocale::getLocale();
			$customLocalePath = $this->getPluginPath() . "/customLocale/";
			
			if(is_dir($customLocalePath)) {
				$this->addConferenceLocalizationOverriding($customLocalePath);
			}
		}

		Hook::add('Schema::get::issue', function ($hookName, $args) {
			$schema = $args[0];
			$this->addProperties($schema);
		});

		return $success;

	}

	public function addConferenceLocalizationOverriding($path): void
	{
		Locale::registerPath($path, PHP_INT_MAX);
	}

	/**
	 * @param mixed $schema
	 * @return void
	 */
	function addProperties($schema): void
	{
		foreach ($this->getAdditionalFields() as $field) {
			$schema->properties->$field = (object)[
				'type' => 'string',
				'apiSummary' => true,
				'validation' => ['nullable']
			];
		}
	}

	/**
	 * @return string[]
	 */
	function getAdditionalFields()
	{
		$fiellds = array(
			'conferenceDateBegin',
			'conferenceDateEnd',
			'conferencePlaceStreet',
			'conferencePlaceCity',
			'conferencePlaceCountry',
			'conferenceOnline'
		);
		return $fiellds;
	}

	/**
	 * @param $hookName
	 * @param $params
	 * @return false|mixed|string|null
	 */
	function issueViewHandler($hookName, $params)
	{
		$request = Application::get()->getRequest();
		$templateManager = TemplateManager::getManager($request);

		$metadataView = $templateManager->fetch($this->getTemplateResource('metadataView.tpl'));
		$templateManager->assign('metadataView', $metadataView);
		return $metadataView;
	}

	/**
	 * @param $hookName
	 * @param $params
	 * @return void
	 */
	function setupHandler($hookName, $params)
	{
		ConferenceHandler::setPlugin($this);
	}

	/***
	 * @param $hookName
	 * @param $params
	 * @return false
	 */
	function handleAdditionalFieldNames($hookName, $params): bool
	{
		$fields =& $params[1];
		foreach ($this->getAdditionalFields() as $field) {
			$fields[] = $field;
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getDisplayName()
	{
		return __('plugins.generic.conference.displayName');
	}

	/***
	 * @return string
	 */
	public function getDescription(): string
	{
		return __('plugins.generic.conference.description');
	}

	/**
	 * Insert metadataEditForm
	 *
	 * @param $hookName
	 * @param $params
	 * @return false
	 */
	function metadataFieldEdit($hookName, $params): bool
	{
		$smarty =& $params[1];
		$output =& $params[2];

		$issue = $smarty->getTemplateVars('issue');
		if ($issue) {
			foreach ($this->getAdditionalFields() as $field) {
				$smarty->assign($field, $issue->getData($field));
			}

		}
		$output .= $smarty->fetch($this->getTemplateResource('metadataForm.tpl'));
		return false;
	}

	/**
	 * @param $hookName
	 * @param $params
	 * @return bool
	 */
	function formExecute($hookName, $params): bool
	{
		$issue =& $params[0]->issue;
		if ($issue) {

			$requestVars = $this->getRequest()->getUserVars();
			foreach ($this->getAdditionalFields() as $field) {
				if (array_key_exists($field, $requestVars)) {
					$issue->setData($field, $requestVars[$field]);
				}
				else {
					$issue->setData($field,"");
				}
			}
		}
		return false;
	}
}
