{extends "$layout"}

{block name="content"}
  <section>
    <p>{l s='You have successfully submitted your payment form.'}</p>
    <p>{l s="Now, you just need to proceed the payment and do what you need to do."}</p>
    <form action="{$actionUrl}" method="get" id="waave_payment_form">
        <input type="hidden" name="access_key" value="{$accessKey}">
        <input type="hidden" name="return_url" value="{$returnUrl}">
        <input type="hidden" name="cancel_url" value="{$cancelUrl}">
        <input type="hidden" name="callback_url" value="{$callbackUrl}">
        <input type="hidden" name="amount" value="{$amount}">
        <input type="hidden" name="reference_id" value="{$referenceId}">
        <input type="hidden" name="currency" value="USD">
        <input type="hidden" name="venue_id" value="{$venueId}">
    </form>
  </section>
{/block}