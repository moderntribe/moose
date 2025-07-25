export default function IconCube3d( props ) {
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
				d="M8 18L32 32L56 18"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
			/>
			<path
				d="M32 32V60"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
			/>
			<path
				d="M56 46V18L32 4L8 18V46L32 60L56 46Z"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
			/>
		</svg>
	);
}
