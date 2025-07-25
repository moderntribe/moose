export default function IconHourglassTime( props ) {
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
				d="M16 60V48C16 38 26 32 26 32C26 32 16 26 16 16V4"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
			/>
			<path
				d="M48 60V48C48 38 38 32 38 32C38 32 48 26 48 16V4"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
			/>
			<path
				d="M10 4H54"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
			<path
				d="M10 60H54"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
		</svg>
	);
}
