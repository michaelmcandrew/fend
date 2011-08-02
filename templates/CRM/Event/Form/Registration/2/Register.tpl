{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}

{include file="CRM/common/TrackingFields.tpl"}

{capture assign='reqMark'}<span class="marker"  title="{ts}This field is required.{/ts}">*</span>{/capture}
<div class="form-item">

{* moved to tpl since need to show only for primary participant page *}
{if $requireApprovalMsg || $waitlistMsg}
  <div id = "id-waitlist-approval-msg" class="messages status">
    <dl>
	{if $requireApprovalMsg}<dd id="id-req-approval-msg">{$requireApprovalMsg}</dd>{/if}
        {if $waitlistMsg}<dd id="id-waitlist-msg">{$waitlistMsg}</dd>{/if} 
    </dl>
  </div>
{/if}

{if $event.intro_text}
    <div id="intro_text">
        <p>{$event.intro_text}</p>
    </div>
{/if}

{if $priceSet}
    <fieldset id="priceset"><legend>{$event.fee_label}</legend>
    <dl>
    {if $priceSet.help_pre}
	<dt>&nbsp;</dt>
	<dd class="description">{$priceSet.help_pre}</dd>
    {/if}
    {foreach from=$priceSet.fields item=element key=field_id}
        {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
            {assign var="element_name" value=price_$field_id}
            <dt style="margin-top: .5em;">{$form.$element_name.label}</dt>
            <dd>
            {assign var="count" value="1"}
            <table class="form-layout-compressed">
                <tr>
                    {foreach name=outer key=key item=item from=$form.$element_name}
                        {if is_numeric($key) }
                            <td class="labels font-light">{$form.$element_name.$key.html}</td>
                            {if $count == $element.options_per_line}
				{assign var="count" value="1"}
                            </tr>
                            <tr>
                            {else}
                                {assign var="count" value=`$count+1`}
                            {/if}
                        {/if}
	            {/foreach}
                </tr>
            </table>
            </dd>
        {else}
            {assign var="name" value=`$element.name`}
            {assign var="element_name" value="price_"|cat:$field_id}
            <dt>{$form.$element_name.label}</dt>
            <dd>&nbsp;{$form.$element_name.html}</dd>
        {/if}
        {if $element.help_post}
            <dt>&nbsp;</dt>
            <dd class="description">{$element.help_post}</dd>
        {/if}
    {/foreach}
    <div class="form-item">
	<dt></dt>
	<dd>{include file="CRM/Event/Form/CalculatePriceset.tpl"}</dd>
    </div> 
    {if $priceSet.help_post}
	<dt>&nbsp;</dt>
	<dd class="description">{$priceSet.help_post}</dd>
    {/if}
    </dl>
    </fieldset>
    {if $form.is_pay_later}
    <dl id="is-pay-later">
	<dt>&nbsp;</dt>
        <dd>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</dd>
    </dl>
    {/if}

{else}
    {if $paidEvent}
	<table class="form-layout-compressed">
	    <tr>
		<td class="label nowrap">{$event.fee_label} <span class="marker">*</span></td>
		<td>&nbsp;</td>
		<td>{$form.amount.html}</td>
	    </tr>
 	</table>
    {/if}
{/if}

{assign var=n value=email-$bltID}
<table class="form-layout-compressed">
    <tr>
	<td class="label nowrap">{$form.$n.label}</td><td>{$form.$n.html}</td>
    </tr>
</table>
{if $form.additional_participants.html}
    <div id="noOfparticipants_show">
	<a href="#" class="button" onclick="hide('noOfparticipants_show'); show('noOfparticipants'); document.getElementById('additional_participants').focus(); return false;"><span>&raquo; {ts}Register additional people for this event{/ts}</span></a>
    </div>
    <div class="spacer"></div>
{/if}
<div id="noOfparticipants" style="display:none">
    <div class="form-item">
    <table class="form-layout">
        <tr>
            <td><a href="#" onclick="hide('noOfparticipants'); show('noOfparticipants_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a></a>
                <label>{$form.additional_participants.label}</label></td>
            <td class="description">
                {$form.additional_participants.html|crmReplace:class:two}<br />
                {ts}You will be able to enter registration information for each additional person after you complete this page and click Continue.{/ts}
            </td>
       	</tr>
    </table>
    </div>
</div> 

{* User account registration option. Displays if enabled for one of the profiles on this page. *}
{include file="CRM/common/CMSUser.tpl"}

{include file="CRM/UF/Form/Block.tpl" fields=$customPre} 

{if $paidEvent}   
    {include file='CRM/Core/BillingBlock.tpl'} 
{/if}        
 

<div id="invoice_information">
{include file="CRM/UF/Form/Block.tpl" fields=$customPost}   
</div>


{if $isCaptcha}
    {include file='CRM/common/ReCAPTCHA.tpl'}
{/if}

<div id="paypalExpress">
{* Put PayPal Express button after customPost block since it's the submit button in this case. *}
{if $paymentProcessor.payment_processor_type EQ 'PayPal_Express' and $buildExpressPayBlock}
    {assign var=expressButtonName value='_qf_Register_upload_express'}
    <fieldset><legend>{ts}Checkout with PayPal{/ts}</legend>
    <table class="form-layout-compressed">
	<tr>
	    <td class="description">{ts}Click the PayPal button to continue.{/ts}</td>
	</tr>
	<tr>
	    <td>{$form.$expressButtonName.html} <span style="font-size:11px; font-family: Arial, Verdana;">Checkout securely.  Pay without sharing your financial information. </span></td>
	</tr>
    </table>
    </fieldset>
{/if}
</div>

<fieldset class="crm-group honor_block-group">
<legend>Payment Information</legend>
<p>To pay by credit card, please click 'Continue' now. This will take you to the next page where you will be able to fill in your card details.</p>
{if $form.is_pay_later}
<p>To pay by invoice, please tick the invoice box below.</p>
<div class="section {$form.is_pay_later.name}-section">
<div class="content">{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</div>
</div>
{/if} 
</fieldset>
{if $form.is_pay_later and $hidePaymentInformation} 
{* Hide Invoice information if contribution is not pay later. *}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="is_pay_later"
    trigger_value       =""
    target_element_id   ="invoice_information" 
    target_element_type ="block"
    field_type          ="radio"
    invert              = 0
}
{/if}


{literal}
<script type="text/javascript">
cj(document).ready(function(){

        cj('#is_pay_later').click(function(){

                    showHideInvoiceCheckbox();

        });

showHideInvoiceCheckbox();
    });
    

</script>
{/literal}



<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>

{if $event.footer_text}
    <div id="footer_text">
        <p>{$event.footer_text}</p>
    </div>
{/if}
</div>

{literal} 
<script type="text/javascript">

    function allowParticipant( ) { 
	var additionalParticipant = document.getElementById('additional_participants').value; 
	var validNumber = "";
	for( i = 0; i< additionalParticipant.length; i++ ) {
	    if ( additionalParticipant.charAt(i) >=1 || additionalParticipant.charAt(i) <=9 ) {
		validNumber += additionalParticipant.charAt(i);
	    } else {
		document.getElementById('additional_participants').value = validNumber;
	    }
	}

        {/literal}{if $allowGroupOnWaitlist}{literal}
           allowGroupOnWaitlist( validNumber );
        {/literal}{/if}{literal}
    }

    {/literal}{if ($form.is_pay_later or $bypassPayment) and $paymentProcessor.payment_processor_type EQ 'PayPal_Express'}
    {literal} 
       showHidePayPalExpressOption( );
    {/literal}{/if}{literal}

    function showHidePayPalExpressOption( )
    {
	var payLaterElement = {/literal}{if $form.is_pay_later}true{else}false{/if}{literal};
	if ( ( cj("#bypass_payment").val( ) == 1 ) ||
	     ( payLaterElement && document.getElementsByName('is_pay_later')[0].checked ) ) {
		show("crm-submit-buttons");
		hide("paypalExpress");
	} else {
		show("paypalExpress");
		hide("crm-submit-buttons");
	}
    }

    {/literal}{if ($form.is_pay_later or $bypassPayment) and $showHidePaymentInformation}{literal} 
       showHidePaymentInfo( );
    {/literal} {/if}{literal}

    function showHidePaymentInfo( )
    {	
	var payLater = {/literal}{if $form.is_pay_later}true{else}false{/if}{literal};

	if ( ( cj("#bypass_payment").val( ) == 1 ) ||
	     ( payLater && document.getElementsByName('is_pay_later')[0].checked ) ) {
	     hide( 'payment_information' );		
	} else {
             show( 'payment_information' );
	}
    }
    
    {/literal}{if $form.additional_participants}{literal}
       showAdditionalParticipant( );
    {/literal}{/if}{literal}


	function showHideInvoiceCheckbox(){
	   showHideByValue('is_pay_later','','invoice_information',
                                                     'table-row','radio',false);
	}
	
	var on_invoice_submit = function(){
  	if(document.getElementById('is_pay_later').checked){
       return InvoiceCheck_value();
    }
  }
  
  
  function InvoiceCheck_value(){
    var err = "";
    /*************** comment for live site ******************/
  var valCustom = "";

	if((document.getElementById('custom_94').value=="" )&&	(document.getElementById('custom_95').value=="") ){

	valCustom = "";
		err=err+"Purchas Order / Attention Of is a required field\n";
        //return false;    
    
	}
	
	
    if(document.getElementById('custom_96').value==""){
        err=err+"Address Line 1 is a required field\n";
        //return false;    
    }
    
   if(document.getElementById('custom_101').value==""){
        err=err+"Postcode is a required field \n";
        //return false;    
    }
    
    if(err!=""){
        alert(err);
        return false;    
    }else{
        return true;    
    }
  }
  
  if($("#Register"))
	$("#Register").submit(on_invoice_submit);
			
	
	
    function showAdditionalParticipant( )
    {	
	if ( document.getElementById('additional_participants').value ) { 
             show( 'noOfparticipants' );
	     hide( 'noOfparticipants_show' );
	} else {
             hide( 'noOfparticipants' );
	     show( 'noOfparticipants_show' );
	}
    }

    {/literal}{if $allowGroupOnWaitlist}{literal}
       allowGroupOnWaitlist( 0 );
    {/literal}{/if}{literal}
    
    function allowGroupOnWaitlist( additionalParticipants )
    {	
      if ( !additionalParticipants ) {
	 additionalParticipants = document.getElementById('additional_participants').value;
      }

      var availableRegistrations = {/literal}'{$availableRegistrations}'{literal};
      var totalParticipants = parseInt( additionalParticipants ) + 1;
      var isrequireApproval = {/literal}'{$requireApprovalMsg}'{literal};
 
      if ( totalParticipants > availableRegistrations ) {
         cj( "#id-waitlist-msg" ).show( );
         cj( "#id-waitlist-approval-msg" ).show( );

         //set the value for hidden bypass payment. 
         cj( "#bypass_payment").val( 1 );

         //hide pay later.
         {/literal}{if $form.is_pay_later}{literal} 
	    cj("#is-pay-later").hide( );
         {/literal} {/if}{literal}
 
      }	else {
         if ( isrequireApproval ) {
            cj( "#id-waitlist-approval-msg" ).show( );
            cj( "#id-waitlist-msg" ).hide( );
         } else {
            cj( "#id-waitlist-approval-msg" ).hide( );
         }
         //reset value since user don't want or not eligible for waitlist 
         cj( "#bypass_payment").val( 0 );

         //need to show paylater if exists.
         {/literal}{if $form.is_pay_later}{literal} 
	    cj("#is-pay-later").show( );
         {/literal} {/if}{literal}
      }

      //now call showhide payment info.
      {/literal}
      {if ($form.is_pay_later or $bypassPayment) and $paymentProcessor.payment_processor_type EQ 'PayPal_Express'}{literal} 
         showHidePayPalExpressOption( );
      {/literal}{/if}
      {literal}
  
      {/literal}{if ($form.is_pay_later or $bypassPayment) and $showHidePaymentInformation}{literal} 
         showHidePaymentInfo( );
      {/literal}{/if}{literal}
    }
</script>
{/literal} 
