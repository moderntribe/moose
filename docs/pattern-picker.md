# Pattern Selection Modal

Many times when creating new post types, we want to provide the user with a selection of pre-made templates to start a new post with. In order to allow for this in Moose, the template property of the post type object must be updated, otherwise the selection modal will never show up. [Learn more about the pattern selection modal here](https://fullsiteediting.com/lessons/introduction-to-block-patterns/#h-how-to-display-the-pattern-selection-when-creating-a-new-page).

The default "post" post type includes the code below and [can be referenced](../wp-content/plugins/core/src/Post_Types/Post/Config.php) when creating new post types. 

### Config.php
```
/**
 * Define block templates for initial editor state
 */
public function register_block_template(): void {
	$post_type_object           = get_post_type_object( $this->post_type );
	$post_type_object->template = [
		[
			'core/pattern',
			[
				'slug' => 'patterns/post',
			],
		],
	];
}
```

### Post_Type_Subscriber.php
```
public function register(): void {
	parent::register();

	$this->block_templates();
}

public function block_templates(): void {
	add_action( 'init', function (): void {
		$this->container->get( Config::class )->register_block_template();
	} );
}
```
