<?php
/**
 * Created by PhpStorm.
 * User: mihaildukin
 * Date: 2020-01-06
 * Time: 20:39
 */

$category = 'windows';
$page = intval( $_GET[ 'page' ] );

if( empty( $page ) ) {

    exit;

}

$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, 'https://answers.microsoft.com/en-us/forum/forumthreadlist?forumId=cacb25ef-5e2a-e011-8a67-d8d385dcbb12&sort=LastReplyDate&dir=Desc&tab=All&meta=&status=all&mod=&modAge=&advFil=&postedAfter=&postedBefore=&page=' . $page . '&threadType=All&tm=1578914091685' );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
curl_setopt( $ch, CURLOPT_ENCODING, 'gzip, deflate' );

$headers = array();
$headers[] = 'Authority: answers.microsoft.com';
$headers[] = 'Pragma: no-cache';
$headers[] = 'Cache-Control: no-cache';
$headers[] = 'Accept: text/html, */*; q=0.01';
$headers[] = 'X-Requested-With: XMLHttpRequest';
$headers[] = 'Ms-Cv: FvVokUaxJZ/QnvHr2gB7VY.1';
$headers[] = 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) snap Chromium/79.0.3945.79 Chrome/79.0.3945.79 Safari/537.36';
$headers[] = 'Sec-Fetch-Site: same-origin';
$headers[] = 'Sec-Fetch-Mode: cors';
$headers[] = 'Accept-Encoding: gzip, deflate, br';
$headers[] = 'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7';

curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

$chResultLinks= curl_exec( $ch );

if ( curl_errno( $ch ) ) {
    
    echo 'Error:' . curl_error( $ch) ;
    
}
$chResultLinks= '<!DOCTYPE html><html><head></head><body>' . $chResultLinks. '</body></html>';

$linkListDomDocument = new DOMDocument();
$linkListDomDocument->loadHTML( $chResultLinks);

$classname = "c-hyperlink";
$id = "thread-link";
$finder = new DomXPath($linkListDomDocument);
$links = $finder->query("//a[contains(@class, '$classname')][contains(@data-bi-id, '$id')]");

$filePath = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $category . '_page_' . $page . '.json';
$output = [ $category => [ ] ];

foreach ( $links as $link ) {

    $link = $link->getAttribute( 'href' );

    curl_setopt( $ch, CURLOPT_URL, $link );
    $chResultLink = curl_exec( $ch );

    if ( curl_errno( $ch ) ) {

        echo 'Error:' . curl_error( $ch );

    }
    else {

        $linkDomDocument = new DOMDocument();
        $linkDomDocument->loadHTML(str_replace("\r\n",'',$chResultLink));

        $arrayOfH1 = $linkDomDocument->getElementsByTagName('h1');

        if( count( $arrayOfH1 ) === 1 ) {

            $h1 = $arrayOfH1[ 0 ]->textContent;
            $finder = new DomXPath( $linkDomDocument );
            $solutions = $finder->query( "//div[contains(@class, 'thread-full-message')]" );
            $solutionsArray = [];

            foreach ( $solutions as $solution ) {

                $solutionsArray[] = trim( $solution->textContent );

            }
            if( is_array( $solutionsArray ) ) {

                $h1Part2 = array_shift( $solutionsArray );
                $h1 = $h1 . $h1Part2;

            }
            $output[$category][] = [
                'symptom' => trim( $h1 ),
                'solutions' => $solutionsArray
            ];

        }

    }

}

if( ! empty( $output ) ) {

    $output = json_encode( $output );
    file_put_contents( $filePath, $output );
    $page++;

    echo '<meta http-equiv="refresh" content="0;URL=http://' . $_SERVER[ 'HTTP_HOST' ] . '?page=' . $page . '">';
    # header('Location: ' . constant('MODX_SITE_URL') . '?page=' . $page);

}