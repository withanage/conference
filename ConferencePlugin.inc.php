<?php
import('lib.pkp.classes.plugins.GenericPlugin');

class ConferencePlugin extends GenericPlugin


{

	/**
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
			HookRegistry::register('Templates::Editor::Issues::IssueData::AdditionalMetadata', array($this, 'metadataFieldEdit'));
			HookRegistry::register('issueform::execute', array($this, 'formExecute'));
			HookRegistry::register('issuedao::getAdditionalFieldNames', array($this, 'handleAdditionalFieldNames'));
			HookRegistry::register('LoadComponentHandler', array($this, 'setupHandler'));
			HookRegistry::register('TemplateResource::getFilename', array($this, '_overridePluginTemplates'));

			$locale = AppLocale::getLocale();
			$customLocalePath = $this->getPluginPath() . "/customLocale/" . $locale . '/';
			if(is_dir($customLocalePath)) {
				$dir = new RecursiveDirectoryIterator($customLocalePath);
				$files = new RecursiveIteratorIterator($dir);

				foreach ($files as $file) {
					$pathinfo = pathinfo($file->getFileName());
					if ($pathinfo && $pathinfo['extension'] == 'po') {
						AppLocale::registerLocaleFile($locale, Core::getBaseDir() . '/' . $file);
					}
				}

				HookRegistry::register('PKPLocale::registerLocaleFile', array($this, 'addCustomLocale'));
			}
		}

		HookRegistry::register('Schema::get::issue', function ($hookName, $args) {
			$schema = $args[0];
			$this->addProperties($schema);
		});

		return $success;

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
	 * @param $args
	 * @return bool
	 */
	function addCustomLocale($hookName, $args)
	{
		import('lib.pkp.classes.file.ContextFileManager');
		$locale =& $args[0];
		$request = Application::get()->getRequest();
		$context = $request->getContext();
		$localeFilename =& $args[1];
		$customLocalePath = Core::getBaseDir() . '/' . $this->getPluginPath() . "/customLocale/".$locale."/$localeFilename";
		if ($context && $customLocalePath) {
			$contextFileManager = new ContextFileManager($context->getId());

			if ($contextFileManager->fileExists($customLocalePath)) {
				AppLocale::registerLocaleFile($locale, $customLocalePath, false);
			}
		}

		return true;
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
		import('plugins.generic.conference.controllers.ConferenceHandler');
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
