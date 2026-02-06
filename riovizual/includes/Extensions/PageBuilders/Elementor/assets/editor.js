document.addEventListener( 'DOMContentLoaded', function () {
	if ( typeof elementor === 'undefined' ) {
		return;
	}
	// Table create link
	elementor.channels.editor.on( 'riovizual:table:create', function () {
		const win = window.open( riovizualEmentorData.add_new_table_url, '_blank' );
		win.focus();
	} );
} );
