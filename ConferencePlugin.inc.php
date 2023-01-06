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

		}

		HookRegistry::register('Schema::get::issue', function ($hookName, $args) {
			$schema = $args[0];

			$this->addProperties($schema);
		});


		return $success;

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
		$fields[] = 'conferenceDOI';
		return false;
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
		$issueDao = DAORegistry::getDAO('IssueDAO'); /* @var $issueDao IssueDAO */


		$conferenceDOI  =  $smarty->getTemplateVars('issue')->getData('conferenceDOI');
		$smarty->assign('conferenceDOI', $conferenceDOI);

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
		if (array_key_exists('conferenceDOI',$requestVars)) {
			$conferenceDOI = $requestVars['conferenceDOI'];

			if ($issue && $conferenceDOI) {
				$issue->setData('conferenceDOI', $conferenceDOI);
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
		$schema->properties->conferenceDOI = (object)[
			'type' => 'string',
			'apiSummary' => true,
			'validation' => ['nullable']
		];
	}
}
