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
{else}
{if $tsLocale eq "es_ES"}
    <div id="intro_text">
    <p>
Por favor complete los detalles del delegado que usted desea inscribir.<br/><br/>
Si desea inscribir a más de un delegado y está de acuerdo en recibir <i>una única factura</i> para todos, puede inscribirlos a todos en este formulario. Por favor complete los detalles del primer delegado y luego haga clic para inscribir delegados adicionales. Para ahorrarle tiempo, los detalles del primer delegado aparecerán automáticamente para cada delegado subsiguiente, pero estos datos pueden ser cambiados. Los detalles de la factura sólo pueden ser introducidos una vez. <br/>
<br/><strong>
Si necesita recibir facturas individuales para cada delegado (por ejemplo si hay datos de facturación o códigos de presupuesto distintos), por favor inscriba cada delegado por separado.
</strong>
<br/>
    </p>
    </div>
{/if}
{if $tsLocale eq "fr_FR"}
    <div id="intro_text">
    <p>
    Veuillez fournir les données du délégué que vous souhaitez inscrire.<br></br>
    Si vous désirez inscrire plusieurs délégués et que <i> vous préférez ne recevoir qu’une facture </i> pour ce groupe de délégués, veuillez saisir les données du premier délégué, puis cliquez pour passer à l’inscription des délégués suivants. Pour gagner du temps, les données que vous avez fournies pour le premier délégué apparaitront automatiquement pour chaque délégué subséquent, mais peuvent être altérées si nécessaires.   Les données de la facture ne peuvent être soumises qu’une seule fois.<br/>
    <br/><strong>
    Si vous avez besoin d’une facture pour chaque délégué, veuillez compléter et remplir une fiche séparée pour chaque délégué.
    </strong>
    <br/>    
    </p>
    </div>
{/if}
{if $tsLocale eq "it_IT"}
    <div id="intro_text">
    <p>
    La preghiamo di fornire i dati della persona che vuole iscrivere da delegato.<br></br>
    Se vuole iscrivere diversi delegati e si accontenterà di <i>una fattura unica</i> per tutti quanti, La preghiamo di fornire i dati del primo delegato, poi cliccare per iscriverne i delegati successivi. Per risparmiare tempo, i dati che fornirà riguardo l’organizzazione del primo delegato appariranno automaticamente per ogni delegato successivo, tuttavia potranno essere cambiati se necessario. I dati per la fattura potranno essere inseriti solo una volta.<br/>
    <br/><strong>
    Se avrete bisogno delle fatture separate per i delegati (ad esempio, con diverse intestazioni o un codice di riferimento unico) la preghiamo di iscrivere ogni delegato separatamente.
    </strong>
    <br/>
    </p>
    </div>
{/if}
{if $tsLocale eq "de_DE"}
    <div id="intro_text">
    <p>
    Bitte geben Sie die Angaben zu dem/der Teilnehmer(in) an, den/die Sie anmelden möchten  <br><br/>
    Wenn Sie zwei oder mehr Personen anmelden und Sie gerne <i>eine einzelne Rechnung</i> für jeden Teilnehmer erhalten möchten, können Sie sie alle in dieses Formular eintragen. Tragen Sie bitte die Angaben zum/zur ersten Teilnehmer(in) ein, und klicken dann um eine(n) weitere(n) Teilnehmer(in) anzumelden. Um Zeit zu sparen: der Name der Organisation des/r ersten Teilnehmer(in) wird bei der Anmeldung jedes weiteren Teilnehmers automatisch erscheinen, kann jedoch verändert werden, wenn es sich um einen anderen Namen handelt. Rechnungsdaten können nur einmal eingegeben werden.<br/> 
    <br/><strong>
    Falls Sie getrennte Rechnungen benötigen (wenn beispielsweise die Rechnungen an verschiedene Adressen geschickt werden sollen oder unter getrennten Budgets laufen) müssen Sie das individuelle Anmeldeformular benutzen.
    </strong>
    <br/>
    </p>
    </div>
{/if}
{/if}

{* MTL Added to allow person to choose EUR/GBP for payment amount *}
{* {if $paidEvent} *}
{*    {if $config->defaultCurrency eq 'EUR'} *}
{*    {ts}If you want to pay in pounds, click {/ts}<a href="{crmURL p='civicrm/event/register' q="reset=1&id=`$event.id`&currency=GBP"}"title="{ts}Pay for Camp in pounds.{/ts}"><strong>{ts}here{/ts}</strong></a> *}
{*    {else} *}
{*    {ts}If you want to pay in euros, click {/ts}<a href="{crmURL p='civicrm/event/register' q="reset=1&id=`$event.id`&currency=EUR"}"title="{ts}Pay for Camp in euros.{/ts}"><strong>{ts}here{/ts}</strong></a> *}
{*    {/if} *}
{* {/if} *}

{* {$form.custom_44.html} *}

<fieldset><legend>{ts}Booking contact{/ts}</legend> 
<table class="{if $action EQ 4}view-layout{else}form-layout-compressed{/if}"> 
    <tr> 
    <td class="option-label">
      {$form.custom_44.label}
        </td> 
        <td class="view-value">
  {if isset($form.custom_44.error)}
      {$form.custom_44.html|crmReplace:class:'form-text huge required error'}
  {else}
      {$form.custom_44.html|crmReplace:class:'form-text huge'}
  {/if} 
  <br /><span class="description">{ts}{$customPre.custom_44.help_post}{/ts}</span>
    </td> 
    </tr>       
</table>
</fieldset>

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
    <fieldset id="priceset"><legend>{$event.fee_label}</legend>
    {if $paidEvent}
    
  <table class="form-layout-compressed">
      <tr>
    <td class="label nowrap">{$event.fee_label} <span class="marker">*</span></td>
    <td>&nbsp;</td>
    <td>{$form.amount.html}</td>
      </tr>
      {if $form.is_pay_later}
      <tr id="is-pay-later">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>{$form.is_pay_later.html}&nbsp;{$form.is_pay_later.label}</td>
      </tr>
      {/if}
  </table>
    {/if}
    </fieldset>
{/if}

{assign var=n value=email-$bltID}

{* User account registration option. Displays if enabled for one of the profiles on this page. *}
{include file="CRM/common/CMSUser.tpl"}

{include file='CRM/Core/BillingBlock.tpl'} 
<style type="text/css">
{literal} 
#crm-container .description {color:purple;}
{/literal} 
</style>
{include file="CRM/UF/Form/ConferenceDelegateBlock.tpl" fields=$customPre emailfield=$form.$n}

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

{if $form.additional_participants.html}
    <div id="noOfparticipants_show">
  <a href="#" class="button" onclick="hide('noOfparticipants_show'); show('noOfparticipants'); document.getElementById('additional_participants').focus(); return false;"><span>&raquo; {ts}Register additional delegates for the conference{/ts}</span></a>
    </div>
    <div class="spacer"></div>
{/if}
<div id="noOfparticipants" style="display:none">
    <div class="form-item">
    <table class="form-layout">
        <tr>
            <td><a href="#" onclick="hide('noOfparticipants'); show('noOfparticipants_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a></a>
                <label>{ts}{$form.additional_participants.label}{/ts}</label></td>
            <td class="description">
                {$form.additional_participants.html|crmReplace:class:two}<br />
                {ts}You will be able to enter registration information for each additional delegate after you complete this page and click 'Continue'.{/ts}
            </td>
        </tr>
    </table>
    </div>
</div> 

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
 
      } else {
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
