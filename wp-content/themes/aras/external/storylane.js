function arasInitDemandBase(){
	
	// add our demandbase form
	const db_form = document.createElement('div');
	db_form.innerHTML = `<form id="db_form" method="post">
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
</form>`;

	// append the demandbase form
	document.body.appendChild( db_form );

	// append the demandbase script
	(function(d,b,a,s,e){ var t = b.createElement(a),
		fs = b.getElementsByTagName(a)[0]; t.async=1; t.id=e; t.src=s;
		fs.parentNode.insertBefore(t, fs); })
	(window,document,'script','https://tag.demandbase.com/197d25a6375588c2.min.js','demandbase_js_lib');  

	const db_fields = document.querySelectorAll('[data-sc-field-name]:not([data-sc-field-name="email"])');

	const updateField = field => {
		// lets find the corresponding marketo field
		let name = field.getAttribute('data-sc-field-name');
		// lets massage the name to salesforce field name
		name = name.replace(/_([a-z])/g, matches => '_'+matches[1].toUpperCase()) + '__c';
		const mkto_field = document.querySelector('[name="'+name+'"]');
		if( mkto_field ){
			mkto_field.value = field.value;
		}
	};

	const fieldMutator = new MutationObserver( (records) => {
		records.forEach( record => updateField( record.target ) );
	});

	db_fields.forEach( f => {
		updateField(f);
		f.addEventListener('change', e => {
			updateField( f );
		});
		fieldMutator.observe( f, {attributes: true, attributeFilter: ['value']} );
	});


	const email = document.querySelector('[name="Email"]');

	if( email ){
		email.addEventListener('change', e => {
			const db_field = document.querySelector('[data-sc-field-name="email"]');
			if( db_field ){
				db_field.value = email.value;
			}
		});
	}

	setTimeout( () => {
		// clean up the marketo form so we can style it...
		const mkto_fields = document.querySelectorAll('.mktoField');
		mkto_fields.forEach( mkto_field => {
			const col = mkto_field.closest('.mktoFormCol');
			if( col && col.tagName && col.tagName == 'DIV' && mkto_field.getAttribute('type')){
				col.classList.add('type_'+ mkto_field.getAttribute('type') );
			}
		});
	}, 100 );
}