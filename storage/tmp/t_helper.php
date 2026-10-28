<?php
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Support\Site;
var_dump(Site::hiDate('2026-10-13'));
var_dump(Site::hiDigits('40 min'));
var_dump(Site::slugify('सीईसी इस्तीफा: मांग पर प्रदर्शन!'));
var_dump(Site::timeAgo('-2 hours'));
var_dump(Site::setting('contact_email'));
var_dump(Site::excerpt('<p>नई दिल्ली। मुख्य चुनाव आयुक्त ज्ञानेश कुमार के इस्तीफे की मांग</p>', 30));
$p = Site::pagination(37, 6, 3);
echo "pages={$p['last_page']} range={$p['start']}-{$p['end']}\n";
