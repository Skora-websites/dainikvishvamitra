<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BulletinController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\EpaperController;
use App\Http\Controllers\Admin\HoroscopeController;
use App\Http\Controllers\Admin\MarketController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\TickerController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ArticleController as PublicArticleController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController as PublicCategoryController;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\EpaperController as PublicEpaperController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HoroscopePageController;
use App\Http\Controllers\HubController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PageController as PublicPageController;
use App\Http\Controllers\PollVoteController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\VideoPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ---- Public site ----
Route::get('/', HomeController::class)->name('home');

// Display-language switch (hi/en), persisted in session; query string on the
// return URL is preserved by back().
Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->whereIn('locale', ['hi', 'en'])
    ->name('language.switch');

Route::get('/category/{slug}', PublicCategoryController::class)->name('category');
Route::get('/article/{slug}', PublicArticleController::class)->name('article');
Route::get('/author/{name}', AuthorController::class)->name('author');
Route::get('/archive', ArchiveController::class)->name('archive');
Route::get('/search', SearchController::class)->name('search');
Route::get('/page/{slug}', PublicPageController::class)->name('page');
Route::get('/video', VideoPageController::class)->name('video');
Route::get('/horoscope', HoroscopePageController::class)->name('horoscope');
Route::get('/epaper', [PublicEpaperController::class, 'index'])->name('epaper');
Route::get('/epaper/{id}', [PublicEpaperController::class, 'show'])->whereNumber('id')->name('epaper.show');

Route::get('/breaking', [HubController::class, 'breaking'])->name('breaking');
Route::get('/opinion', [HubController::class, 'opinion'])->name('opinion');

Route::get('/contact', [ContactFormController::class, 'show'])->name('contact');
Route::post('/contact', [ContactFormController::class, 'store'])->middleware('throttle:10,1')->name('contact.store');

Route::get('/subscribe', [SubscribeController::class, 'show'])->name('subscribe.show');
Route::post('/subscribe', [SubscribeController::class, 'store'])->middleware('throttle:10,1')->name('subscribe');
Route::post('/poll-vote', PollVoteController::class)->middleware('throttle:30,1')->name('poll-vote');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/rss', RssController::class)->name('rss');
Route::get('/feed', RssController::class)->name('rss.feed');

