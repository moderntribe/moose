const SvgIconSuitcaseTravel = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="M42 14V4H22v10"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="M14 14v44M50 14v44"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
		<path
			d="M56 14H8a6 6 0 0 0-6 6v32a6 6 0 0 0 6 6h48a6 6 0 0 0 6-6V20a6 6 0 0 0-6-6Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
	</svg>
);
export default SvgIconSuitcaseTravel;
