# Block Templates

Last Updated: 04/23/24

Currently we support creating static and dynamic custom blocks. Generated custom blocks ultimately live in the core theme under `./blocks/tribe`.

Please see the WordPress documentation on [external block templates here](https://developer.wordpress.org/news/2024/04/16/creating-an-external-project-template-for-create-block/).

## Usage

In your command line tool, you can run `npm run create-block`. This will trigger the create block tool. Once within this tool, follow the prompts to complete creating your block 

- Steps to create a custom block: http://p.tri.be/i/Iaz9Ym
	1. Choose the template variant (static or dynamic)
	2. Enter the slug for the block
	3. Enter the name for the block
	4. Enter a short description of the block
	5. Pick a [dashicon](https://developer.wordpress.org/resource/dashicons/) to assign to the block
	6. Choose a category to assign to your block
- Created block markup (static variant): http://p.tri.be/i/0HRhUd

## Gotchas

- Because the `create-block` script inherits the code that creates the "plugin" version of a block, it also inherits the code where the `textdomain` property in the `block.json` file is automatically set to the defined block slug. This only applies to the `block.json` file, as we use the `namespace` in the template files to get around this. Please remember to update the `textdomain` property in the `block.json` file once your block gets generated.
- Once the block is created, don't forget to add the block slug to the block definers `TYPES` array in the core plugin under `./src/Blocks/Blocks_Definer.php`.
