acf.add_filter('color_picker_args', function( args, $field ){
	// do something to args
	args.palettes = ['#971a42', '#5b1347', '#0077c2', '#3f0f33']
	// return
	return args;
});
