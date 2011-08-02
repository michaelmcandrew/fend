{* Edit or display Profile fields, when embedded in an online contribution or event registration form. *}
{if ! empty( $fields )}
   {strip} 
   {if $help_pre && $action neq 4}<div class="messages help">{$help_pre}</div>{/if} 
    
    {assign var=zeroField value="Initial Non Existent Fieldset"} 
    {assign var=fieldset  value=$zeroField} 
    {assign var=newfieldset  value=$zeroField} 
    {assign var=firstrec  value="Y"} 
    {assign var=drawfield  value="Y"} 
    
    {foreach from=$fields item=field key=fieldName} 
    {if $fieldName == "custom_44"}
    	{* Already drawn this field in the register.tpl *}
        {assign var=drawfield  value="N"} 
    {else}
        {assign var=drawfield  value="Y"} 
    {/if}
    {if $fieldName == "individual_prefix"}{assign var=newfieldset  value="Delegate details"}{/if}
    {if $fieldName == "custom_43"}{assign var=newfieldset  value="Member Organisation"}{/if}
    {if $fieldName == "custom_20"}
    	{assign var=newfieldset  value="Delegate's evening event attendance"}
        <tr> 
        <td class="option-label"><label for="email-5">{ts}Delegate's email address{/ts}<span class="marker" title="This field is required.">*</span></label></td> 
        <td class="view-value">{$emailfield.html|crmReplace:class:huge}<br /><span class="description">
        {if $additional == "Y"}
        {ts}This email address <u>will be required for this delegate to gain access to our online workshop booking facility and should be unique to this delegate</u> - please check it is entered correctly and keep a record of the address used.{/ts}
        {else}
        {ts}This email address <u>will be required for this delegate to gain access to our online workshop booking facility and should be unique to this delegate</u> - please check it is entered correctly and keep a record of the address used.{/ts}
	{/if}
	</span> </td> 
        </tr>     	
    {/if}
    {if $fieldName == "custom_22"}{assign var=newfieldset  value="Invoicing details"}{/if}
    {* Add check box for same as delegate address for ease of use *}
    {if $fieldName == "custom_25"}
        <tr> 
        <td class="option-label"><label for="same_as_billing">{ts}Invoice address same as delegate address?{/ts}</label></td>
        <td class="view-value">
