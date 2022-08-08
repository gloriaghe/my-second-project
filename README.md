Progetto Laravel con login
Inizializzazione

    Creare la cartella del progetto
    Entrare dal terminale nella cartella
    composer create-project --prefer-dist laravel/laravel:^7.0 .
    Solo per Laravel <= 7 rimuovere fzaninotto/faker perchè deprecato e installare la nuova libreria per generare dati fake: composer remove fzaninotto/faker composer require fakerphp/faker
    Se volete usare la laravel-debugbar: composer require barryvdh/laravel-debugbar --dev
    Installare la libreria di scaffolding: composer require laravel/ui:^2.4
    Scegliere lo scaffolding desiderato (nel nostro caso vue con autenticazione): php artisan ui vue --auth
    (installare eventuali altre librerie per altre cose come: gestione ruoli, generazione slug)
    Su package.json modificare:
        "bootstrap": "^4.0.0", in "bootstrap": "^5.1.3", (o comunque la versione che si vuole usare)
        "popper.js": "^1.12", in "@popperjs/core": "^2.11.5",
        e eliminare: "jquery": "^3.2"

    Su resorces/js/bootstrap.js commentare (perchè non servono per bootstrap 5):
        window.Popper = require('popper.js').default;
        window.$ = window.jQuery = require('jquery');
    Aggiornare il file webpack.mix.js affinchè produca file js diversi per il front office (con Vue) e il backoffice (con blade):

    mix.js('resources/js/front.js', 'public/js')
        .js('resources/js/back.js', 'public/js')
        .sass('resources/sass/back.scss', 'public/css')
        .options({
            processCssUrls: false
        });

    nella cartella resources/js/ creare i file front.js e back.js copiando il file app.js già presente
    nella cartella resources/css/ rinominare il file app.scss in back.scss
    Installare le librerie js: npm install
    Creare la cartella Models (con la maiuscola) e metterci dentro il file del model User.php
    Modificare in User.php il namespace: da namespace App; a namespace App\Models; (ricordatevi che il namespace di tutto ciò che sta nella cartella app deve coincidere con la struttura delle cartelle)
    Per l'intero progetto i model li costruiremo con: php artisan make:model Models/<NomeDelModel>
    fare una ricerca nella cartella del progetto di App\User e sostituirlo con App\Models\User (non modificare però i file che si trovano nella cartella vendor)
    Spostare e rinominare il file app/Http/Controllers/HomeController.php in app/Http/Controllers/Admin/AdminController.php
    Nel file appena spostato:
        modificare il namespace a namespace App\Http\Controllers\Admin;
        aggiungere la riga di codice (se non c'è già) use App\Http\Controllers\Controller;
    fare una ricerca nella cartella del progetto di App\Http\Controllers\HomeController e sostituirlo con App\Http\Controllers\Admin\AdminController (non modificare però i file che si trovano nella cartella vendor)
    Per l'intero progetto i controllers li costruiremo con: php artisan make:controller <NomeCartella>/<NomeDelController> --resource --model=<NomeDelModel>
    Rigenerare la classmap: composer dump-autoload
    Nel file app/Providers/RouteServiceProvider.php modificare:
        public const HOME = '/home'; in public const HOME = '/admin'; (oppure quello che avete messo voi)
    Se serve modificare il file app/Http/Middleware/Authenticate.php:
        return route('login'); con la route che volete voi

Routes

    Nel file routes/web.php creare le rotte necessarie raggruppando tutte quelle dedicate al backoffice sotto il termine admin. Esempio:

    Route::get('/', function () {
        return view('guests.home');
    })->name('home');

    Auth::routes();

    Route::middleware('auth')
    ->namespace('Admin')
    ->name('admin.')
    ->prefix('admin')
    ->group(function () {
        Route::get('/', 'AdminController@dashboard')->name('dashboard');
        Route::resource('posts', 'PostController');
    });

Database

    Creare il database da phpMyAdmin oppure da linea di comando o come volete
    Nel file .env aggiornare i dati del database (quelli che iniziano con DB_) e giacchè anche APP_NAME col nome della vostra app
    Aggiornare i file delle migrations: php artisan migrate
    Aggiornare il file DatabaseSeeder.php aggiungendo $this->call(ModelSeeder::class); per ogni model di cui abbiamo il seeder
    Aggiornare i file dei seeders
        agiungere use Faker\Generator as Faker;
        modificare public function run() a public function run(Faker $faker)
    (slugs cercate nei file del progetto per dettagli)
    Nel model impostare la proprietà $fillable con le colonne che possono essere "mass assigned"

Views

    Organizzare la cartella resources/views con:
        una sottocartella admin (con le sottocartelle per ciascun model risorsa)
        una sottocartella guests
    spostare home.blade.php dentro admin e rinominarlo in dashboard.blade.php o comunquer rinominare i file con nomi chiari
    aggiornare i vecchi nomi dei template blade ovunque erano stai usati (controllers, web.php, altri template blade ...)

Parte Vue

    Nel webpack.mix.js aggiungere mix.js('resources/js/front.js', 'public/js')
    Creare il file front.js in resources/js
    Avviare (o riavviare) npm run watch
    Nel file front.js scrivere il codice per connettere Vue alla pagina:

    require('./bootstrap');

    window.Vue = require('vue'); // importiamo la libreria Vue
    import App from './components/App.vue'; // importiamo il componente base App.vue e lo assegniamo alla variabile App

    // inizializziamo l'applicazione Vue passandogli l'oggetto di inizializzazione
    const app = new Vue({
        el: '#root', // id del componente nel file HTML dentro il quale opererà Vue
        render: h => h(App) // monta il componente App nell'elemento root
    });

    Creare o modificare il template blade che gestirà la pagina (guest/home.blade.php)
    Svuotare il body e mettere l'elemento vuoto root per Vue e l'importazione del file front.js:

    <body>
        <div id="root"></div>

        <script src="{{ asset('js/front.js') }}"></script>
    </body>
    });

    Verificare che in routes/web.php abbiamo la rotta '/' che punta a 'guest.home' (parte che potremmo dover modificare)
    Svuotare la cartella resources/js/components se esiste oppure crearla
    Creare il componente App.vue nella stessa cartella (resources/js/components)
    Ed ora scrivere tutto il front office

Laravel API

    ...

Usare il router di Vue

    installarlo: npm install vue-router@3 --save-dev
    in resources/js/front.js collegare vue-router con vue:

        require('./bootstrap');

        import Vue from 'vue';
        import VueRouter from 'vue-router'; // importiamo la libreria vue-router
        import App from './App.vue';

        // importiamo tutti i componenti delle pagine
        // ...
        // ...
        // ...


        // definiamo le rotte
        const routes = [
            {
                path: 'percorso url',
                name: 'nome rotta',
                component: NomeComponente,
            },
            // definite anche gli altri
            {
                path: '*',
                name: 'page404',
                component: Page404,
            }
        ]

        // costruiamo il nostro router
        const router = new VueRouter({
            routes,
            mode: 'history',
        });


        Vue.use(VueRouter); // diciamo a Vue di usare il plugin vue-router

        const app = new Vue({
            el: '#root',
            render: h => h(App),
            router, // diciamo a vue di inizializzare la nostra app usando il router che abbiamo costruito
        });

    Creiamo la cartella pages con tutte le pagine
    Creiamo i componenti vue per ciascuna pagina
    Usiamo il router:

        <router-view></router-view>

    e

        <router-link :to="{name: 'nome route''">Contentuto</router-link>
