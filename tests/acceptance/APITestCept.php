<?php

$I = new AcceptanceTester( $scenario );
$I->wantTo( 'Проверить, что страничка и api отвечают и возвращают корректные ответы' );
$I->amOnPage( '/' );
$I->seeResponseCodeIs( \Codeception\Util\HttpCode::OK );
$I->see( 'Solve Your PC Problem' );
$I->amOnPage( '/api.php?symptom=no sound' );
$I->seeResponseCodeIs( \Codeception\Util\HttpCode::OK );
$I->see( 'the sound doesn\'t work' );
$I->see( 'check and make sure that the speakers are turned on and that they have power' );
$I->dontSee( 'the keyboard or mouse quit working' );