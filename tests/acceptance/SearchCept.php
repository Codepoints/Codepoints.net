<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('check the basic search');

$I->amOnPage('/search');
$I->seeResponseCodeIs(200);
$I->seeElement('.page-footer');
$I->dontSeeElement('.tiles');

$I->amOnPage('/search?q=aiw7To0ee4doo9ei');
$count = $I->grabAttributeFrom('main', 'data-count');
$I->expect('search to handle missing results gracefully');
$I->assertTrue(ctype_digit($count));
$I->seeResponseCodeIs(200);
$I->assertEquals(0, (int)$count);
$I->see('No Codepoints Found', 'h1');

$I->amOnPage('/search?q=latin');
$count = $I->grabAttributeFrom('main', 'data-count');
$I->assertTrue(ctype_digit($count));
$I->expect('search to be reasonably confined');
$I->assertLessThan(100000, (int)$count);
$I->assertLessThan((int)$count, 1);

$I->amOnPage('/search?q=small%20capital');
$count = $I->grabAttributeFrom('main', 'data-count');
$I->assertTrue(ctype_digit($count));
$I->expect('search to find multi-word names');
$I->assertLessThan(100000, (int)$count);
$I->assertLessThan((int)$count, 1);

$I->amOnPage('/search?q=ccc');
$count = $I->grabAttributeFrom('main', 'data-count');
$I->assertTrue(ctype_digit($count));
$I->expect('search to ignore empty property names');
$I->assertEquals(0, (int)$count);
