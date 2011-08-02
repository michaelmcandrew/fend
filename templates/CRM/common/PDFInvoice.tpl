<html>
<head>
<title>{$InvoiceTitle}</title>
    <link rel="stylesheet" href="sites/all/modules/civicrm/MTL/css/invoice.css" type="text/css" />
</head>
<body>
<table>
<tr>
<td align="left" width="50%">
<h1>{$InvoiceTitle}</h1>
</td>
<td align="right">
  <img alt="Federation of European Nurses in Diabetes" src="sites/all/modules/civicrm/MTL/i/logo.gif" class="logo" />
</td>
</tr>
<tr>
<td>
{if $addressed_to != null}
  {$addressed_to}<br />
{/if}
{* {if $attention_of != null} *}
{*   For attention of : {$attention_of}<br /> *}
{* {/if} *}
{if $address_line1 != null}
  {$address_line1}<br />
{/if}
{if $address_line2 != null}
  {$address_line2}<br />
{/if}
{if $address_line3 != null}
  {$address_line3}<br />
{/if}
{if $town != null}
  {$town}<br />
{/if}
{if $postcode != null}
  {$postcode},
{/if}
{if $city != null}
  {$city}<br />
{/if}
{if $country != null}
  {$country}<br />
{/if}
{if $company_tax_no != null}
  {$company_tax_no}
{/if}
</td>
<td align="right">
24 Homesdale Avenues<br />
London <br /> SW14 7BQ<br />
United Kingdom</b>
</td>
</tr>
<tr>
<td colspan="2"><br /><br />
</td>
</tr>
<tr>
<td colspan="2" align="left">
  <table cellspacing="0" cellpadding="0" border="0">
  <tr>
  <td>
  <b>{$InvoiceNoLabel}:</b> {$invoice_no}<br />
  <b>{$InvoiceDateLabel}:</b> {$invoice_date|date_format:"%d %B %y"}<br />
  <b>{$InvoiceDueDateLabel}:</b> {$invoice_due_date|date_format:"%d %B %y"}
  </td>
  <td align="right">
  Phone: +32 3449 4374 <br />
  Email: registration2010@fend.org <br />
  </td>
  </tr>
  </table>
  <br /><br /><br />
</td>
</tr>
{if $special_instructions != null}
  <tr>
  <td colspan="2">
  <b>{$YourReferenceLabel}:</b>{$special_instructions}<br /><br />
  </td>
  </tr>
{/if}
</table>
<table>
<tr>
<td class="borderedcell"><b>{*$item_descr_column_heading*} FEND Conference 2010</b>
</td>
<td  class="borderedcell" align="right">
<b>sub total ({$invoiceItems|@count} detail lines)</b>
</td>
</tr>
{foreach from=$invoiceItems item=invoiceItem}
<tr>
<td  class="borderedcell">
{$invoiceItem.description|strip_tags}
</td>
<td  class="borderedcell" align="right">
{$invoiceItem.fee_amount|crmMoney:$currency}
</td>
{/foreach}
<tr>
<td class="borderedcell" align="right">&nbsp;
</td>
<td class="borderedcell" align="right">
<b>{$TotalPayableLabel} {$fee_amount|crmMoney:$currency}</b>
</td>
</tr>
</table>
<br />
<br />
<h2><center>{$PaymentDetailsLabel}</center></h2>
<br />
<br />
<table>
<tr>
<td class="borderedcell">
<b>{$BankLabel}:</b> <br />
Barclays Bank plc<br />
{$BankAddressLabel}
PO Box 13<br />
George Street <br />Richmond Surrey TW9 1JU,<br> United Kingdom<br /><br />
{if $currencyCode eq "EUR" }
{$PaymentInstructions}<br /><br />
Account Name: <b> Federation of European Nurses in Diabetes</b><br />
IBAN:<b> GB85BARC20721740905135</b> <br />
SWIFT/BIC: <b>BARCGB22 </b><br />
{else}
Account name: <b>Federation of European Nurses in Diabetes</b><br />
IBAN:<b> GB85BARC20721740905135</b> <br />
SWIFT/BIC: <b>BARCGB22</b><br />
{/if}
</td>
<td class="borderedcell" align="center" valign="middle">

<b><span class="red">
{$InvoiceHelpTextPreInvoiceNo}
<br />{$invoice_no}
{$InvoiceHelpTextPostInvoiceNo}
</span></b>
</td>
</tr>

<tr>
<td class="borderedcell" align="center" valign="middle" colspan=2>
{if $email_invoice_address neq "" }
{$InvoiceEmailedLabel} {$email_invoice_address}
{/if}
{if $post_invoice eq "1" }
<br />{$PostCopyLabel}
{/if}
</td>
</tr>


</table>
</body>
</html>