// ---- Admin ----
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest-only auth
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:8,1')->name('login.attempt');
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Authenticated area
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        // Articles
        Route::get('articles', [ArticleController::class, 'index'])->name('articles');
        // Fixed paths must be registered before the {id} routes, otherwise
        // Laravel matches "create"/"bulk-delete" as the {id} parameter.
        Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::get('articles/{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::post('articles/bulk-delete', [ArticleController::class, 'bulkDestroy'])->name('articles.delete_bulk');
        Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::post('articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
        Route::post('articles/{id}/delete', [ArticleController::class, 'destroy'])->name('articles.delete');

        // Uploads (rich-text editor image uploads + storage media gallery)
        Route::get('upload/images', [UploadController::class, 'index'])->name('uploads.index');
        Route::post('upload/image', [UploadController::class, 'image'])->name('uploads.image');

        // Content modules
        Route::controller(CategoryController::class)->group(function () {
            Route::get('categories', 'index')->name('categories');
            Route::post('categories', 'store')->name('categories.store');
            Route::post('categories/{id}', 'update')->name('categories.update');
            Route::post('categories/{id}/delete', 'destroy')->name('categories.delete');
        });

        Route::controller(TickerController::class)->group(function () {
            Route::get('ticker', 'index')->name('ticker');
            Route::post('ticker', 'store')->name('ticker.store');
            Route::post('ticker/{id}', 'update')->name('ticker.update');
            Route::post('ticker/{id}/delete', 'destroy')->name('ticker.delete');
        });

        Route::controller(BulletinController::class)->group(function () {
            Route::get('bulletins', 'index')->name('bulletins');
            Route::post('bulletins', 'store')->name('bulletins.store');
            Route::post('bulletins/{id}', 'update')->name('bulletins.update');
            Route::post('bulletins/{id}/delete', 'destroy')->name('bulletins.delete');
        });

        Route::controller(MarketController::class)->group(function () {
            Route::get('market', 'index')->name('market');
            Route::post('market', 'store')->name('market.store');
            Route::post('market/{id}', 'update')->name('market.update');
            Route::post('market/{id}/delete', 'destroy')->name('market.delete');
        });

        Route::controller(VideoController::class)->group(function () {
            Route::get('videos', 'index')->name('videos');
            Route::post('videos', 'store')->name('videos.store');
            Route::post('videos/{id}', 'update')->name('videos.update');
            Route::post('videos/{id}/delete', 'destroy')->name('videos.delete');
        });

        Route::controller(ContactController::class)->group(function () {
            Route::get('messages', 'index')->name('messages');
            Route::post('messages/{id}/read', 'markRead')->name('messages.read');
            Route::post('messages/read-all', 'markAllRead')->name('messages.read_all');
            Route::post('messages/{id}/delete', 'destroy')->name('messages.delete');
        });

        Route::controller(HoroscopeController::class)->group(function () {
            Route::get('horoscope', 'index')->name('horoscope');
            Route::post('horoscope/{id}', 'update')->name('horoscope.update');
        });

        Route::controller(PollController::class)->group(function () {
            Route::get('polls', 'index')->name('polls');
            Route::post('polls', 'store')->name('polls.store');
            // Fixed-path routes MUST be registered before {id} — otherwise
            // Laravel matches "options" as the {id} parameter.
            Route::post('polls/options', 'addOption')->name('polls.add_option');
            Route::post('polls/options/{id}/delete', 'deleteOption')->name('polls.delete_option');
            Route::post('polls/{id}', 'update')->name('polls.update');
            Route::post('polls/{id}/delete', 'destroy')->name('polls.delete');
            Route::post('polls/{id}/reset', 'resetVotes')->name('polls.reset');
        });

        Route::controller(EpaperController::class)->group(function () {
            Route::get('epapers', 'index')->name('epapers');
            Route::post('epapers', 'store')->name('papers.store');
            Route::post('epapers/{id}', 'update')->name('papers.update');
            Route::post('epapers/{id}/delete', 'destroy')->name('papers.delete');
        });

        Route::controller(AdminPageController::class)->group(function () {
            Route::get('pages', 'index')->name('pages');
            Route::post('pages', 'store')->name('pages.store');
            Route::post('pages/{slug}', 'update')->name('pages.update');
            Route::post('pages/{slug}/delete', 'destroy')->name('pages.delete');
        });

        Route::controller(ElectionController::class)->group(function () {
            Route::get('elections', 'index')->name('elections');
            Route::post('elections', 'store')->name('elections.store');
            Route::post('elections/{id}', 'update')->name('elections.update');
            Route::post('elections/{id}/delete', 'destroy')->name('elections.delete');
        });

        Route::controller(SubscriberController::class)->group(function () {
            Route::get('subscribers', 'index')->name('subscribers');
            Route::get('subscribers/export', 'exportCsv')->name('subscribers.export');
            Route::post('subscribers/{id}/toggle', 'toggle')->name('subscribers.toggle');
            Route::post('subscribers/{id}/delete', 'destroy')->name('subscribers.delete');
        });

        // Users are admin-only
        Route::middleware('admin.role:admin')->group(function () {
            Route::get('settings', [SettingController::class, 'index'])->name('settings');
            Route::post('settings', [SettingController::class, 'save'])->name('settings.save');

            Route::controller(UserController::class)->group(function () {
                Route::get('users', 'index')->name('users');
                Route::post('users', 'store')->name('users.store');
                Route::post('users/{id}', 'update')->name('users.update');
                Route::post('users/{id}/delete', 'destroy')->name('users.delete');
            });
        });
    });
});
