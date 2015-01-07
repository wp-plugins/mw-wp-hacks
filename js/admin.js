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

	/**
	 * カスタム投稿タイプ表示件数
	 */
	$( '#mw-wp-hacks-cpt-archive-posts p' ).each( function( i, e ) {
		var disable_check = function( input_default_check ) {
			var is_default = input_default_check.prop( 'checked' );
			var input_number = input_default_check.parents( 'p' ).find( 'input[type="number"]' );
			if ( is_default ) {
				input_number.attr( 'disabled', 'disabled' );
			} else {
				input_number.removeAttr( 'disabled' );
			}
		}
		var input_default_check = $( e ).find( '.mw-wp-hacks-cpt-archive-posts-use-default' );
		disable_check( input_default_check )

		input_default_check.click( function() {
			disable_check( $( this ) );
		} );
	} );
} );