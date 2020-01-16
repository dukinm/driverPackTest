<?php
/**
 * Created by PhpStorm.
 * User: mihaildukin
 * Date: 2020-01-13
 * Time: 06:19
 */

if( ! function_exists( 'morphy_init' ) ) {

    /**
     * @param string $language
     * @return object \cijic\phpMorphy\Morphy
     */
    function morphy_init( $language = 'en' ) {

        return new cijic\phpMorphy\Morphy( $language );

    }

}
if( ! function_exists( 'string_to_normal_string' ) ) {

    /**
     * @param $morphy
     * @param string $original
     * @return string
     */
    function string_to_normal_string( $morphy, $original = '' ) {

        $original = mb_strtoupper( $original );
        $words = explode( ' ', $original );
        foreach ( $words as $wordKey => $word ) {

            if ( $word === 'NO' || $word === 'NOPE' || substr_count($word,'N\'T') ) {

                $word = 'NOT';

            }

            $wordLemma = $morphy->lemmatize($word, phpMorphy::NORMAL);

            if( is_array( $wordLemma ) ) {

                $word = $wordLemma[0];

            }

            $words[ $wordKey ] = $word;

        }

        $words = implode(' ', $words);

        return $words;

    }

}
if( ! function_exists( 'database_init' ) ) {

    /**
     * @param array $config
     * @return bool|mysqli
     */
    function database_init( $config = [] ) {

        $mysqliConnection = mysqli_connect( $config[ 'DBHost' ], $config[ 'DBUser' ], $config[ 'DBPassword' ], $config[ 'DBName' ] );

        if( mysqli_connect_errno() ) {

            return false;

        }
        else{

            return $mysqliConnection;

        }

    }

}
if( ! function_exists( 'database_close' ) ) {

    /**
     * @param mysqli $mysqliConnection
     */
    function database_close( $mysqliConnection ) {

        mysqli_close( $mysqliConnection );

    }

}
if( ! function_exists( 'database_api_get_categories' ) ) {

    /**
     * @param mysqli $mysqliConnection
     * @return array|bool
     */
    function database_api_get_categories( $mysqliConnection ) {

        $sql = 'CALL `api_get_categories`();';

        if ( $result = mysqli_query( $mysqliConnection, $sql ) ) {

            $output = mysqli_fetch_all( $result, MYSQLI_ASSOC );
            mysqli_next_result( $mysqliConnection );
            mysqli_store_result( $mysqliConnection );
            mysqli_free_result( $result );
            return $output;

        }

        return false;
    }

}
if( ! function_exists( 'database_api_set_symptom' ) ) {

    /**
     * @param mysqli $mysqliConnection
     * @param int $categoryId
     * @param string $symptomDescription
     * @param string $symptomDescriptionNormalize
     * @return bool|int
     */
    function database_api_set_symptom( $mysqliConnection, $categoryId = 0, $symptomDescription = '', $symptomDescriptionNormalize = '' ) {

        $categoryId = intval( $categoryId );
        $symptomDescription = mysqli_real_escape_string( $mysqliConnection, $symptomDescription );
        $symptomDescriptionNormalize = mysqli_real_escape_string( $mysqliConnection, $symptomDescriptionNormalize );
        $sql = 'SELECT `api_set_symptom`("' . $categoryId . '", "' . $symptomDescription . '", "' . $symptomDescriptionNormalize . '") AS `api_set_symptom`';
        if ( $result = mysqli_query( $mysqliConnection, $sql ) ) {

            $output =  mysqli_fetch_all( $result, MYSQLI_NUM );
            mysqli_next_result( $mysqliConnection );
            mysqli_store_result( $mysqliConnection );
            mysqli_free_result( $result );

            if ( is_array( $output ) ) {
                return $output[0][0];

            }

        }

        return false;
    }

}
if( ! function_exists( 'database_api_set_solution' ) ) {

    /**
     * @param mysqli $mysqliConnection
     * @param int $symptomId
     * @param string $solutionDescription
     * @param string $solutionDescriptionNormalize
     * @return bool|int
     */
    function database_api_set_solution( $mysqliConnection, $symptomId = 0, $solutionDescription = '', $solutionDescriptionNormalize = '' ) {

        $symptomId = intval( $symptomId );
        $solutionDescription = mysqli_real_escape_string( $mysqliConnection, $solutionDescription );
        $solutionDescriptionNormalize = mysqli_real_escape_string( $mysqliConnection, $solutionDescriptionNormalize );
        $sql = 'SELECT `api_set_solution`("' . $symptomId . '", "' . $solutionDescription . '", "' . $solutionDescriptionNormalize . '") AS `api_set_solution`;';

        if ( $result = mysqli_query( $mysqliConnection, $sql ) ) {

            $output =  mysqli_fetch_row( $result );
            mysqli_next_result( $mysqliConnection );
            mysqli_store_result( $mysqliConnection );
            mysqli_free_result( $result );

            if ( is_array( $output ) ) {

                return $output[0];

            }

        }

        return false;
    }

}
if( ! function_exists( 'database_api_find_solutions' ) ) {

    /**
     * @param mysqli $mysqliConnection
     * @param string $textToFindNormalize
     * @return bool|array
     */
    function database_api_find_solutions( $mysqliConnection, $textToFindNormalize = '' ) {

        $textToFindNormalize = mysqli_real_escape_string( $mysqliConnection, $textToFindNormalize );
        $sql = 'CALL `api_find_solutions`("' . $textToFindNormalize . '");';
        if ( $result = mysqli_query( $mysqliConnection, $sql ) ) {

            $output =  mysqli_fetch_all( $result, MYSQLI_ASSOC );
            mysqli_next_result( $mysqliConnection );
            mysqli_store_result( $mysqliConnection );
            mysqli_free_result( $result );

            return $output;

        }

        return false;
    }

}