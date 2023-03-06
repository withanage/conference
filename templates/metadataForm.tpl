{**
 * plugins/generic/conference/templates/metadataForm.tpl
 *}

<div id="conferenceDate">
    {fbvFormSection title="plugins.generic.conference.metadata.conferenceDate.title"}
    {fbvElement type="text" id="conferenceDateBegin" value=$conferenceDateBegin  required=false label="plugins.generic.conference.metadata.conferenceDateBegin"  class="datepicker"  size=$fbvStyles.size.SMALL inline=true }
    {fbvElement type="text" id="conferenceDateEnd" value=$conferenceDateEnd  required=false label="plugins.generic.conference.metadata.conferenceDateEnd"  class="datepicker"  size=$fbvStyles.size.SMALL inline=true}





    {/fbvFormSection}
</div>

