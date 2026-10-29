<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Deterministic fixture data for the e2e suite (topic: running).
 *
 * The public site renders with an empty database (every home section is
 * null-guarded), but the category and article routes need real rows, and the
 * admin flows need a login whose password can be declared as a credential
 * (static credential passwords must be at least 6 code points, so the
 * DatabaseSeeder's 5-character 'admin' password cannot be declared).
 *
 * Idempotent: `php artisan db:seed --class=E2ESeeder --force`.
 */
class E2ESeeder extends Seeder
{
    private const PASSWORD = 'e2e-pass-1';

    public function run(): void
    {
        $this->seedUsers();
        $catIds = $this->seedCategories();
        $this->seedArticles($catIds);
    }

    private function seedUsers(): void
    {
        User::updateOrCreate(
            ['username' => 'e2e.admin'],
            [
                'name' => 'E2E Admin',
                'email' => 'e2e.admin@vishwamitra.in',
                'password_hash' => Hash::make(self::PASSWORD),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    /** @return array<string,int> slug => id */
    private function seedCategories(): array
    {
        $cats = [
            'latest-news' => ['ताज़ा खबरें', 1],
            'politics' => ['राजनीति', 2],
            'business-and-markets' => ['व्यापार एवं बाज़ार', 3],
            'sports' => ['खेल', 4],
            'entertainment' => ['मनोरंजन', 5],
            'horoscope' => ['राशिफल', 6],
        ];

        $ids = [];
        foreach ($cats as $slug => [$name, $order]) {
            $cat = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name_hi' => $name,
                    'description' => $name.' — e2e fixture',
                    'accent_color' => 'bg-primary',
                    'pills' => 'मुख्य,विश्लेषण',
                    'sort_order' => $order,
                    'show_in_nav' => true,
                    'show_in_footer' => true,
                    'status' => 'active',
                ]
            );
            $ids[$slug] = $cat->id;
        }

        return $ids;
    }

    /** @param array<string,int> $catIds */
    private function seedArticles(array $catIds): void
    {
        $now = Carbon::now();

        $articles = [
            [
                'slug' => 'e2e-hero-ganga-cleanup',
                'category' => 'latest-news',
                'title' => 'गंगा सफाई अभियान में रिकॉर्ड स्तर पर कुदरत उपाय',
                'excerpt' => 'राष्ट्रीय मिशन के तहत इस वर्ष सबसे बड़ी संख्या में नए उपाय शुरू किए गए हैं।',
                'body' => '<p>गंगा सफाई अभियान के तहत इस वर्ष अब तक के सबसे बड़ी कुदरत उपाय कार्यक्रम शुरू किए गए हैं। विशेषज्ञों का कहना है कि यह दीर्घकालिक लक्ष्य की ओर महत्वपूर्ण कदम है।</p><p>पर्यावेक्षण बोर्ड ने साप्ताहिक समीक्षा की स्थिति जाँची और अगले चरण की योजना प्रस्तुत की।</p>',
                // The only fixture row that carries a real upload, so thumbnails
                // are visible on every seeded screen (hero, article page, feed).
                'image' => 'uploads/news/2026/10/28-34fed13e36ed.jpg',
                'home_slot' => 'hero',
                'home_order' => 1,
                'cat_slot' => 'lead',
                'cat_order' => 1,
                'key_points' => ['अभियान के तहत नए उपाय शुरू', 'साप्ताहिक समीक्षा की व्यवस्था'],
            ],
            [
                'slug' => 'e2e-politics-session-begins',
                'category' => 'politics',
                'title' => 'मानसून सत्र की शुरुआत, विपक्ष ने उठाए मुद्दे',
                'excerpt' => 'सत्र में महंगाई और रोज़गार सबसे बड़े मुद्दे बने।',
                'body' => '<p>मानसून सत्र की पहली बैठक में विपक्ष ने महंगाई और रोज़गार पर सरकार से जवाब तलब किया। सरकार ने विधेयकों की संख्या बढ़ाने का ब्यौरा दिया।</p>',
                'home_slot' => 'pol_lead',
                'home_order' => 1,
            ],
            [
                'slug' => 'e2e-market-sensex-high',
                'category' => 'business-and-markets',
                'title' => 'Sensex में उछाल, निवेशकों के जेब में आया बड़ा फायदा',
                'excerpt' => 'बैंकिंग और IT शेयर्स में तेजी ने बाज़ार को रिकॉर्ड ऊंचाई पर पहुंचा दिया।',
                'body' => '<p>बैंकिंग और आईटी शेयर्स में आई तेजी के बीच सेंसेक्ष ने नया रिकॉर्ड बनाया। विश्लेषकों ने चेतावनी दी कि लाभ की बुकिंग भी देखी जा सकती है।</p>',
                'home_slot' => 'market',
                'home_order' => 1,
            ],
            [
                'slug' => 'e2e-sports-series-win',
                'category' => 'sports',
                'title' => 'भारत ने जीता टेस्ट सीरीज़, अंतिम दिन में 6 विकेट',
                'excerpt' => 'अंतिम दिन के खेल में गेंदबाजों ने कमाल कर दिखाया।',
                'body' => '<p>अंतिम दिन के खेल में भारतीय गेंदबाज़ों ने शानदार प्रदर्शन करते हुए 6 विकेट से सीरीज़ जीती। कप्तान ने खेल के बाद प्रेसवारा में टीम के अनुभव की वजह बताई।</p>',
                'home_slot' => 'sport_lead',
                'home_order' => 1,
            ],
            [
                'slug' => 'e2e-ent-film-release',
                'category' => 'entertainment',
                'title' => 'नई फिल्म ने जमाया पहला दिन, बॉक्स ऑफिस पर बनी कमाई',
                'excerpt' => 'पहले दिन की कमाई ने अपनी उम्मीदें पूरी कर दिखाईं।',
                'body' => '<p>बड़ी स्क्रीन पर आई नई फिल्म ने पहले दिन से ही दर्शकों का दिल जीत लिया। निर्माताओं का कहना है कि अगले हफ्ते और शोर बढ़ाने की योजना है।</p>',
                'home_slot' => 'ent',
                'home_order' => 1,
            ],
            [
                'slug' => 'e2e-latest-civic-update',
                'category' => 'latest-news',
                'title' => 'शहर में नई बस सेवाएं, यात्रियों को मिलेगी राहत',
                'excerpt' => 'नए मार्गों पर चलेंगी इलेक्ट्रिक बसें, किराया वही रहेगा।',
                'body' => '<p>परिवहन निगम ने नए मार्गों पर इलेक्ट्रिक बस सेवाएं शुरू की हैं। अधिकारियों का कहना है कि किराया में कोई बढ़ोतरी नहीं की गई है।</p>',
                'cat_slot' => 'grid',
                'cat_order' => 1,
            ],
            [
                'slug' => 'e2e-latest-weather-alert',
                'category' => 'latest-news',
                'title' => 'मानसून की गतिविधियों में उछाल, कई जिलों में येलो अलर्ट',
                'excerpt' => 'मौसम विभाग ने अगले तीन दिन की भारी वर्षा की संभावना जताई है।',
                'body' => '<p>मौसम विभाग ने कई जिलों में येलो अलर्ट जारी किया है। प्रशासन ने निचले इलाकों के निवासियों को सतर्क कर दिया है।</p>',
                'cat_slot' => 'grid',
                'cat_order' => 2,
            ],
            [
                'slug' => 'e2e-politics-oppn-meet',
                'category' => 'politics',
                'title' => 'विपक्ष की बैठक, साझा रणनीति पर होगी चर्च',
                'excerpt' => 'आगामी चुनाव को लेकर सभी दलों ने एकजुटता दिखाने की कोशिश की।',
                'body' => '<p>विपक्षी दलों की बैठक में आगामी चुनाव के लिए साझा रणनीति पर बहस हुई। अधिवक्ताओं ने बाद में संक्षिप्त जानकारी दी।</p>',
                'cat_slot' => 'lead',
                'cat_order' => 1,
            ],
        ];

        foreach ($articles as $a) {
            $article = Article::updateOrCreate(
                ['slug' => $a['slug']],
                [
                    'category_id' => $catIds[$a['category']],
                    'title' => $a['title'],
                    'excerpt' => $a['excerpt'],
                    'body' => $a['body'],
                    'image' => $a['image'] ?? null,
                    'image_alt' => $a['title'],
                    'author_name' => 'विशेष संवाददाता',
                    'location' => 'नई दिल्ली',
                    'badge_label' => null,
                    'badge_color' => 'bg-primary',
                    'is_breaking' => false,
                    'is_featured' => isset($a['home_slot']) && $a['home_slot'] === 'hero',
                    'is_opinion' => false,
                    'is_visual' => false,
                    'home_slot' => $a['home_slot'] ?? null,
                    'home_order' => $a['home_order'] ?? 0,
                    'cat_slot' => $a['cat_slot'] ?? null,
                    'cat_order' => $a['cat_order'] ?? 0,
                    'read_minutes' => 3,
                    'views' => 0,
                    'comment_count' => 0,
                    'status' => 'published',
                    'published_at' => $now->copy()->subMinutes(30 + random_int(0, 600)),
                    'meta_title' => $a['title'],
                    'meta_desc' => $a['excerpt'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            foreach ($a['key_points'] ?? [] as $i => $point) {
                DB::table('key_points')->updateOrInsert(
                    ['article_id' => $article->id, 'point' => $point],
                    ['sort_order' => $i + 1]
                );
            }
        }
    }
}
