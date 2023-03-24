<?php
import('lib.pkp.classes.plugins.GenericPlugin');

class ConferencePlugin extends GenericPlugin


{
	public function register($category, $path, $mainContextId = NULL)
	{
		$success = parent::register($category, $path, $mainContextId);
		if ($success && $this->getEnabled($mainContextId)) {

			HookRegistry::register('Templates::Editor::Issues::IssueData::AdditionalMetadata', array($this, 'metadataFieldEdit'));
			HookRegistry::register('issueform::execute', array($this, 'formExecute'));
			HookRegistry::register('issuedao::getAdditionalFieldNames', array($this, 'handleAdditionalFieldNames'));
			HookRegistry::register('LoadComponentHandler', array($this, 'setupHandler'));
			HookRegistry::register('TemplateResource::getFilename', array($this, '_overridePluginTemplates'));


			$locale = AppLocale::getLocale();
			$customLocalePath = $this->getPluginPath() ."/conferenceLocale/" . $locale . "/customLocale.po";
			if (file_exists($customLocalePath)) {
				AppLocale::registerLocaleFile($locale, $customLocalePath);
			}

		}

		HookRegistry::register('Schema::get::issue', function ($hookName, $args) {
			$schema = $args[0];
			$this->addProperties($schema);
		});

	return $success;

	}
	function issueViewHandler ($hookName, $params){
		$request = Application::get()->getRequest();
		$templateManager = TemplateManager::getManager($request);

		$metadataView = $templateManager->fetch($this->getTemplateResource('metadataView.tpl'));
		$templateManager->assign('metadataView', $metadataView );
		return $metadataView;
	}

	function setupHandler($hookName, $params) {
		import('plugins.generic.conference.controllers.ConferenceHandler');
			ConferenceHandler::setPlugin($this);
	}

	/***
	 * @param $hookName
	 * @param $params
	 * @return false
	 */
	function handleAdditionalFieldNames($hookName, $params) : bool
	{
		$fields =& $params[1];
		foreach ($this->getAdditionalFields() as $field) {
			$fields[] = $field;
			}
		return false;
	}


	function getAdditionalFields(){
		$fiellds = array(
			'conferenceDateBegin',
			'conferenceDateEnd',
			'conferencePlaceStreet',
			'conferencePlaceCity',
			'conferencePlaceCountry'
		);
		return $fiellds;
	}



	/**
	 * @return string
	 */
	public function getDisplayName()
	{
		return 'Support Conferences';
	}


	/***
	 * @return string
	 */
	public function getDescription() :string
	{
		return '';
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
		if($issue) {
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
		$requestVars = $this->getRequest()->getUserVars();

		foreach ($this->getAdditionalFields() as $field) {
			if (array_key_exists($field, $requestVars)) {
				$conferenceDateBegin = $requestVars[$field];
				if ($issue && $conferenceDateBegin) {
					$issue->setData($field, $conferenceDateBegin);
				}
			}
		}
		return  false;


	}

	/**
	 * @param mixed $schema
	 * @return void
	 */
	function addProperties(mixed $schema): void
	{
		foreach ($this->getAdditionalFields() as $field) {
			$schema->properties->$field = (object)[
				'type' => 'string',
				'apiSummary' => true,
				'validation' => ['nullable']
			];
		}
	}
}
