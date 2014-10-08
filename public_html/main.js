( function () {
	$( '.nagf-select-project' ).on( 'change', function () {
		location.href = './?project=' + this.value;
	} );
	$( '.nagf-select-metric' ).on( 'change', function () {
		location.hash = '#' + this.value;
	} );
}() );
