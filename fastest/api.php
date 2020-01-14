<?php
/**
 * Created by PhpStorm.
 * User: mihaildukin
 * Date: 2020-01-07
 * Time: 16:28
 */

$DBFile = 'sample-data.json';
$DBJson = file_get_contents( $DBFile );
$DB = json_decode( $DBJson, 1 );

$request = [];
if ( ! empty( $_POST ) ) {

    $request = $_POST;

}
else {

    $request = file_get_contents( 'php://input' );
    $request = json_decode( $request,1 );

}
$symptom = $request[ 'symptom' ];
$symptomParts = explode( ' ', $symptom );
$symptomPartsCount = count( $symptomParts );


$results = [];

foreach ( $DB as $categoryKey => $category ) {

    foreach ( $category as $symptomAndSolutions ) {

        $countOfSymptomPartWasFound = 0;

        foreach ( $symptomParts as $symptomPart ) {


            if ( $symptomPart === 'no' || $symptomPart === 'not' || substr_count( $symptomPart, 'n\'t' ) > 0 ) {
                $symptomPart = 'n\'t'; # все проблемы в базе данных содержат отрицание указанное через постфикс n't
            }

            if ( substr_count( $symptomAndSolutions[ 'symptom' ], $symptomPart ) > 0 ) {

                $countOfSymptomPartWasFound++;

            }

        }

        if ( $countOfSymptomPartWasFound === $symptomPartsCount ) {

            if ( !isset( $results[ $categoryKey ] ) ) {

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

            $solutions[] = '<li>' . $solution . '</li>';

        }

        $solutions = implode( '', $solutions );

        $output[] = '<h2>[' . $category . '] ' . $symptomAndSolutions[ 'symptom' ] . '</h2><ul>' . $solutions . '</ul>';

    }

}

$output = implode( $output );

echo $output;