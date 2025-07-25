export default function IconCircleCheck( props ) {
	return (
		<svg
			xmlns="http://www.w3.org/2000/svg"
			{ ...props }
			width="64"
			height="64"
			viewBox="0 0 64 64"
			fill="none"
		>
			<path
				d="M54 24C55.2 27 56 30.4 56 34C56 48.4 44.4 60 30 60C15.6 60 4 48.4 4 34C4 19.6 15.6 8 30 8C35 8 39.8 9.4 43.8 12"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
			/>
			<path
				d="M20 26L30 36L60 6"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
		</svg>
	);
}
