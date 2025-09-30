const SvgIconScaleBalance = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="m14 12 12 18c0 4.218-4 10-12 10S2 34.218 2 30l12-18Zm0 0h36m0 0 12 18c0 4.218-4 10-12 10s-12-5.782-12-10l12-18Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
		<path
			d="M32 6v46M16 58h32"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="M2 30h24M38 30h24"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
	</svg>
);
export default SvgIconScaleBalance;
