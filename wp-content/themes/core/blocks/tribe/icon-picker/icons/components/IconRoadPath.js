export default function IconRoadPath( props ) {
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
				d="M2 60L20 4"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
			/>
			<path
				d="M44 4L62 60"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
			/>
			<path
				d="M32 10V16"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
			<path
				d="M32 26V34"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
			<path
				d="M32 44V54"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
		</svg>
	);
}
