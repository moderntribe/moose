const SvgIconClockTime = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="M32 4v8M60 32h-8M32 60v-8M4 32h8"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
		<path
			d="M32 60c15.464 0 28-12.536 28-28S47.464 4 32 4 4 16.536 4 32s12.536 28 28 28Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="m20 16 12 16h12"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
	</svg>
);
export default SvgIconClockTime;
