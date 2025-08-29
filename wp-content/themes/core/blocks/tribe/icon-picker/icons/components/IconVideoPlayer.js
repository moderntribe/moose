const SvgIconVideoPlayer = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="m42 26 20-10v32L42 38"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
		<path
			d="M36 10H8a6 6 0 0 0-6 6v32a6 6 0 0 0 6 6h28a6 6 0 0 0 6-6V16a6 6 0 0 0-6-6Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
	</svg>
);
export default SvgIconVideoPlayer;
