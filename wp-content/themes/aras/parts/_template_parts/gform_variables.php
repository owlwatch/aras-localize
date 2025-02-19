<script type="text/javascript">
  jQuery(document).ready(function($) {
    // Function to check for changes in the email field
    function checkEmailFieldChange() {
      var emailFieldValue = jQuery('.gform-body input[type="email"]').val();
      var existingEmailValue = jQuery('#db_Fields_emailvalue__Value').val();
      if (emailFieldValue !== existingEmailValue) {
        jQuery('#db_Fields_emailvalue__Value').val(emailFieldValue);
      }
    }
    // Function to check for changes in the company field
    function checkCompanyFieldChange() {
      var companyFieldValue = jQuery('.gform-body input[placeholder="Company"]').val();
      var existingCompanyValue = jQuery('#db_Fields_companyvalue__Value').val();
      if (companyFieldValue !== existingCompanyValue) {
        jQuery('#db_Fields_companyvalue__Value').val(companyFieldValue);
      }
    }
    // Check for changes when the email field loses focus
    jQuery('.gform-body input[type="email"]').blur(function() {
      checkEmailFieldChange();
    });
    // Check for changes when the company field loses focus
    jQuery('.gform-body input[placeholder="Company"]').blur(function() {
      checkCompanyFieldChange();
    });
    // Check for changes when the form is submitted
    jQuery('form').submit(function() {
      checkEmailFieldChange();
      checkCompanyFieldChange();
    });
  });
</script>
<form id="db_form" method="post">
  <input id="db_Fields_emailvalue__Value" name="db.Fields[emailvalue].Value" type="hidden" value="" data-sc-field-name="email">
  <input id="db_Fields_companyvalue__Value" name="db.Fields[companyvalue].Value" type="hidden" value="Aras Corp" data-sc-field-name="company">
  <input id="db_Fields_0001__Value" name="db.Fields[0001].Value" type="hidden" value=" " data-sc-field-name="DB_annual_revenue">
  <input id="db_Fields_0002__Value" name="db.Fields[0002].Value" type="hidden" value=" " data-sc-field-name="DB_Company_Facebook_url">
  <input id="db_Fields_0003__Value" name="db.Fields[0003].Value" type="hidden" value=" " data-sc-field-name="DB_Company_Twitter_url">
  <input id="db_Fields_0004__Value" name="db.Fields[0004].Value" type="hidden" value=" " data-sc-field-name="DB_email">
  <input id="db_Fields_0005__Value" name="db.Fields[0005].Value" type="hidden" value=" " data-sc-field-name="DB_executive_city">
  <input id="db_Fields_0006__Value" name="db.Fields[0006].Value" type="hidden" value=" " data-sc-field-name="DB_executive_country">
  <input id="db_Fields_0007__Value" name="db.Fields[0007].Value" type="hidden" value=" " data-sc-field-name="DB_executive_description">
  <input id="db_Fields_0008__Value" name="db.Fields[0008].Value" type="hidden" value=" " data-sc-field-name="DB_executive_linkedIn_profile">
  <input id="db_Fields_0009__Value" name="db.Fields[0009].Value" type="hidden" value=" " data-sc-field-name="DB_executive_state">
  <input id="db_Fields_0010__Value" name="db.Fields[0010].Value" type="hidden" value=" " data-sc-field-name="DB_first_name">
  <input id="db_Fields_0011__Value" name="db.Fields[0011].Value" type="hidden" value=" " data-sc-field-name="DB_job_function">
  <input id="db_Fields_0012__Value" name="db.Fields[0012].Value" type="hidden" value=" " data-sc-field-name="DB_job_level">
  <input id="db_Fields_0013__Value" name="db.Fields[0013].Value" type="hidden" value=" " data-sc-field-name="DB_last_name">
  <input id="db_Fields_0014__Value" name="db.Fields[0014].Value" type="hidden" value=" " data-sc-field-name="DB_phone">
  <input id="db_Fields_0015__Value" name="db.Fields[0015].Value" type="hidden" value=" " data-sc-field-name="DB_title">
  <input id="db_Fields_0015__Value" name="db.Fields[0015].Value" type="hidden" value=" " data-sc-field-name="DB_employee_count">
</form>

<script>
  window.onload = function() {
    // Function to set cookies
    function setCookie(name, value) {
      document.cookie = `${name}=${value}`;
    }
    // Function to get value from input and set cookie
    function setInputCookie(inputElement) {
      const fieldName = inputElement.getAttribute('data-sc-field-name');
      const value = inputElement.value;
      setCookie(fieldName, value);
      // Check if there's a matching field in the form body
      const formField = document.querySelector(`.gform-body [value="${fieldName}"]`);
      if (formField) {
        formField.value = value; // Set value in the form field
      }
    }
    // Get all input elements within the form
    const inputs = document.querySelectorAll('#db_form input');
    // Loop through each input to set initial cookies and attach event listeners
    inputs.forEach(function(input) {
      setInputCookie(input);
      input.addEventListener('change', function() {
        setInputCookie(this);
      });
    });
  };
  //
</script>


<?/* Since WPEngine can't do UTM parameters through PHP, we'll just do it with JS */ ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Function to extract UTM parameters from the URL
    function getUTMParameters() {
      // lets use the localStorage object we saved
      var utmJson = localStorage.getItem('aras_utm');
      if( utmJson ){
        return JSON.parse(utmJson);
      }
      return {};
    }

    function getRaidParameters() {
      // lets use the localStorage object we saved
      var raidJson = localStorage.getItem('aras_raid');
      if( raidJson ){
        return JSON.parse(raidJson);
      }
      return {};
    }
    // Function to autofill form fields with UTM parameters
    function autofillFormFields() {
      var utmParameters = getUTMParameters();
      Object.keys(utmParameters).forEach(function(key) {
        var fieldValue = utmParameters[key];
        var fields = document.querySelectorAll('input[placeholder="utm_' + key + '"],[data-field-name="utm_'+key+'"]>input'); // Find field with placeholder matching UTM parameter
        fields.forEach( field =>  field.value = fieldValue ); // Populate field with UTM parameter value
      });
      var raidParameters = getRaidParameters();
      Object.keys(raidParameters).forEach(function(key) {
        console.log( key, raidParameters[key] );
        var fieldValue = raidParameters[key];
        var fields = document.querySelectorAll('input[placeholder="' + key + '"],[data-field-name="'+key+'"]>input'); // Find field with placeholder matching UTM parameter
        fields.forEach( field =>  field.value = fieldValue );
      });
    }
    autofillFormFields();
    // also autofill form fields whenever a gravity form is loaded
    document.addEventListener('gform/post_render', autofillFormFields);
    document.addEventListener('gform_post_render', autofillFormFields);
  });
</script>