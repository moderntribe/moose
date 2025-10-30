const init = () => {
	document.addEventListener('click', (e) => {
		if ( ! e.target.classList.contains( 'acf-header-swatch' )) {
			return;
		}

		const button  = e.target;
		const wrapper = button.closest( '.acf-header-color-picker' );
		const input   = wrapper.querySelector( 'input[type="hidden"]' );
		const all     = wrapper.querySelectorAll( '.acf-header-swatch' );

		all.forEach( b => b.removeAttribute( 'aria-checked' ) );
		button.setAttribute( 'aria-checked', 'true' );
		input.value = button.dataset.value;
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
	});
}

export default init;
