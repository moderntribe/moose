export default function IconVideoPlayer( props ) {
	return (
		<svg
			role="presentation"
			aria-hidden="true"
			xmlns="http://www.w3.org/2000/svg"
			{ ...props }
			width="64"
			height="64"
			viewBox="0 0 64 64"
			fill="none"
		>
			<path
				d="M42 26L62 16V48L42 38"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
			/>
			<path
				d="M36 10H8C4.68629 10 2 12.6863 2 16V48C2 51.3137 4.68629 54 8 54H36C39.3137 54 42 51.3137 42 48V16C42 12.6863 39.3137 10 36 10Z"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
		</svg>
	);
}
