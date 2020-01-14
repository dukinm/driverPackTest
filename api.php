<?php
/**
 * Created by PhpStorm.
 * User: mihaildukin
 * Date: 2020-01-12
 * Time: 02:28
 */

include_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . 'lib.php';



$morphy = morphy_init( 'en' );

$DBFile = 'fastest' . DIRECTORY_SEPARATOR . 'sample-data.json';
$DBJson = file_get_contents( $DBFile );
$DB = json_decode( $DBJson, 1 );

$request = [];
if ( ! empty( $_POST ) ) {

    $request = $_POST;

}
else if ( ! empty( $_GET ) ) {

    $request = $_GET;

}
else {

    $request = file_get_contents( 'php://input' );
    $request = json_decode( $request,1 );

}
$symptom = string_to_normal_string( $morphy, $request[ 'symptom' ] );
$symptomParts = explode( ' ', $symptom );
$symptomPartsCount = count( $symptomParts );

$results = [];

foreach ( $DB as $categoryKey => $category ) {

    foreach ( $category as $symptomAndSolutions ) {

        $countOfSymptomPartWasFound = 0;
        $symptomAndSolutions[ 'symptomTmp' ] = string_to_normal_string( $morphy, $symptomAndSolutions[ 'symptom' ] );

        foreach ( $symptomParts as $symptomPart ) {


            if ( substr_count( $symptomAndSolutions[ 'symptomTmp' ], $symptomPart ) > 0 ) {

                $countOfSymptomPartWasFound++;

            }

        }

        if ( $countOfSymptomPartWasFound === $symptomPartsCount ) {

            if ( ! isset( $results[ $categoryKey ] ) ) {

                $results[ $categoryKey ] = [];

            }

            $results[ $categoryKey ] = array_merge( $results[ $categoryKey ], [ $symptomAndSolutions ] );
        }

    }

}

# echo json_encode( $results );

$output = [];

foreach ( $results  as $category => $symptomsAndSolutions ) {

    foreach ( $symptomsAndSolutions as $symptomAndSolutions ) {

        $solutions = [];

        foreach ( $symptomAndSolutions[ 'solutions' ] as $solution ) {

            $solutions[] = '<li class="results__solution">' . $solution . '</li>';

        }

        $solutions = implode( '', $solutions );

        $output[] = '<h2 class="results__symptom">[' . $category . '] ' . $symptomAndSolutions[ 'symptom' ] . '</h2><ul class="results__solutions">' . $solutions . '</ul>';

    }

}

$output = implode( $output );

echo $output;