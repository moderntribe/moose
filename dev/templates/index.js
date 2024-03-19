const { join } = require( 'path' );

module.exports = {
	blockTemplatesPath: join( __dirname, 'block' ),
	defaultValues: {
		attributes: {
			exampleTextControl: {
				type: 'string',
				default: '',
			},
		},
		dashicon: 'block-default',
		supports: {
			html: false,
			align: [ 'wide', 'grid', 'full' ],
			spacing: {
				margin: true,
				padding: true,
			},
		},
		viewScript: 'file:./view.js',
	},
	variants: {
		static: {},
		dynamic: {
			render: 'file:./render.php',
		},
	},
};
