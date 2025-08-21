const SvgIconPresentation = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="M22 22v40M14 14a6 6 0 1 0 0-12 6 6 0 0 0 0 12ZM42 22H10a4 4 0 0 0-4 4v36"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="M28 8h30v36H30"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="m44 44 10 18"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
	</svg>
);
export default SvgIconPresentation;
