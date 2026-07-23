const CACHE_NAME = 'oat-v5';

const OFFLINE_URL = '/_offline';

const OFFLINE_PAGE = `
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>OAT - Offline</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
    background:linear-gradient(135deg,#f8fafc,#eff6ff,#eef2ff);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}
.card{
    background:#fff;
    border-radius:16px;
    padding:40px;
    max-width:420px;
    text-align:center;
    box-shadow:0 5px 25px rgba(0,0,0,.08);
}
.icon{
    font-size:42px;
    margin-bottom:15px;
}
h1{
    color:#1e293b;
    margin-bottom:10px;
}
p{
    color:#64748b;
    line-height:1.6;
    margin-bottom:25px;
}
button{
    background:#2563eb;
    color:#fff;
    border:none;
    border-radius:10px;
    padding:12px 24px;
    cursor:pointer;
    font-size:15px;
}
button:hover{
    background:#1d4ed8;
}
</style>
</head>
<body>
<div class="card">
<div class="icon">📡</div>
<h1>You're Offline</h1>
<p>
Please check your internet connection.
Your pending accomplishments are safely stored and will sync when you're back online.
</p>
<button onclick="location.reload()">Try Again</button>
</div>
</body>
</html>
`;

self.addEventListener('install', event => {

    event.waitUntil(

        caches.open(CACHE_NAME).then(cache => {

            return cache.put(
                new Request(OFFLINE_URL),
                new Response(OFFLINE_PAGE,{
                    headers:{
                        'Content-Type':'text/html'
                    }
                })
            );

        })

    );

    self.skipWaiting();

});

self.addEventListener('activate', event => {

    event.waitUntil(

        caches.keys().then(keys=>{

            return Promise.all(

                keys
                    .filter(key=>key!==CACHE_NAME)
                    .map(key=>caches.delete(key))

            );

        })

    );

    self.clients.claim();

});

self.addEventListener('fetch', event => {

    const request = event.request;

    if(request.method !== 'GET'){
        return;
    }

    const url = new URL(request.url);

    // Ignore unsupported protocols
    if(
        url.protocol !== 'http:' &&
        url.protocol !== 'https:'
    ){
        return;
    }

    // -------------------------
    // HTML pages
    // -------------------------

    if(request.mode === 'navigate'){

        event.respondWith(

            fetch(request)

                .then(response=>{

                    if(response.ok){

                        const clone = response.clone();

                        caches.open(CACHE_NAME)
                            .then(cache=>cache.put(request,clone));

                    }

                    return response;

                })

                .catch(()=>{

                    return caches.match(request)

                        .then(cached=>{

                            return cached ||
                                caches.match(OFFLINE_URL);

                        });

                })

        );

        return;

    }

    // -------------------------
    // Local assets
    // -------------------------

    if(url.origin === location.origin){

        event.respondWith(

            caches.match(request)

                .then(cached=>{

                    if(cached){
                        return cached;
                    }

                    return fetch(request)

                        .then(response=>{

                            if(response.ok){

                                const clone = response.clone();

                                caches.open(CACHE_NAME)
                                    .then(cache=>cache.put(request,clone));

                            }

                            return response;

                        });

                })

        );

        return;

    }

    // -------------------------
    // External assets (CDNs)
    // -------------------------

    event.respondWith(

        caches.match(request)

            .then(cached=>{

                if(cached){
                    return cached;
                }

                return fetch(request)

                    .then(response=>{

                        if(
                            response.ok ||
                            response.type === 'opaque'
                        ){

                            const clone = response.clone();

                            caches.open(CACHE_NAME)
                                .then(cache=>cache.put(request,clone));

                        }

                        return response;

                    });

            })

    );

});
