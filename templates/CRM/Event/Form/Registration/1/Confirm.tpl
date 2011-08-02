{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}

{include file="CRM/common/TrackingFields.tpl"}

<div class="form-item">
    {if $isOnWaitlist}
        <div class="help">
            {ts}Please verify the information below. <span class="bold">Then click 'Continue' to be added to the WAIT LIST for this event</span>. If space becomes available you will receive an email with a link to a web page where you can complete your registration.{/ts}
        </div>
    {elseif $isRequireApproval}
        <div class="help">
            {ts}Please verify the information below. Then click 'Continue' to submit your registration. <span class="bold">Once approved, you will receive an email with a link to a web page where you can complete the registration process.</span>{/ts}
        </div>
    {else}
        <div id="help">
        {ts}Please verify the information below. Click <strong>Go Back</strong> if you need to make changes.{/ts}
        {if $contributeMode EQ 'notify' and !$is_pay_later and ! $isAmountzero }
            {if $paymentProcessor.payment_processor_type EQ 'Google_Checkout'}
                {ts 1=$paymentProcessor.processorName}Click the <strong>%1</strong> button to checkout to Google, where you will select your payment method and complete the registration.{/ts}
            {else} 	
                {ts 1=$paymentProcessor.processorName}Click the <strong>Continue</strong> button to checkout to %1, where you will select your payment method and complete the registration.{/ts}
            {/if }
        {else}
            {ts}Otherwise, click the <strong>Continue</strong> button below to complete your registration.{/ts}
        {/if}
        </div>
        {if $is_pay_later}
            <div class="bold">{$pay_later_receipt}</div>
        {/if}
    {/if}

    {if $event.confirm_text}
        <div id="intro_text">
	    <p>{$event.confirm_text}</p>
        </div>
    {/if}
    
    <div class="header-dark">
        {ts}Event Information{/ts}
    </div>
    <div class="display-block">
        {include file="CRM/Event/Form/Registration/EventInfoBlock.tpl"}
    </div>
    {if $paidEvent} 
    <div class="header-dark">
        {$event.fee_label}
    </div>
    <div class="display-block">
        {if $lineItem}
            {include file="CRM/Event/Form/Registration/LineItem.tpl"}
        {elseif $amount || $amount == 0}
            {foreach from= $amount item=amount key=level}  
		<strong>{$amount.amount|crmMoney} &nbsp;&nbsp; {$amount.label}</strong><br />	
            {/foreach}
            {if $totalAmount}
		<br /><strong>{ts}Total Amount{/ts}:&nbsp;&nbsp;{$totalAmount|crmMoney}</strong>
            {/if}	 		
            {if $hookDiscount.message}
                <em>({$hookDiscount.message})</em>
            {/if}
        {/if}
    </div>
    {/if}
	
    <div class="header-dark">
    	{ts}Booking Contact Email{/ts}
    </div>
    <div class="display-block">
        {* {$email} *}
        {$form.custom_44.html}
    </div>
    {if $event.participant_role neq 'Attendee' and $defaultRole}
        <div class="header-dark">
            {ts}Participant Role{/ts}
        </div>
        <div class="display-block">
            {$event.participant_role}
        </div>
    {/if}

    {if $customPre}
        {foreach from=$customPre item=field key=cname}
	    {if $field.groupTitle}
		{assign var=groupTitlePre  value=$field.groupTitle} 
            {/if}
	{/foreach}
        <div class="header-dark">
	    {$groupTitlePre}
        </div>  
        {* {include file="CRM/UF/Form/Block.tpl" fields=$customPre} *}
	<fieldset><legend>{$customPre.additionalCustomPreGroupTitle}</legend>
	    <table class="form-layout-compressed">
		{foreach from=$customPre item=value key=field}
		<tr>
		    {if $field == "custom_44"}
		      <td class="label twenty">{ts}Delegate email address{/ts}</td><td class="view-value">{$email}</td>
		    {else}
		      <td class="label twenty">{$form.$field.label}</td><td class="view-value">{$form.$field.html}</td>
		    {/if}
		</tr>
		{/foreach}
	    </table>
	</fieldset>
    {/if}
    {if $customPost}
        {foreach from=$customPost item=field key=cname}
            {if $field.groupTitle}
		{assign var=groupTitlePost  value=$field.groupTitle} 
            {/if}
        {/foreach}
        <div class="header-dark">
	    {$groupTitlePost}
        </div>  
        {include file="CRM/UF/Form/Block.tpl" fields=$customPost}
    {/if}

    {* display Additional Participant Profile Information *}
    {if $addParticipantProfile}
        {foreach from=$addParticipantProfile item=participant key=participantNo}
            <div class="header-dark">
                {ts 1=$participantNo+1}Participant Information - Participant %1{/ts}	
            </div>
            {if $participant.additionalCustomPre}
                <fieldset><legend>{$participant.additionalCustomPreGroupTitle}</legend>
                    <table class="form-layout-compressed">
                        {foreach from=$participant.additionalCustomPre item=value key=field}
                        <tr>
			    <td class="label twenty">{$field}</td><td class="view-value">{$value}</td>
                        </tr>
                        {/foreach}
                    </table>
                </fieldset>
            {/if}

            {if $participant.additionalCustomPost}
                <fieldset><legend>{$participant.additionalCustomPostGroupTitle}</legend>
                    <table class="form-layout-compressed">
                        {foreach from=$participant.additionalCustomPost item=value key=field}
                        <tr>
			    <td class="label twenty">{$field}</td><td class="view-value">{$value}</td>
                        </tr>
                        {/foreach}
                    </table>
                </fieldset>
            {/if}
        <div class="spacer"></div>
        {/foreach}
    {/if}

    {if $contributeMode ne 'notify' and !$is_pay_later and $paidEvent and !$isAmountzero and !$isOnWaitlist and !$isRequireApproval}
    <div class="header-dark">
        {ts}Billing Name and Address{/ts}
    </div>
    <div class="display-block">
        <strong>{$billingName}</strong><br />
        {$address|nl2br}
    </div>
    {/if}
    
    {if $contributeMode eq 'direct' and ! $is_pay_later and !$isAmountzero and !$isOnWaitlist and !$isRequireApproval}
    <div class="header-dark">
        {ts}Credit Card Information{/ts}
    </div>
    <div class="display-block">
        {$credit_card_type}<br />
        {$credit_card_number}<br />
        {ts}Expires{/ts}: {$credit_card_exp_date|truncate:7:''|crmDate}<br />
    </div>
    {/if}
    
    {if $contributeMode NEQ 'notify'} {* In 'notify mode, contributor is taken to processor payment forms next *}
    <div class="messages status">
        <p>
        {ts}Your registration will not be submitted until you click the <strong>Continue</strong> button. <br />Please click the button once only.{/ts}
        </p>
    </div>
    {/if}    
   
    {if $paymentProcessor.payment_processor_type EQ 'Google_Checkout' and $paidEvent and !$is_pay_later and ! $isAmountzero and !$isOnWaitlist and !$isRequireApproval}
        <fieldset><legend>{ts}Checkout with Google{/ts}</legend>
        <table class="form-layout-compressed">
	    <tr>
		<td class="description">{ts}Click the Google Checkout button to continue.{/ts}</td>
	    </tr>
	    <tr>
		<td>{$form._qf_Confirm_next_checkout.html} <span style="font-size:11px; font-family: Arial, Verdana;">Checkout securely.  Pay without sharing your financial information. </span></td>
	    </tr>
        </table>
        </fieldset>    
    {/if}

<label for="same_as_billing">{ts}I have read and accept the <a href="/conditions-barcelona" target="_blank">terms and conditions</a> of conference registration: {/ts}</label>

<span class="marker">*</span><input type="checkbox" id="accept_t_and_c" value="" onclick="showAdditionalParticipant(); return true;">

<div class="spacer"></div>

    <div id="crm-submit-buttons">
	{$form.buttons.html}
    </div>

    {if $event.confirm_footer_text}
        <div id="footer_text">
            <p>{$event.confirm_footer_text}</p>
        </div>
    {/if}
</div>

<SCRIPT LANGUAGE="JavaScript">
{literal}


    function showAdditionalParticipant( )
    {	
	if ( document.getElementById("accept_t_and_c").checked == true ) {
	     document.getElementsByName( "_qf_Confirm_next" ).item(0).disabled = false;
             //document.getElementByName("_qf_Confirm_next").disabled = false
	} else {
	     document.getElementsByName( "_qf_Confirm_next" ).item(0).disabled = true;
             //document.getElementByName("_qf_Confirm_next").disabled = true;             
	}
    }
    showAdditionalParticipant();
{/literal}
</SCRIPT>

{include file="CRM/common/showHide.tpl"}
