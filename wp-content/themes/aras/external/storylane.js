function arasInitDemandBase(){
	console.log('arasInitDemandBase');
	const db_fields = document.querySelectorAll('[data-sc-field-name]:not([data-sc-field-name="email"])');

	const updateField = field => {
		// lets find the corresponding marketo field
		let name = field.getAttribute('data-sc-field-name');
		console.log( name );
		// lets massage the name to salesforce field name
		name = name.replace(/_([a-z])/g, matches => '_'+matches[1].toUpperCase()) + '__c';
		console.log( [name, field, field.value] );
		const mkto_field = document.querySelector('[name="'+name+'"]');
		console.log( mkto_field );
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
			console.log( 'updateField', f );
			updateField( f );
		});
		fieldMutator.observe( f, {attributes: true, attributeFilter: ['value']} );
	});


	const email = document.querySelector('[name="Email"]');

	if( email ){
		email.addEventListener('change', e => {
			console.log( '')
			const db_field = document.querySelector('[data-sc-field-name="email"]');
			if( db_field ){
				db_field.value = email.value;
			}
		});
	}
}