jQuery( function( $ ) {

	var cnt = $( '#mw-wp-hacks-thumbnail .add-box' ).length + $( '#mw-wp-hacks-widget .add-box' ).length;

	/**
	 * 削除ボタン
	 */
	$( '.mwhacks-remove' ).on( 'click', function() {
		cnt++;
		$( this ).closest( '.add-box' ).fadeOut( function() {
			$( this ).remove();
		} );
	} );

	/**
	 * 追加ボタン
	 */
	$( '.mwhacks-add' ).click( function() {
		cnt ++;
		var clone = $( this ).closest( 'tr' ).find( '.add-box:first' ).clone( true );
		clone.find( 'input, select' ).each( function() {
			$( this ).attr( 'name', $( this ).attr( 'name' ).replace( /\[\d+\]/, '[' + cnt + ']' ) );
		} );
		$( this ).closest( 'tr' ).find( '.add-box:first' ).after( clone.fadeIn() );
	} );

} );