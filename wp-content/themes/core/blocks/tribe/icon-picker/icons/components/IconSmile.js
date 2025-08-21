const SvgIconSmile = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="M32 62c16.569 0 30-13.431 30-30C62 15.431 48.569 2 32 2 15.431 2 2 15.431 2 32c0 16.569 13.431 30 30 30Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="M20 32a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM44 32a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"
			fill="currentColor"
		/>
		<path
			d="M42.4 44a12 12 0 0 1-20.8 0"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
	</svg>
);
export default SvgIconSmile;
