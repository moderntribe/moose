const SvgIconTimeline = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="M32 2v60M62 6H46v16h16V6ZM38 14h-6M62 42H46v16h16V42ZM38 50h-6M2 40h16V24H2v16ZM26 32h6"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
	</svg>
);
export default SvgIconTimeline;
