const CACHE = "test-cache";
const TIMEOUT = 10000;

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches
            .open( CACHE )
            .then(
                (cache) => cache.addAll(
                    [
                        '/',
                        '/offline.html',
                    ]
                )
            )
            .then(
                () => self.skipWaiting()
            )
    );
});

self.addEventListener( 'activate', ( event ) => {
    event.waitUntil( self.clients.claim() );
});

self.addEventListener('fetch', ( event ) => {
    event.respondWith(
        fromNetwork( event.request, TIMEOUT )
        .catch(
            () => {
                return fromCache( event.request );
            }
        )
    );
});


function useFallback() {
    return fromCache( '/offline' );
}

function fromNetwork( request ) {
    return new Promise(( fulfill, reject ) => {
        let timeoutId = setTimeout( reject, TIMEOUT );
        fetch( request )
            .then(
                ( response ) => {
                    clearTimeout( timeoutId );
                    fulfill( response );
                },
                reject
            );
    });
}


function fromCache( request ) {
    return caches
            .open( CACHE )
            .then(
                ( cache ) => cache.match( request ).then( ( matching ) => matching || useFallback() )
            );
}