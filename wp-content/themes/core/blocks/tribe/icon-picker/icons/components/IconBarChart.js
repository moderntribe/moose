export default function IconBarChart( props ) {
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
				d="M4 58H60"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
			<path
				d="M18 26H6V50H18V26Z"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
			<path
				d="M38 6H26V50H38V6Z"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
			<path
				d="M58 36H46V50H58V36Z"
				stroke="currentColor"
				strokeWidth="2"
				strokeMiterlimit="10"
				strokeLinecap="square"
			/>
		</svg>
	);
}
