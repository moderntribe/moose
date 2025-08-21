const SvgIconCircleCheck = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="M54 24c1.2 3 2 6.4 2 10 0 14.4-11.6 26-26 26S4 48.4 4 34 15.6 8 30 8c5 0 9.8 1.4 13.8 4"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
		<path
			d="m20 26 10 10L60 6"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
	</svg>
);
export default SvgIconCircleCheck;
