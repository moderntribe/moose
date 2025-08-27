const SvgIconStrategyPuzzle = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="M4 32h8v3a5 5 0 1 0 10 0v-3h20v-3a5 5 0 1 1 10 0v3h8"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="M32 60v-8h3a5 5 0 1 0 0-10h-3V22h-3a5 5 0 1 1 0-10h3V4"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="M60 4H4v56h56V4Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
	</svg>
);
export default SvgIconStrategyPuzzle;
