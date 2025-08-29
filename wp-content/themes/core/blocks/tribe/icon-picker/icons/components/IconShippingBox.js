const SvgIconShippingBox = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="M2 14h60"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
		<path
			d="M62 14 48 2H16L2 14v48h60V14Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="M40 34H24V14l6-12h4l6 12v20Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
	</svg>
);
export default SvgIconShippingBox;
