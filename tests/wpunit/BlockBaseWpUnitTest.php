<?php declare(strict_types=1);

class BlockBaseWpUnitTest extends \Codeception\TestCase\WPTestCase {

    use \Tribe\Plugin\Assets\Traits\Assets;

    public function setUp(): void {
        parent::setUp();

        $block_dir    = get_template_directory() . '/dist/blocks/core/button';
        $editor_asset = $block_dir . '/editor.asset.php';
        mkdir( $block_dir, 0777, true );

        $editor_asset_content = file_get_contents( __DIR__ . '/../_data/editor_asset_test.php' );
        $file_handler         = fopen( $editor_asset, 'w' );
        fwrite( $file_handler, $editor_asset_content );
        fclose( $file_handler );
    }

    public function test_editor_styles_can_get_version_from_asset_php()
    {
        $args = $this->get_asset_file_args( get_theme_file_path( "dist/blocks/core/button/editor.asset.php" ) );
        self::assertArrayHasKey( 'version', $args );
        $version = $args['version'] ?? false;
        self::assertEquals( '024bef6fb3a0bc82cb17', $version );
    }

    protected function tearDown(): void {
        parent::tearDown();

        $block_dir    = get_template_directory() . '/dist/blocks/core/button';
        $editor_asset = $block_dir . '/editor.asset.php';

        unlink( $editor_asset );
        rmdir( $block_dir );
    }

}
