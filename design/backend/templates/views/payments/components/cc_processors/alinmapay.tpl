{* $Id$ *}

<div class="form-field">
    <label for="terminal_id">{__("terminal_id")}:</label>
    <input type="text" name="payment_data[processor_params][terminal_id]" id="terminal_id" value="{$processor_params.terminal_id}" class="input-text" />
</div>

<div class="form-field">
    <label for="password">{__("password")}:</label>
    <input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text" />
</div>

<div class="form-field">
    <label for="merchant_key">{__("merchant_key")}:</label>
    <input type="text" name="payment_data[processor_params][merchant_key]" id="merchant_key" value="{$processor_params.merchant_key}" class="input-text" />
</div>

<div class="form-field">
    <label for="currency">{__("currency")}:</label>
    <select name="payment_data[processor_params][currency]" id="currency">
        <option value="SAR" {if $processor_params.currency == "SAR"}selected="selected"{/if}>{("SAR")}</option>
		 <option value="INR" {if $processor_params.currency == "INR"}selected="selected"{/if}>{("INR")}</option>
    </select>
</div>


<div class="form-field">
    <label for="request_url">{__("request_url")}:</label>
    <input type="text" name="payment_data[processor_params][request_url]" id="request_url" value="{$processor_params.request_url}" class="input-text" />
</div>

<div class="form-field">
    <label for="metadata">{__("metadata")}:</label>
    <input type="text" name="payment_data[processor_params][metadata]" id="request_url" value="{$processor_params.metadata}" class="input-text" />
</div>

<div class="form-field">
 <label for="transaction_type">{__("transaction_type")}:</label>
    <select name="payment_data[processor_params][transaction_type]" id="transaction_type">
        <option value="4" {if $processor_params.transaction_type == "4"}selected="selected"{/if}>{("Authorization")}</option>

		
		<option value="1" {if $processor_params.transaction_type == "1"}selected="selected"{/if}>{("Purchase")}</option>
    </select>

</div>