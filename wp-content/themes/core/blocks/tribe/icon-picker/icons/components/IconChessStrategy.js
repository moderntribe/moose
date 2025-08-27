const SvgIconChessStrategy = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="M32 22c5.523 0 10-4.477 10-10S37.523 2 32 2 22 6.477 22 12s4.477 10 10 10ZM18 22h28"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="M38 22a47.732 47.732 0 0 0 10.6 30M15.4 52A47.732 47.732 0 0 0 26 22"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
		<path
			d="M54 52H10v10h44V52Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
	</svg>
);
export default SvgIconChessStrategy;
