{**
 * plugins/generic/conference/templates/metadataForm.tpl
 *}

<div id="conferenceDate">
    {fbvFormSection title="plugins.generic.conference.metadata.conferenceDateBegin.title"}
    {fbvElement type="text" id="conferenceDateBegin" value=$conferenceDateBegin  required=false label="plugins.generic.conference.metadata.conferenceDateBegin"  class="datepicker"  size=$fbvStyles.size.SMALL inline=true }
    {fbvElement type="text" id="conferenceDateBegin" value=$conferenceDateBegin  required=false label="plugins.generic.conference.metadata.conferenceDatEnd"  class="datepicker"  size=$fbvStyles.size.SMALL inline=true}





    {/fbvFormSection}
</div>

