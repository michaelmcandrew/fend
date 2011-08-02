{if $skipCount}
    <h3>{ts}Skipped Delegate(s):{/ts} {$skipCount}</h3>
{/if}
{if $action & 1024}
    {include file="CRM/Event/Form/Registration/PreviewHeader.tpl"}
{/if}

{include file="CRM/common/TrackingFields.tpl"}

{capture assign='reqMark'}<span class="marker"  title="{ts}This field is required.{/ts}">*</span>{/capture}

{*CRM-4320*}
{if $statusMessage}
    <div class="messages status">
        <p>{ts}{$statusMessage}{/ts}</p>
    </div>
{/if}

{assign var="button_name" value="_qf_"|cat:$form.formName}
{assign var="button_name_skip" value=$button_name|cat:"_next_skip"}

<div id="crm-submit-buttons">
    {foreach from=$form.buttons item=button key=button_id}
        {if ($button.type eq 'submit')}         
            {if ($button_id eq $button_name_skip)}
              {ts}I no longer wish to register this additional delegate: {/ts}{$button.html}
      {/if}
  {/if}
    {/foreach}
</div>

<div class="form-item">
{if $priceSet}
    <fieldset id="priceset"><legend>{$event.fee_label}</legend>
    <dl>
    {foreach from=$priceSet.fields item=element key=field_id}
        {if ($element.html_type eq 'CheckBox' || $element.html_type == 'Radio') && $element.options_per_line}
            {assign var="element_name" value=price_$field_id}
            <dt>{$form.$element_name.label}</dt>
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
    </dl>
    <div class="form-item">
        <dt></dt>
        <dd>{include file="CRM/Event/Form/CalculatePriceset.tpl"}</dd>
    </div> 
    </fieldset>
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

<style type="text/css">
{literal} 
#crm-container .description {color:purple;}
{/literal} 
</style>

{assign var=n value=email-$bltID}

{include file="CRM/UF/Form/ConferenceDelegateBlock.tpl" fields=$additionalCustomPre emailfield=$form.$n additional="Y"}

{* {include file="CRM/UF/Form/Block.tpl" fields=$additionalCustomPre} *}
{* {include file="CRM/UF/Form/Block.tpl" fields=$additionalCustomPost} *}

</div>

<div id="crm-submit-buttons">
    {foreach from=$form.buttons item=button key=button_id}
        {if ($button.type eq 'submit')}         
            {if ($button_id neq $button_name_skip)}
              {$button.html}
      {/if}
  {/if}
    {/foreach}
</div>
