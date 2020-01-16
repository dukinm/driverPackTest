<?php
/**
 * Created by PhpStorm.
 * User: mihaildukin
 * Date: 2020-01-16
 * Time: 05:42
 */

include_once $_SERVER[ 'DOCUMENT_ROOT' ] . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
include_once $_SERVER[ 'DOCUMENT_ROOT' ] . DIRECTORY_SEPARATOR . 'config.php';
include_once $_SERVER[ 'DOCUMENT_ROOT' ] . DIRECTORY_SEPARATOR . 'lib.php';

$filesToImportFolder = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;

$filesToImportMask = $filesToImportFolder . '*.json';
$filesToImport = glob( $filesToImportMask );

if ( is_array( $filesToImport ) && ( ! empty( $filesToImport ) ) ) {

    $DBFile = $filesToImport[ 0 ];
    $DBJson = file_get_contents( $DBFile );
    $DB = json_decode( $DBJson, 1 );

    $morphy = morphy_init();
    $mysqliConnection = database_init( $config );
    $categoriesInDB = database_api_get_categories( $mysqliConnection );
    $categoriesInDBArrayIdByCategory = [];

    foreach ( $categoriesInDB as $categoryInDB ) {

        $categoriesInDBArrayIdByCategory[ $categoryInDB[ 'categoryName' ] ] = $categoryInDB[ 'categoryId' ];

    }

    foreach ( $DB as $categoryKey => $category ) {

        foreach ($category as $symptomAndSolutions) {

            if( empty( $symptomAndSolutions[ 'solutions' ]  ) ) {

                continue;

            }
            $symptomDescription = $symptomAndSolutions[ 'symptom' ];
            $symptomDescriptionNormalize = string_to_normal_string( $morphy, $symptomDescription );
            $categoryId = $categoriesInDBArrayIdByCategory[ $categoryKey ];
            $symptomId = database_api_set_symptom( $mysqliConnection, $categoryId, $symptomDescription, $symptomDescriptionNormalize );
            foreach ( $symptomAndSolutions[ 'solutions' ] as $solutionDescription ) {

                $solutionDescriptionNormalize = string_to_normal_string( $morphy, $solutionDescription );
                database_api_set_solution( $mysqliConnection, $symptomId, $solutionDescription, $solutionDescriptionNormalize );

            }

        }

    }

    database_close( $mysqliConnection );
    unlink( $DBFile );

    echo '<meta http-equiv="refresh" content="0;URL=http://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ] . '">';

}