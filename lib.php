<?php
/**
 * Created by PhpStorm.
 * User: mihaildukin
 * Date: 2020-01-13
 * Time: 06:19
 */

if( ! function_exists( 'morphy_init' ) ) {

    function morphy_init( $language = 'en' ) {

        return new cijic\phpMorphy\Morphy( $language );

    }

}
if( ! function_exists( 'string_to_normal_string' ) ) {

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

    function database_init( $config = [] ) {

        $mysqli = mysqli_connect( $config['DBHost'], $config['DBUser'], $config['DBPassword'], $config['DBName'] );

        if( mysqli_connect_errno() ) {

            return false;

        }
        else{

            return $mysqli;

        }

    }

}