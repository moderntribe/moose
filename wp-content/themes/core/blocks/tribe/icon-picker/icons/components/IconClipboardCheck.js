const SvgIconClipboardCheck = ( props ) => (
	<svg
		width="1em"
		height="1em"
		viewBox="0 0 64 64"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		{ ...props }
	>
		<path
			d="m24 38 6 6 12-12M38 8a6 6 0 1 0-12 0h-6v8h24V8h-6Z"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
			strokeLinecap="square"
		/>
		<path
			d="M50 10h6v52H8V10h6"
			stroke="currentColor"
			strokeWidth={ 2 }
			strokeMiterlimit={ 10 }
		/>
	</svg>
);
export default SvgIconClipboardCheck;
