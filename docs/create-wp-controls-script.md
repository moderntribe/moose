# Create WP Controls Script

The "Create WP Controls" script is used to quickly and easily add custom, core WP controls to core blocks. Note that while this can be used to add controls to custom blocks, it should only be used with core blocks, as there are better ways to add controls to custom blocks.

## Supported Control Types

- [ToggleControl](https://wordpress.github.io/gutenberg/?path=/docs/components-togglecontrol--docs) / `toggle`: A true/false field that can be used to assign a class or property when the control is toggled.
- [NumberControl](https://wordpress.github.io/gutenberg/?path=/docs/components-experimental-numbercontrol--docs) / `number`: A text field that only accepts numbers. Can be used to set a property with the number entered into the field.
- [SelectControl](https://wordpress.github.io/gutenberg/?path=/docs/components-selectcontrol--docs) / `select`: A normal select box field that can be used to give the user options and assign a property based on the value selected.

## Usage

1. Import the script
```js
import createWPControls from 'utils/create-wp-controls';
```

2. Create your settings object
```js
const settings = {
	attributes: {
		stackingOrder: {
			type: 'string',
		},
	},
	blocks: [ 'core/column' ],
	controls: [
		{
			applyClass: 'tribe-has-stacking-order',
			applyStyleProperty: '--tribe-stacking-order',
			attribute: 'stackingOrder',
			defaultValue: 0,
			helpText: __(
				'The stacking order of the element at mobile breakpoints. This setting only applies if the "Stack on mobile" setting for the Columns block is turned on.',
				'tribe'
			),
			label: __( 'Stacking Order', 'tribe' ),
			type: 'number',
		},
	],
};
```

Let's break this down a little bit:

```js
attributes: {
	stackingOrder: {
		type: 'string',
	},
},
```
> :bulb: First, we're creating an `attributes` object that is defining the attributes we want to add to the block. In this case, we're creating a `stackingOrder` attribute for the `core/column` block. 

```js
blocks: [ 'core/column' ],
```
> :bulb: Next, we define what blocks we want the controls to appear on. In this case we're saying that this control should appear on the `core/column` block.

```js
controls: [
	{
		applyClass: 'tribe-has-stacking-order',
		applyStyleProperty: '--tribe-stacking-order',
		attribute: 'stackingOrder',
		defaultValue: 0,
		helpText: __(
			'The stacking order of the element at mobile breakpoints. This setting only applies if the "Stack on mobile" setting for the Columns block is turned on.',
			'tribe'
		),
		label: __( 'Stacking Order', 'tribe' ),
		type: 'number',
	},
],
```
> :bulb: Lastly, we're defining the controls array that will be used to define what controls we want to create for the block we've defined. In this case we're defining a `number` control with a default value of `0`. The `applyClass` property tells the create control script that when the value is changed from the default, we should apply the `tribe-has-stacking-order` class. The `applyStyleProperty` property tells the create control script that the `--tribe-stacking-order` style property should be set to the value of the control when the default is changed.

> :memo: **Note:** You do not always have to set both the `applyClass` and `applyStyleProperty` properties. You can use one or the other separately. Using them together though can be a powerful tool in order to only apply the style property if the class is applied.

3. Call the create controls script and pass in your settings array.

```js
createWPControls( settings );
```

## Limitations

- Because the script currently only supports specific controls, additional time will be required to extend the script if a different type of control needs to be added. This can be done in the `determineControlToRender` function within the script. Make sure to import your control type!
- Currently the script only supports adding a class or a style property. There are no other functions of the script in terms of block output.
- Not really a limitation but I should note that we shouldn't use this script unless it's decided with the project team that these controls should be created using this method. There are other ways that may be better for your projects (block styles, adding classes manually, etc). If you feel strongly that this script gives the client the best experience, go for it!
