import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import DynamicColorPicker from 'components/DynamicColorPicker';

const mountColorPicker = (el) => {
	if ( ! el || el.__mounted) {
		return;
	}
	el.__mounted = true;

	const props                         = JSON.parse( el.dataset.props || '{}' );
	const { colorAttribute, value: initialValue, colorsToUse } = props;

	const Bridge = () => {
		const [current, setCurrent] = useState( initialValue );

		useEffect(() => {
			const input = document.querySelector( `input[name = "${colorAttribute}"]` );

			if (input) {
				const hex = normalizeValue( current );
				input.value = extractSlugByHex(hex, colorsToUse);
				input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			}
		}, [current, name]);

		const normalizeValue = ( val ) => {
			if ( ! val || typeof val !== 'object' ) {
				return val ?? '';
			}

			if (val.slug) {
				return val.slug;
			}

			if (val.value) {
				return val.value;
			}
			const first = Object.values( val ).find( v => typeof v === 'string' );

			return first ?? '';
		};


		const extractSlugByHex = ( hex ) => {
			if (!hex)  {
				return '';
			}
			const entry = colorsToUse.find( color => {
				return color.color.toLowerCase() === hex.toLowerCase()
			});

			return entry?.slug ?? hex;
		};

		return (
			<DynamicColorPicker
				{...props}
				colorsToUse={colorsToUse}
				colorValue={normalizeValue(current)}
				onChange={setCurrent}
			/>
		);
	};

	const root = createRoot( el );
	root.render( <Bridge /> );
};

const init = () => {
	window.MTColorPickerBridge ??= [];
	window.MTColorPickerBridge.forEach( ({ el }) => mountColorPicker( el ) );
	window.MTColorPickerBridg = { push: ({ el }) => mountColorPicker( el ) };


	['acf/render_block_preview', 'acf/setup_fields'].forEach((event) => {
		window.addEventListener(event, () => {
			document
				.querySelectorAll( '.acf-color-picker-wrapper' )
				.forEach( (el) => mountColorPicker( el ) );
		});
	});

};

export default init;
