<?php

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Проверить, что страничка и api отвечают и возвращают корректные ответы' );
$I->amOnPage( '/' );
$I->seeResponseCodeIs( \Codeception\Util\HttpCode::OK );
$I->see( 'Solve Your PC Problem' );
$I->amOnPage( '/api.php?symptom=no sound' );
$I->seeResponseCodeIs( \Codeception\Util\HttpCode::OK );
$I->see( 'Realtek Sound Drivers Problem' );
$I->see( 'Windows 10 keeps dropping sound device' );
$I->dontSee( 'the keyboard or mouse quit working' );