"use strict";

( function ( $ ) {
	var $textarea = $( 'textarea[name="hm_post_css"]' );
	var editor = wp.codeEditor.initialize( $textarea );
	var suspendEditorUpdate = false;
	editor.codemirror.on( 'change', function ( codemirror ) {
		suspendEditorUpdate = true;
		$textarea.val( codemirror.getValue() ).trigger( 'change' );
		suspendEditorUpdate = false;
	} );
	$textarea.on( 'change', function ( value ) {
		if ( ! suspendEditorUpdate ) {
			editor.codemirror.setValue( value );
		}
	} );
	editor.codemirror.setValue( $textarea.val() );
	setTimeout( function () {
		editor.codemirror.refresh();
	}, 100 );
} )( jQuery );
