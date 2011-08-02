{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}

{include file="CRM/common/TrackingFields.tpl"}

<div class="form-item">
    {* Don't use "normal" thank-you message for Waitlist and Approval Required registrations - since it will probably not make sense for those situations. dgg *}
    {if $event.thankyou_text AND (not $isOnWaitlist AND not $isRequireApproval)} 
        <div id="intro_text">
            <p>
            {$event.thankyou_text}
            </p>
        </div>
    {/if}
{if $tsLocale eq "es_ES"}    
        <div id="intro_text">
            <p>
Muchas gracias por su inscripción. Una confirmación de la inscripción ha sido enviada al correo electrónico facilitado en el formulario.
<br /><br />
Por favor reserve su alojamiento tan pronto como le sea posible. <a href="/es/accommodation-barcelona" target="_blank">Haga clic aquí</a> para visitar nuestra página de reservas de hotel.
<br /><br />
<b><u>Más información sobre la Conferencia</u></b>
<br /><br />
Toda la información sobre la conferencia se encuentra en la página web, que será actualizada regularmente entre ahora y junio. Dado que la información no se enviará, le rogamos que consulte toda la información que necesite de la página web antes de la conferencia. 
<br /><br />
Al llegar a la conferencia recibirá la versión final del programa plenario y de los talleres junto con la lista de participantes.
<br /><br />
No se repartirán copias de los documentos en la página web durante la conferencia. Los sumarios y las ponencias que los ponentes y los líderes de los talleres pondrán a vuestra disposición antes de la conferencia se publicarán en la página web. Las versiones finales de todas las presentaciones estarán disponibles en nuestra página web después de la conferencia.
<br /><br />
<b><u>Pago</u></b>
<br /><br />
A continuación emitiremos una factura por el importe de la cantidad a abonar. El pago se deberá realizar antes de que hayan pasado 28 días de la fecha emisión de la factura o, en todo caso, antes del viernes, 11 de junio 2010 (lo que sea más corto). Los datos de nuestro banco figuran en la factura.
<br /><br />
Cualquier cancelación o modificación de la reserva debe ser hecha por escrito a conference@esn-eu.org.
<br /><br />
La cancelación de reservas después de que la factura haya sido emitida conlleva un recargo de 5% por gastos administrativos de cancelación.
<br /><br />
No habrá reembolso de las cancelaciones hechas después del viernes, 11 de junio 2010. 
<br /><br />
¡Un saludo cordial de todos de la Red Social Europea (ESN)!
<br /><br />
¡Nos complace darle la bienvenida a Barcelona! 
        </p>
    </div>
{/if}
{if $tsLocale eq "de_DE"}    
        <div id="intro_text">
            <p>
Vielen Dank für Ihre Anmeldung. Eine Bestätigung Ihrer Buchung wird Ihnen per E-Mail zugesandt. Wenn Sie eine Mehrfachbuchung haben, ist die Bestätigung zum verantwortlichen Mitarbeiter gesendet worden.
<br /><br />
Bitte buchen Sie Ihre Hotelunterbringung so bald wie möglich - <a href="/de/accommodation-barcelona" target="_blank">Klicken Sie hier</a> um die Hotel Seite zu besuchen. 
<br /><br />
<b><u>Weitere Informationen</u></b>
<br /><br />
Alle Informationen über die Konferenz sind auf unserer regelmäßig aktualisierten Webseite verfügbar. Bitte entnehmen Sie von dort alle relevanten Informationen; Sie werden von uns keine weiteren Nachrichten per Post oder Mail erhalten. Bei Ihrer Anmeldung in Barcelona werden Ihnen ein endgültiges Konferenz- und Workshop-Progamm sowie eine Teilnehmerliste ausgehändigt werden.
<br /><br />
Auf der Konferenz erhalten Sie keine Kopien von Präsentationen und Vorträgen in Papierform. Diese können Sie ca. 2 Wochen vor Konferenzbeginn von dieser Webseite herunterladen. Endgültige Versionen von Präsentationen und Vorträgen werden nach der Konferenz auf der ESN Webseite zur Verfügung stehen. 
<br /><br />
<b><u>Zahlung</u></b>
<br /><br />
Wir werden Ihnen nun den ausstehenden Betrag in Rechnung stellen. Der Rechnungsbeitrag wird fällig 28 Tage nach Rechnungsdatum oder in jedem Fall vor Freitag dem 11. Juni 2010 (je nachdem, welcher Zeitraum kürzer ist). Zahlungsinformationen sind auf der Rechnung vermerkt.
<br /><br />
Stornierungen oder Buchungsänderungen sind in schriftlicher Form an conference@esn-eu.org. 
<br /><br />
Bei Stornierungen nach Rechnungstellung erheben wir eine Stornierungsgebühr (5%) für administrative Aufgaben.
<br /><br />
Bei Stornierungen, die uns nach Freitag, dem 11. Juni 2010 erreichen, stellen wir den gesamten Rechnungsbetrag in Rechnung.
<br /><br />
Herzliche Grüße vom gesamten Team des ESN. Wir freuen uns schon jetzt, 
<br /><br />
Sie in Barcelona willkommen heißen zu dürfen! 
        </p>
    </div>
{/if}
{if $tsLocale eq "it_IT"}    
        <div id="intro_text">
            <p>
Grazie per essersi iscritto/a alla conferenza. Saremo lieti di darLe il benvenuto a Barcellona. I dettagli della prenotazione da Lei fatta sono stati copiati all'indirizzo email fornito nel modulo.
<br /><br />
Consigliamo che prenotiate appena possibile il vostro alloggio in albergo per la conferenza - cliccate qui per andare alla pagina Alloggio di questo sito. 
<br /><br />
<b><u>Maggiori informazioni sulla conferenza</u></b>
<br /><br />
Tutte le informazioni sulla conferenza sono disponibili sul sito web, il quale sarà aggiornato regolarmente da ora fino a giugno. Vi invitiamo ad attingere direttamente da questo sito tutte le informazioni di cui potreste aver bisogno prima della conferenza, in quanto non vi verranno inviate individualmente. 
<br /><br />
Una volta giunti alla conferenza vi verrà consegnata la versione finale del programma plenario e dei workshop, assieme ad una lista completa dei partecipanti.
<br /><br />
Alla conferenza non vi saranno consegnate copie stampate delle presentazioni. Bozze di relazioni verranno pubblicate sul sito web per quelli di voi interessati in un argomento specifico della conferenza circa due settimane prima della conferenza. La versione finale di ogni relazione svolta verrà pubblicata sul nostro sito poco dopo la conferenza.
<br /><br />
<b><u>Pagamento</u></b>
<br /><br />
Tra breve riceverete la fattura per la vostra quota di partecipazione. Il pagamento dovrà essere effettuato entro 28 giorni della data della fattura o, in ogni modo, entro venerdì 11 giugno 2010 (se questo periodo è più breve). Dati bancari per il versamento si trovano sulla fattura.
<br /><br />
Le disdette o qualsiasi cambiamento alla prenotazione vanno comunicati per email a conference@esn-eu.org. 
<br /><br />
Per le disdette ricevute dopo l'emesso della nostra fattura teniamo il diritto di applicare una penale pari al 5% per costi di amministrazione.
<br /><br />
Non ci sarà rimborso alcuno per le disdette pervenute dopo il venerdì 11 giugno 2010.
<br /><br />
Saluti cordiali da tutti all'European Social Network. Vi aspettiamo a Barcellona! 
<br /><br />
E' stata inviata una email con i dettagli dell'evento a hjgh@jghju.com
        </p>
    </div>
{/if}
{if $tsLocale eq "fr_FR"}    
        <div id="intro_text">
            <p>
Merci de nous avoir fait parvenir votre fiche d’inscription. Une copie de cette inscription sera envoyée par email à l’adresse fournie dans le formulaire. 
<br /><br />
Nous vous en prions de réserver aussitôt que possible votre hébergement – cliquez ici pour accéder à la page d'hébergement. 
<br /><br />
<b><u>Informations additionnelles sur la conférence</u></b>
<br /><br />
Toute l’information sur la conférence est disponible sur notre site Internet qui sera régulièrement mis à jour d’ici au mois de juin. Veuillez à relever toute l’information dont vous aurez besoin sur le site car elle ne vous parviendra pas par courrier. 
<br /><br />
Dès votre arrivée à la conférence, le programme final des sessions plénières et des ateliers vous sera remis, ainsi que la liste des délégués.
<br /><br />
Les présentations et autres documents rendus disponibles par les intervenants avant la conférence seront publiés en ligne, donc rendus disponibles pour le téléchargement.
<br /><br />
Il n’y aura pas de distribution des présentations lors de la conférence, mais une version finale de celles-ci sera publiée sur le site peu après la conférence.
<br /><br />
<b><u>Règlement</u></b>
<br /><br />
Nous vous enverrons prochainement la facture correspondant au montant de votre inscription. Le paiement doit être effectué dans les 28 jours à partir de la date figurant sur la facture et dans tous les cas, avant le vendredi, 11 juin 2010. Vous trouverez les coordonnées bancaires sur la facture.
<br /><br />
Les modifications aux inscriptions, également les annulations, doivent être envoyées par écrit à conference@esn-eu.org. 
<br /><br />
Les annulations reçues après l'émission de la facture feront l'objet de frais administratifs à hauteur de 5%.
<br /><br />
Aucun remboursement ne sera possible pour les annulations qui sont reçues après le vendredi, 11 juin 2010.
<br /><br />
Nos meilleures salutations de l' ESN. Nous vous attendons à Barcelone! 
<br /><br />
        </p>
    </div>
         
    
{/if}
    {* Show link to Tell a Friend (CRM-2153) *}
    {if $friendText}
        <div id="tell-a-friend">
            <a href="{$friendURL}" title="{$friendText}" class="button"><span>&raquo; {$friendText}</span></a>
       </div><br /><br />
    {/if}  

    <div id="help">
        {if $isOnWaitlist}
            <p>
                <span class="bold">{ts}You have been added to the WAIT LIST for this event.{/ts}</span>
                {ts}If space becomes available you will receive an email with a link to a web page where you can complete your registration.{/ts}
            </p> 
        {elseif $isRequireApproval}
            <p>
                <span class="bold">{ts}Your registration has been submitted.{/ts}
                {ts}Once your registration has been reviewed, you will receive an email with a link to a web page where you can complete the registration process.{/ts}</span>
            </p>
        {elseif $is_pay_later and $paidEvent}
            <div class="bold">{$pay_later_receipt}</div>
            {if $is_email_confirm}
                <p>{ts 1=$email}An email with event details has been sent to %1.{/ts}</p>
            {/if}
        {* PayPal_Standard sets contribution_mode to 'notify'. We don't know if transaction is successful until we receive the IPN (payment notification) *}
        {elseif $contributeMode EQ 'notify' and $paidEvent}
            <p>{ts 1=$paymentProcessor.processorName}Your registration payment has been submitted to %1 for processing. Please print this page for your records.{/ts}</p>
            {if $is_email_confirm}
                <p>{ts 1=$email}A registration confirmation email will be sent to %1 once the transaction is processed successfully.{/ts}</p>
            {/if}
        {else}
            <p>{ts}Your registration has been processed successfully. Please print this page for your records.{/ts}</p>
            {if $is_email_confirm}
                <p>{ts 1=$email}A registration confirmation email has also been sent to %1{/ts}</p>
            {/if}
        {/if}
    </div>
    <div class="spacer"></div>

    <div class="header-dark">
        {ts}Event Information{/ts}
    </div>
    <div class="display-block">
        {include file="CRM/Event/Form/Registration/EventInfoBlock.tpl" context="ThankYou"}
    </div>

    {if $paidEvent}
    <div class="header-dark">
        {$event.fee_label}
    </div>
    <div class="display-block">
        {if $lineItem}
            {include file="CRM/Event/Form/Registration/LineItem.tpl"}<br />
        {elseif $amount || $amount == 0}
            {foreach from= $finalAmount item=amount key=level}  
                <strong>{$amount.amount|crmMoney} &nbsp;&nbsp; {$amount.label}</strong><br /> 
            {/foreach}
            {if $totalAmount}
                <br /><strong>{ts}Event Total{/ts}: {$totalAmount|crmMoney}</strong>
                {if $hookDiscount.message}
                    <em>({$hookDiscount.message})</em>
                {/if}
                <br />
            {/if} 
        {/if}
        {if $receive_date}
            <strong>{ts}Transaction Date{/ts}</strong>: {$receive_date|crmDate}<br />
        {/if}
        {if $contributeMode ne 'notify' AND $trxn_id}
            <strong>{ts}Transaction #{/ts}: {$trxn_id}</strong><br />
        {/if}
    </div>
    {elseif $participantInfo}
        <div class="header-dark">
            {ts}Additional Participant Email(s){/ts}
        </div>
        <div class="display-block">
            {foreach from=$participantInfo  item=mail key=no}  
                <strong>{$mail}</strong><br />  
            {/foreach}
        </div>
    {/if}

    <div class="header-dark">
        {ts}Registered email{/ts}
    </div>
    <div class="display-block">
        {$email}
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
        {foreach from=$customPre item=field key=customName}
            {if $field.groupTitle}
                {assign var=groupTitlePre  value=$field.groupTitle} 
            {/if}
        {/foreach}
        <div class="header-dark">
      {$groupTitlePre}
        </div>  
        {include file="CRM/UF/Form/Block.tpl" fields=$customPre}
    {/if}

    {if $contributeMode ne 'notify' and $paidEvent and ! $is_pay_later and ! $isAmountzero and !$isOnWaitlist and !$isRequireApproval}   
    <div class="header-dark">
        {ts}Billing Name and Address{/ts}
    </div>
    <div class="display-block">
        <strong>{$billingName}</strong><br />
        {$address|nl2br}
    </div>
    {/if}

    {if $contributeMode eq 'direct' and $paidEvent and ! $is_pay_later and !$isAmountzero and !$isOnWaitlist and !$isRequireApproval}
    <div class="header-dark">
        {ts}Credit Card Information{/ts}
    </div>
    <div class="display-block">
        {$credit_card_type}<br />
        {$credit_card_number}<br />
        {ts}Expires{/ts}: {$credit_card_exp_date|truncate:7:''|crmDate}
    </div>
    {/if}

    {if $customPost}
        {foreach from=$customPost item=field key=customName}
            {if $field.groupTitle}
                {assign var=groupTitlePost  value=$field.groupTitle} 
            {/if}
        {/foreach}
        <div class="header-dark">
            {$groupTitlePost}
        </div>  
        {include file="CRM/UF/Form/Block.tpl" fields=$customPost}
    {/if}

    {*display Additional Participant Info*}
    {if $customProfile}
        {foreach from=$customProfile item=value key=customName}
            <div class="header-dark">
                {ts 1=$customName+1}Participant Information - Participant %1{/ts} 
            </div>
            {foreach from=$value item=val key=field}
                {if $field eq additionalCustomPre or $field eq additionalCustomPost }
                    {if $field eq 'additionalCustomPre' }
                        <fieldset><legend>{$value.additionalCustomPre_grouptitle}</legend>
                    {else}
                        <fieldset><legend>{$value.additionalCustomPost_grouptitle}</legend>
                    {/if}
                    <table class="form-layout-compressed">  
                    {foreach from=$val item=v key=f}
                        <tr>
                            <td class="label twenty">{$f}</td><td class="view-value">{$v}</td>
                        </tr>
                    {/foreach}
                    </table>
                    </fieldset>
                {/if}
            {/foreach}
            <div class="spacer"></div>  
        {/foreach}
    {/if}

    {if $event.thankyou_footer_text}
        <div id="footer_text">
            <p>{$event.thankyou_footer_text}</p>
        </div>
    {/if}
    
    {if $event.is_public }
        {include file="CRM/Event/Form/Registration/iCalLinksConference.tpl"}
        {if $tsLocale eq "en_GB"}    
          <a href="/home-barcelona">{ts}Click here to return to conference home page{/ts}</a>
        {/if}     
        {if $tsLocale eq "es_ES"}    
          <a href="/es/home-barcelona">{ts}Click here to return to conference home page{/ts}</a>
        {/if}     
        {if $tsLocale eq "fr_FR"}    
          <a href="/fr/home-barcelona">{ts}Click here to return to conference home page{/ts}</a>
        {/if}     
        {if $tsLocale eq "it_IT"}    
          <a href="/it/home-barcelona">{ts}Click here to return to conference home page{/ts}</a>
        {/if}     
        {if $tsLocale eq "de_DE"}    
          <a href="/de/home-barcelona">{ts}Click here to return to conference home page{/ts}</a>
        {/if}     
    {/if} 
</div>
