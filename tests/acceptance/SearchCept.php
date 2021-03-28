<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('check the basic search');

$I->amOnPage('/search');
$I->seeResponseCodeIs(200);
$I->seeElement('.page-footer');
$I->dontSeeElement('.tiles');

$I->amOnPage('/search?q=epres');
$count = $I->grabAttributeFrom('main', 'data-count');
$I->assertTrue(ctype_digit($count));
$I->expect('search to be reasonably confined');
$I->assertLessThan(100000, (int)$count);
