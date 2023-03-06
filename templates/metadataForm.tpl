{**
 * plugins/generic/conference/templates/metadataForm.tpl
 *}

<div id="conferenceDOI">
    {fbvFormSection title="plugins.generic.conference.manager.settings.conferenceDOI.title"}
    {fbvElement type="text" id="conferenceDOI" value=$conferenceDOI  required=false label="plugins.generic.conference.manager.settings.conferenceDOI" maxlength="40" size=$fbvStyles.size.MEDIUM disabled=false}
    {/fbvFormSection}
</div>

