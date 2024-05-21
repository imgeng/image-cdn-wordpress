<?php
$delivery_addresses = $delivery_addresses ?? [];
$url_host = $url_host ?? "";
?>

<style>
	/* Global styles */
	:root {
		--colorPrimaryDark: #0b132b;
		--colorPrimaryLight: #494f61;
		--colorPlaceholder: #9b9fa8;
		--colorBackground: #eeeff0;
		--colorBorder: #cdcfd3;
		--colorAccent: #2271b1;
		--gutter: 0 16px;
		--border: 0px solid var(--colorBorder);
		--radius: 3px;
	}

	#image_cdn_host_container {
		display: inline-block;
		font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
		position: absolute;
		left: 77px;
		top: 38px;

		@media screen and (max-width: 782px) {
			left: 0;
		}

		* {
			margin: 0;
			box-sizing: border-box;
		}

		#image_cdn_host_container {
			display: flex;
			justify-content: center;
			align-items: flex-start;
			height: 100vh;
			width: 100vw;
			background-color: var(--colorBackground);
		}

		/* Input styling */

		.input {
			display: flex;
			flex-direction: column;
			justify-content: center;
			border: var(--border);
			border-radius: var(--radius);
			height: 34px;
			width: 25em;
			padding: var(--gutter);

			@media screen and (max-width: 782px) {
				width: 100%;
			}
		}

		.input__active {
			border-color: var(--colorAccent);
			border-bottom-left-radius: 0px !important;
			border-bottom-right-radius: 0px !important;
		}

		.input__placeholder {
			display: flex;
			flex-direction: row;
			justify-content: space-between;
			align-items: center;
			background: transparent;
			color: var(--colorPrimaryDark);

			span {
				height: 16px;
				margin-top: -2px;
			}
		}

		.input:hover {
			cursor: pointer;
		}

		.placeholder {
			background: transparent;
			color: var(--colorPrimaryDark);
		}

		.input__selected {
			color: var(--colorPrimaryDark);
		}

		/* Dropdown styling */

		.structure {
			position: absolute;
			display: flex;
			flex-direction: column;
			justify-content: flex-start;
			max-height: 200px;
			width: 25em;
			overflow: scroll;
			background-color: white;
			box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.1);
			padding: 10px 0;

			@media screen and (max-width: 782px) {
				width: 100%;
			}
		}

		.structure > div {
			display: flex;
			flex-direction: row;
			align-items: center;
			padding: 5px 16px;
		}

		.structure > div:hover {
			background-color: var(--colorAccent);
			color: white;
			cursor: pointer;
		}

		.structure > div > h5 {
			font-weight: 600;
			margin-right: 4px;
		}

		.structure > div > p {
			font-weight: 400;
			font-size: 13px;
			color: var(--colorPlaceholder);
		}

		.hide {
			display: none;
		}
	}
</style>

<script>
	const dropdownIcon = () => {
		const dropdown = document.createElement('span');
		dropdown.innerHTML = `<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg"><path d="M5 6l5 5 5-5 2 1-7 7-7-7 2-1z" fill="#555"/></svg>`;
		return dropdown;
	}

	const addresses = [
		<?php foreach ( $delivery_addresses as $addr ) :
		$addr .= ".imgeng.in";?>
		{
			value: "<?php echo esc_html( $addr ); ?>",
			label: "<?php echo $addr ?>"
		},
		<?php endforeach; ?>
	]

	const selectArea = document.getElementById("image_cdn_host_container");

	const dropdown = () => {
		if (!selectArea) return;

		const select = document.getElementById("image_cdn_host_select");
		select.remove();

		const component = document.createElement("div");

		const input = createInput();
		const dropdown = showDropdown();

		component.appendChild(input);
		component.appendChild(dropdown);
		selectArea.appendChild(component);
	};

	const createInput = () => {
		// Creates the input outline
		const input = document.createElement("div");
		input.classList = "input";
		input.addEventListener("click", toggleDropdown);

		// Creates the input placeholder content
		const inputPlaceholder = document.createElement("div");
		inputPlaceholder.classList = "input__placeholder";

		const placeholder = document.createElement("p");
		placeholder.textContent = "<?php echo esc_html( __( 'Show registered delivery addresses', 'image-cdn' ) ); ?>";
		placeholder.classList.add('placeholder')

		// Appends the placeholder and chevron (stored in assets.js)
		inputPlaceholder.appendChild(placeholder);
		inputPlaceholder.appendChild(dropdownIcon());
		input.appendChild(inputPlaceholder);

		return input;
	};

	const showDropdown = () => {
		const structure = document.createElement("div");
		structure.classList.add("structure", "hide");

		addresses.forEach(address => {
			const {
				value,
				label,
			} = address;
			const option = document.createElement("div");
			option.addEventListener("click", () => selectOption(value));
			option.setAttribute("value", value);

			const l = document.createElement("div");
			l.textContent = label;

			option.appendChild(l);
			structure.appendChild(option);
		});
		return structure;
	};

	const toggleDropdown = () => {
		const dropdown = document.querySelector(".structure");
		dropdown.classList.toggle("hide");

		const input = document.querySelector(".input");
		input.classList.toggle("input__active");
	};

	const selectOption = (value) => {
		const host = document.getElementById("image_cdn_host");
		host.setAttribute("value", value);
		host.value = value;
		host.dispatchEvent(new Event('change'));
		toggleDropdown();
	};

	dropdown();

	<?php if ( !empty($url_host) ) : ?>
	selectOption("<?php echo esc_html($url_host); ?>");
	toggleDropdown();
	<?php endif; ?>
</script>