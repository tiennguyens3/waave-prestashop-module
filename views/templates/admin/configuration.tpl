<form method="post" id="payment-form" class="defaultForm form-horizontal">
  <input type="hidden" name="btnSubmit" value="1">
  <div class="panel" id="fieldset_0">
   <div class="panel-heading">
      <i class="icon-envelope"></i>Waave payment gateway
   </div>
   <div class="form-wrapper">
      <div class="form-group">
         <label class="control-label col-lg-3">
         Waave Sandbox
         </label>
         <div class="col-lg-9">
          <div class="col-lg-6 switch prestashop-switch fixed-width-lg">
            <input type="radio" name="WAAVE_SANDBOX" id="WAAVE_SANDBOX_on" value="1" {if ($waaveSandbox == 1)}checked="checked"{/if}>
            <label for="WAAVE_SANDBOX_on">Yes</label>
            <input type="radio" name="WAAVE_SANDBOX" id="WAAVE_SANDBOX_off" value="0" {if ($waaveSandbox == 0)}checked="checked"{/if}>
            <label for="WAAVE_SANDBOX_off">No</label>
            <a class="slide-button btn"></a>
          </div>
         </div>
      </div>
      <div class="form-group">
         <label class="control-label col-lg-3 required">
         Access Key
         </label>
         <div class="col-lg-9">
            <input type="text" name="ACCESS_KEY" id="ACCESS_KEY" value="{$accessKey}" class="" required="required">
         </div>
      </div>
      <div class="form-group">
         <label class="control-label col-lg-3 required">
         Private Key
         </label>
         <div class="col-lg-9">
            <input type="text" name="PRIVATE_KEY" id="PRIVATE_KEY" value="{$privateKey}" class="" required="required">
         </div>
      </div>
      <div class="form-group">
         <label class="control-label col-lg-3 required">
         Venue ID
         </label>
         <div class="col-lg-9">
            <input type="text" name="VENUE_ID" id="VENUE_ID" value="{$venueId}" class="" required="required">
         </div>
      </div>
   </div>
   <!-- /.form-wrapper -->
   <div class="panel-footer">
      <button type="submit" value="1" id="configuration_form_submit_btn" name="btnSubmit" class="btn btn-default pull-right">
      <i class="process-icon-save"></i> Save
      </button>
   </div>
</div>
</form>