<SCRIPT LANGUAGE="JavaScript">
{literal}
function copyAddress(sameAsBilling) {

	var orgName = document.getElementById('custom_14').value;
	var addressLine1 = document.getElementById('custom_15').value;
	var addressLine2 = document.getElementById('custom_16').value;
	var addressLine3 = document.getElementById('custom_17').value;
	var city = document.getElementById('custom_18').value;
	var postalCode = document.getElementById('custom_19').value;
	var country = document.getElementById('custom_34').value;

	var copy_orgName = document.getElementById('custom_24');
	var copy_addressLine1 = document.getElementById('custom_25');
	var copy_addressLine2 = document.getElementById('custom_26');
	var copy_addressLine3 = document.getElementById('custom_27');
	var copy_city = document.getElementById('custom_28');
	var copy_postalCode = document.getElementById('custom_29');
	var copy_country = document.getElementById('custom_35');

	if (sameAsBilling.checked) {
		if (copy_orgName) copy_firstName.value = orgName;
		if (copy_addressLine1) copy_addressLine1.value = addressLine1;
		if (copy_addressLine2) copy_addressLine2.value = addressLine2;
		if (copy_addressLine3) copy_addressLine3.value = addressLine3;
		if (copy_city) copy_city.value = city;
		if (copy_postalCode) copy_postalCode.value = postalCode;
		if (copy_country) copy_country.value = country;
	} else {
		if (copy_orgName) copy_orgName.value = "";
		if (copy_addressLine1) copy_addressLine1.value = "";
		if (copy_addressLine2) copy_addressLine2.value = "";
		if (copy_addressLine3) copy_addressLine3.value = "";
		if (copy_city) copy_city.value = "";
		if (copy_postalCode) copy_postalCode.value = "";
		if (copy_country) copy_country.value = "";
	}
}
{/literal}
</SCRIPT>
<input type="checkbox" id="same_as_billing" value="" onClick="copyAddress(this)"></td>
        </tr>     	
    {/if}
{*    {if $fieldName == "custom_31"} *}
{*    	<div class="messages help">Social Companion text....</div> *}
{*    	{assign var=newfieldset  value="Social Companion"} *}
{*    {/if} *}
    {if $drawfield == "Y"}
    
	    {if $fieldset != $newfieldset} 
		{assign var=fieldset  value=$newfieldset}
		{if $firstrec == "N"}  
		   </table> 
		   </fieldset>
		{/if} 

		{assign var=firstrec  value="N"} 

		<fieldset><legend>{$fieldset}</legend> 

		{assign var=groupHelpPost  value=`$field.groupHelpPost`} 
		{if $field.groupHelpPre && $action neq 4 && $action neq 1028} 
		    <div class="messages help">{$field.groupHelpPre}</div> 
		{/if} 
		<table class="{if $action EQ 4}view-layout{else}form-layout-compressed{/if}"> 
	    {/if} 

	    {assign var=n value=$field.name} 
    
    
	    {if $field.options_per_line != 0} 
		<tr> 
		<td class="option-label">{$form.$n.label}</td> 
		<td class="view-value"> 
		     {assign var="count" value="1"} 
		    {strip} 
		    <table class="form-layout-compressed"> 
		    <tr> 
		      {* sort by fails for option per line. Added a variable to iterate through the element array*} 
		      {assign var="index" value="1"} 
		      {foreach name=outer key=key item=item from=$form.$n} 
		      {if $index < 10} 
			  {assign var="index" value=`$index+1`} 
		      {else} 
			  <td class="labels font-light">{$form.$n.$key.html}</td> 
			  {if $count == $field.options_per_line} 
			      </tr> 
			       <tr> 
			       {assign var="count" value="1"} 
			  {else} 
				{assign var="count" value=`$count+1`} 
			  {/if} 
		      {/if} 
		      {/foreach} 
		    </tr> 
		    </table> 
		    {/strip} 
		    {* Show explanatory text for field if not in 'view' or 'preview' modes *} 
		    {if $field.help_post && $action neq 4 && $action neq 1028}
			<span class="description">{$field.help_post}</span> 
		    {/if} 
		</td> 
		</tr> 
	    {else} 
		<tr>
		   <td class="label">{$form.$n.label}</td>
		   <td class="view-value">
		     {if $n|substr:0:3 eq 'im-'}
		       {assign var="provider" value=$n|cat:"-provider_id"}
		       {$form.$provider.html}&nbsp;
		     {/if}
		     {if $n eq 'email_greeting' or  $n eq 'postal_greeting' or $n eq 'addressee'}
			{include file="CRM/Profile/Form/GreetingType.tpl"}  
		     {elseif $n eq 'group'} 
			<table id="selector" class="selector" style="width:auto;">
				<tr><td>{$form.$n.html}{* quickform add closing </td> </tr>*}
			</table>
		     {else}
		       {* Make the custom fields larger then they are by default *}
		       {assign var="fieldName" value=$form.$n.name}
		       {assign var="fieldFirstSix" value=$fieldName|truncate:6:""}
		       {if ($fieldFirstSix == "custom") and ($form.$n.type == "text")}
		       	 {$form.$n.html|crmReplace:class:huge}
		       {else}
		       	 {$form.$n.html}
		       {/if}
		       
		       {if $n eq 'gender' && $form.$fieldName.frozen neq true}
			  &nbsp;(&nbsp;<a href="#" title="unselect" onclick="unselectRadio('{$n}', '{$form.formName}');return false;">{ts}unselect{/ts}</a>&nbsp;)
		       {/if}
		     {/if}
		     {*CRM-4564*}
		     {* Parvez Changed this as we dont want unselect for conference booking *}
		     {* Changed word Radio to xxxRadio *}
		     {if $field.html_type eq 'xxxRadio' && $form.$fieldName.frozen neq true}
			 <span style="line-height: .75em; margin-top: 1px;">
			  &nbsp;(&nbsp;<a href="#" title="unselect" onclick="unselectRadio('{$n}', '{$form.formName}');return false;">{ts}unselect{/ts}</a>&nbsp;)
			 </span>
		     {elseif $field.html_type eq 'Autocomplete-Select'}
			 {include file="CRM/Custom/Form/AutoComplete.tpl" element_name = $n }
		     {elseif ( $field.data_type eq 'Date'  or 
			      ( ( ( $n eq 'birth_date' ) or ( $n eq 'deceased_date' ) ) and 
				    !call_user_func( array('CRM_Utils_Date','checkBrithDateFormat') ) ) ) }
			    <span>
				{include file="CRM/common/calendar/desc.tpl" trigger="$form.$n.name"}
				{include file="CRM/common/calendar/body.tpl" dateVar=$form.$n.name startDate=1905 endDate=2010 doTime=1  trigger="$form.$n.name"}
			    </span>
		     {/if}  
		     {* Show explanatory text for field if not in 'view' or 'preview' modes *} 
		     {if $field.help_post && $action neq 4 && $action neq 1028}
			<br /><span class="description">{$field.help_post}</span> 
		     {/if} 
		   </td>
		</tr> 
	    {/if}     
	    
	    {/if}  {* drawfield is Y *} 
	    
    {/foreach} 
    </table>

    {if $field.groupHelpPost && $action neq 4  && $action neq 1028} 
	<div class="messages help">{$field.groupHelpPost}</div> 
    {/if}

    {if $mode eq 4} 
	<div class="crm-submit-buttons">  
	 {$form.buttons.html} 
	</div> 
    {/if}

    {if $mode ne 8 && $action neq 1028} 
	</fieldset> 
    {/if} 

    {if $help_post && $action neq 4}<br /><div class="messages help">{$help_post}</div>{/if} 
    {/strip} 
	
{/if} {* fields array is not empty *} 

{literal}
  <script type="text/javascript">
   
cj(document).ready(function(){ 
	cj('#selector tr:even').addClass('odd-row ');
	cj('#selector tr:odd ').addClass('even-row');
});
 
  </script>
{/literal}