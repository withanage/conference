<?php
import('lib.pkp.classes.plugins.GenericPlugin');
class ConferencePlugin extends GenericPlugin {
	public function register($category, $path, $mainContextId = NULL) {
		$success = parent::register($category, $path, $mainContextId);
		if ($success && $this->getEnabled($mainContextId)) {
			HookRegistry::register('Templates::Editor::Issues::IssueData::AdditionalMetadata', array($this, 'metadataFieldEdit'));
			HookRegistry::register('issueform::execute', array($this, 'metadataFieldExecute'));
			HookRegistry::register('issuedao::getAdditionalFieldNames', array($this, 'handleAdditionalFieldNames'));

		}

		HookRegistry::register('Schema::get::issue', function ($hookName, $args) {
			$schema = $args[0];

			$schema->properties->conferenceDOI = (object)[
				'type' => 'string',
				'apiSummary' => true,
				'validation' => ['nullable']
			];
		});

		return $success;

	}

	function handleAdditionalFieldNames($hookName, $params) {
		$fields =& $params[1];
		$fields[] = 'conferenceDOI';
		return false;
	}


	/**
	 * Provide a name for this plugin
	 *
	 * The name will appear in the Plugin Gallery where editors can
	 * install, enable and disable plugins.
	 */
	public function getDisplayName() {
		return 'Support Conferences';
	}

	/**
	 * Provide a description for this plugin
	 *
	 * The description will appear in the Plugin Gallery where editors can
	 * install, enable and disable plugins.
	 */
	public function getDescription() {
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
		$output .= $smarty->fetch($this->getTemplateResource('metadataForm.tpl'));
		return false;
	}

	function metadataFieldExecute($hookName, $params)
	{
		$request = $this->getRequest();
		$requestVars  = $request->getUserVars();
		$conferenceDOI = $requestVars['conferenceDOI'];
		$issue =& $params[0]->issue;
		if ($issue && $conferenceDOI) {
			$issue->setData('conferenceDOI', $conferenceDOI);
		}


	}
}
