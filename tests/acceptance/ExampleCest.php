<?php declare(strict_types=1);

class ExampleCest {
    public function test_it_works( AcceptanceTester $I ): void {
        $I->amOnPage('/');
    }

}
