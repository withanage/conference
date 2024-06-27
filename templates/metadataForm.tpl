{**
 * plugins/generic/conference/templates/metadataForm.tpl
 *}

<div id="conferenceDate">
    {fbvFormSection title="plugins.generic.conference.metadata.conferenceDate.title"}
		{fbvElement type="text" id="conferenceDateBegin" value=$conferenceDateBegin  required=false label="plugins.generic.conference.metadata.conferenceDateBegin"  class="datepicker"  size=$fbvStyles.size.SMALL inline=true }
		{fbvElement type="text" id="conferenceDateEnd" value=$conferenceDateEnd  required=false label="plugins.generic.conference.metadata.conferenceDateEnd"  class="datepicker"  size=$fbvStyles.size.SMALL inline=true}
   {/fbvFormSection}
</div>
<div id="conferencePlace">
    {fbvFormSection title="plugins.generic.conference.metadata.conferencePlace.title"}
    {fbvElement type="text" id="conferencePlaceStreet" value=$conferencePlaceStreet  required=false label="plugins.generic.conference.metadata.conferencePlaceStreet"   size=$fbvStyles.size.SMALL inline=true}
    {fbvElement type="text" id="conferencePlaceCity" value=$conferencePlaceCity  required=false label="plugins.generic.conference.metadata.conferencePlaceCity"   size=$fbvStyles.size.SMALL inline=true}
    {fbvElement type="text" id="conferencePlaceCountry" value=$conferencePlaceCountry  required=false label="plugins.generic.conference.metadata.conferencePlaceCountry"   size=$fbvStyles.size.SMALL inline=true}
    {/fbvFormSection}
</div>


<div id="conferenceOnlineStatus">
    {fbvFormSection for="conferenceOnlineStatus" size=$fbvStyles.size.MEDIUM list=true}

        {fbvElement type="checkbox" id="conferenceOnline" label="plugins.generic.conference.metadata.conferenceOnline.checkBox" checked=$conferenceOnline}

    {/fbvFormSection}
</div>

