if( typeof window.fetch === 'function' ) {
    document.querySelector( '.search-form' ).addEventListener(
        'submit',
        async ( event ) => {

            event.preventDefault();

            let banner = document.querySelector( '.banner' );
            let bannerSmallClass = 'banner--small';
            let results = document.querySelector( '.results' );
            let resultsWithSolutionsClass = 'results--with-solutions';
            let resultsList = document.querySelector( '.results__list' );
            let symptom = document.querySelector( '.search-form__text-input--symptom' ).value;
            console.log(symptom.length);
            if( symptom.length === 0 ) {

                banner.classList.remove(bannerSmallClass);
                results.classList.remove(resultsWithSolutionsClass);

            }
            else {

                let url = document.location.href + 'api.php';
                let formBody = JSON.stringify( { symptom: symptom } );

                let rawResponse = await fetch( url, {

                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'text/html' },
                    body: formBody

                });

                let response = await rawResponse.text();

                if( response.length > 0 ) {
                    resultsList.innerHTML = response;
                }
                else {
                    resultsList.innerHTML = '<div class="results__error">Не удалось получить результаты поиска</div>';
                }

                banner.classList.add(bannerSmallClass);
                results.classList.add(resultsWithSolutionsClass);

            }
        }
    );
}