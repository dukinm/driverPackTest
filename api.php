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
$mysqliConnection = database_init( $config );
$solutions = database_api_find_solutions( $mysqliConnection, $symptom );

# echo json_encode( $solutions );

$output = '';

$previousSymptomId = null;

foreach ( $solutions as $solution ) {
    if ( $solution[ 'symptomId' ] !== $previousSymptomId ) {
        if ( ! empty( $output ) ) {
            $output .= '</ul>';
        }
        $output .= '<h2 class="results__symptom results__symptom--' . $solution[ 'categoryName' ] . '" data-score="' . $solution[ 'score' ] . '">[' . $solution[ 'categoryName' ] . '] ' . htmlentities($solution[ 'symptomDescription' ]) . '</h2><ul class="results__solutions">';
    }
    $previousSymptomId = $solution[ 'symptomId' ];
    $output .= '<li class="results__solution">' . htmlentities($solution[ 'solutionDescription' ]) . '</li>';
}
if ( ! empty( $output ) ) {
    $output .= '</ul>';
}

echo $output;