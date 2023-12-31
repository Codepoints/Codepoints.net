<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('check the "random" redirect');

$I->expectTo('see a redirect from calling /random');

$I->stopFollowingRedirects();
$I->amOnPage('/random');

$I->seeResponseCodeIs(303);

$I->seeHttpHeader('Location');
$locationHeader = $I->grabHttpHeader('Location');
$I->assertRegExp('~^/U\\+[0-9A-F]{4,6}$~', $locationHeader);
