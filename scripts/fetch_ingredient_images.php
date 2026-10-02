<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

const UPLOAD_DIR = __DIR__ . '/../public/ingredients';
const USER_AGENT = 'DapurCerdas/1.0 (https://example.invalid; contact: dapur-cerdas@example.com)';

$MAP = [
    'Bayam' => [['en', 'Spinach']],
    'Telur Ayam' => [['en', 'Egg as food']],
    'Nasi Putih' => [['id', 'Nasi'], ['en', 'Cooked rice']],
    'Kangkung' => [['id', 'Kangkung']],
    'Wortel' => [['en', 'Carrot']],
    'Brokoli' => [['en', 'Broccoli']],
    'Labu Siam' => [['en', 'Chayote']],
    'Terong' => [['en', 'Eggplant']],
    'Sawi Hijau' => [['id', 'Sawi']],
    'Pakcoy' => [['en', 'Bok choy'], ['id', 'Pakcoy']],
    'Kol Putih' => [['en', 'Cabbage']],
    'Kacang Panjang' => [['en', 'Yardlong bean']],
    'Buncis' => [['en', 'Green bean']],
    'Tomat' => [['en', 'Tomato']],
    'Timun' => [['en', 'Cucumber']],
    'Daun Singkong' => [['en', 'Cassava'], ['id', 'Daun singkong']],
    'Daun Katuk' => [['id', 'Katuk']],
    'Daun Kelor' => [['id', 'Kelor'], ['en', 'Moringa oleifera']],
    'Labu Kuning' => [['en', 'Pumpkin']],
    'Selada' => [['en', 'Lettuce']],
    'Paprika Merah' => [['en', 'Bell pepper']],
    'Kale' => [['en', 'Kale']],
    'Kol Ungu' => [['en', 'Red cabbage']],
    'Pare' => [['en', 'Bitter melon'], ['id', 'Pare']],
    'Kemangi' => [['id', 'Kemangi'], ['en', 'Basil']],
    'Tahu Putih' => [['en', 'Tofu'], ['id', 'Tahu']],
    'Tempe' => [['id', 'Tempe'], ['en', 'Tempeh']],
    'Telur Bebek' => [['id', 'Telur pindang'], ['en', 'Duck egg']],
    'Telur Puyuh' => [['id', 'Telur puyuh'], ['en', 'Quail egg']],
    'Dada Ayam' => [['en', 'Chicken breast'], ['en', 'Chicken']],
    'Paha Ayam' => [['en', 'Chicken leg'], ['en', 'Chicken']],
    'Ayam Giling' => [['en', 'Ground meat']],
    'Hati Ayam' => [['en', 'Liver (food)'], ['id', 'Hati ayam']],
    'Daging Sapi' => [['en', 'Beef']],
    'Daging Sapi Giling' => [['en', 'Ground beef']],
    'Daging Kambing' => [['en', 'Lamb and mutton']],
    'Hati Sapi' => [['en', 'Liver (food)']],
    'Ikan Nila' => [['en', 'Nile tilapia'], ['id', 'Nila']],
    'Ikan Lele' => [['en', 'Catfish'], ['id', 'Lele']],
    'Ikan Patin' => [['en', 'Pangasius'], ['id', 'Patin']],
    'Ikan Kembung' => [['id', 'Kembung'], ['en', 'Indian mackerel']],
    'Ikan Tuna' => [['en', 'Thunnus'], ['id', 'Tuna']],
    'Ikan Salmon' => [['en', 'Salmon']],
    'Ikan Teri' => [['en', 'Anchovy']],
    'Ikan Bandeng' => [['id', 'Bandeng'], ['en', 'Milkfish']],
    'Ikan Gurame' => [['id', 'Gurame'], ['en', 'Gourami']],
    'Ikan Gabus' => [['id', 'Gabus'], ['en', 'Channa striata']],
    'Udang' => [['id', 'Udang'], ['en', 'Shrimp and prawn']],
    'Cumi-Cumi' => [['id', 'Cumi-cumi'], ['en', 'Squid']],
    'Kerang' => [['en', 'Clam']],
    'Sosis Ayam' => [['en', 'Sausage']],
    'Nugget Ayam' => [['en', 'Chicken nugget'], ['en', 'Chicken fingers']],
    'Bakso Sapi' => [['id', 'Bakso'], ['en', 'Meatball']],
    'Smoked Beef' => [['en', 'Pastrami']],
    'Nasi Merah' => [['en', 'Brown rice']],
    'Beras Putih' => [['en', 'Rice']],
    'Kentang' => [['en', 'Potato']],
    'Kentang Goreng' => [['en', 'French fries']],
    'Ubi Jalar' => [['en', 'Sweet potato']],
    'Singkong' => [['en', 'Cassava']],
    'Talas' => [['en', 'Taro'], ['id', 'Talas']],
    'Jagung Manis' => [['en', 'Sweet corn']],
    'Roti Tawar' => [['en', 'White bread']],
    'Roti Gandum' => [['en', 'Brown bread'], ['en', 'Whole wheat bread']],
    'Mie Basah' => [['en', 'Noodle'], ['id', 'Mie']],
    'Mie Instan' => [['en', 'Instant noodles'], ['id', 'Mie instan']],
    'Bihun' => [['id', 'Bihun'], ['en', 'Rice vermicelli']],
    'Oatmeal' => [['en', 'Oatmeal']],
    'Makaroni' => [['en', 'Macaroni']],
    'Sagu Mutiara' => [['en', 'Tapioca'], ['en', 'Tapioca pearls']],
    'Sereal Jagung' => [['en', 'Corn flakes']],
    'Pisang' => [['en', 'Banana']],
    'Apel' => [['en', 'Apple']],
    'Jeruk Manis' => [['en', 'Orange (fruit)']],
    'Mangga' => [['en', 'Mango']],
    'Pepaya' => [['en', 'Papaya']],
    'Semangka' => [['en', 'Watermelon']],
    'Melon' => [['en', 'Cantaloupe'], ['en', 'Honeydew']],
    'Anggur' => [['en', 'Grape']],
    'Alpukat' => [['en', 'Avocado'], ['id', 'Alpukat']],
    'Nanas' => [['en', 'Pineapple']],
    'Jambu Biji' => [['id', 'Jambu biji'], ['en', 'Guava']],
    'Rambutan' => [['en', 'Rambutan']],
    'Salak' => [['id', 'Salak'], ['en', 'Salak']],
    'Duku' => [['en', 'Lansium parasiticum'], ['id', 'Duku']],
    'Belimbing' => [['en', 'Star fruit'], ['id', 'Belimbing']],
    'Pir' => [['en', 'Pear']],
    'Stroberi' => [['en', 'Strawberry']],
    'Kiwi' => [['en', 'Kiwifruit']],
    'Kelapa Muda' => [['en', 'Coconut'], ['id', 'Kelapa muda']],
    'Kurma' => [['en', 'Date palm'], ['en', 'Phoenix dactylifera']],
    'Durian' => [['en', 'Durian']],
    'Bawang Merah' => [['id', 'Bawang merah'], ['en', 'Shallot']],
    'Bawang Putih' => [['id', 'Bawang putih'], ['en', 'Garlic bulb']],
    'Bawang Bombay' => [['en', 'Onion']],
    'Jahe' => [['en', 'Ginger']],
    'Kunyit' => [['en', 'Turmeric'], ['id', 'Kunyit']],
    'Lengkuas' => [['en', 'Galangal'], ['id', 'Lengkuas']],
    'Serai' => [['en', 'Lemongrass'], ['id', 'Serai']],
    'Daun Jeruk' => [['en', 'Kaffir lime'], ['id', 'Daun jeruk']],
    'Daun Salam' => [['en', 'Syzygium polyanthum'], ['en', 'Bay leaf']],
    'Daun Bawang' => [['en', 'Scallion'], ['id', 'Daun bawang']],
    'Seledri' => [['en', 'Celery']],
    'Cabe Merah Keriting' => [['en', 'Chili pepper'], ['en', 'Red pepper']],
    'Cabe Rawit' => [['en', 'Bird\'s eye chili'], ['en', 'Chili pepper']],
    'Jeruk Nipis' => [['en', 'Lime (fruit)'], ['en', 'Key lime']],
    'Kecap Manis' => [['id', 'Kecap manis'], ['id', 'Kecap']],
    'Kecap Asin' => [['en', 'Soy sauce'], ['id', 'Kecap']],
    'Garam' => [['en', 'Salt']],
    'Gula Pasir' => [['en', 'Sugar']],
    'Gula Merah' => [['en', 'Palm sugar'], ['en', 'Jaggery']],
    'Kaldu Bubuk' => [['en', 'Bouillon cube'], ['en', 'Stock cube']],
    'Terasi' => [['id', 'Terasi'], ['en', 'Shrimp paste']],
    'Kemiri' => [['id', 'Kemiri'], ['en', 'Candlenut']],
    'Ketumbar Bubuk' => [['en', 'Coriander'], ['en', 'Coriander seed']],
    'Merica Bubuk' => [['en', 'Black pepper']],
    'Pala Bubuk' => [['en', 'Nutmeg']],
    'Cengkeh' => [['en', 'Clove']],
    'Minyak Goreng' => [['en', 'Cooking oil']],
    'Santan Instan' => [['en', 'Coconut milk'], ['id', 'Santan']],
    'Kelapa Parut' => [['en', 'Coconut'], ['en', 'Copra']],
    'Tepung Terigu' => [['en', 'Wheat flour'], ['id', 'Tepung terigu']],
    'Tepung Beras' => [['en', 'Rice flour'], ['id', 'Tepung beras']],
    'Tepung Tapioka' => [['en', 'Tapioca']],
    'Maizena' => [['en', 'Corn starch'], ['en', 'Cornflour']],
    'Sambal Terasi Instan' => [['id', 'Sambal'], ['en', 'Sambal']],
    'Cuka' => [['en', 'Vinegar']],
    'Saus Tomat' => [['en', 'Ketchup']],
    'Susu UHT Full Cream' => [['en', 'Milk'], ['id', 'Susu']],
    'Susu Cair Segar' => [['en', 'Milk'], ['id', 'Susu']],
    'Susu Kental Manis' => [['en', 'Condensed milk'], ['en', 'Sweetened condensed milk']],
    'Susu Bubuk Full Cream' => [['en', 'Powdered milk'], ['en', 'Dried milk']],
    'Yogurt Plain' => [['en', 'Yogurt'], ['id', 'Yogurt']],
    'Keju Cheddar' => [['en', 'Cheddar cheese']],
    'Mentega' => [['en', 'Butter']],
    'Margarin' => [['en', 'Margarine']],
];

function httpGet(string $url): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $finalUrl = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    return [$status, $body, $finalUrl];
}

function wikiThumbnail(string $lang, string $title): ?string
{
    $query = http_build_query([
        'action' => 'query',
        'titles' => $title,
        'prop' => 'pageimages',
        'piprop' => 'thumbnail',
        'pithumbsize' => 300,
        'format' => 'json',
        'redirects' => 1,
    ]);

    [$status, $body] = httpGet("https://{$lang}.wikipedia.org/w/api.php?{$query}");
    if ($status !== 200 || empty($body)) {
        return null;
    }

    $json = json_decode($body, true);
    foreach ($json['query']['pages'] ?? [] as $page) {
        if (isset($page['thumbnail']['source'])) {
            return $page['thumbnail']['source'];
        }
    }

    return null;
}

function searchThumbnail(string $lang, string $term): ?string
{
    $query = http_build_query([
        'action' => 'query',
        'generator' => 'search',
        'gsrsearch' => $term,
        'gsrlimit' => 3,
        'gsrnamespace' => 0,
        'prop' => 'pageimages',
        'piprop' => 'thumbnail',
        'pithumbsize' => 300,
        'format' => 'json',
        'redirects' => 1,
    ]);

    [$status, $body] = httpGet("https://{$lang}.wikipedia.org/w/api.php?{$query}");
    if ($status !== 200 || empty($body)) {
        return null;
    }

    $json = json_decode($body, true);
    foreach ($json['query']['pages'] ?? [] as $page) {
        if (!empty($page['title'])) {
            $t = strtolower($page['title']);
            if (str_contains($t, 'file') || str_contains($t, 'logo') || str_contains($t, 'map') || str_contains($t, 'disambigu')) {
                continue;
            }
        }
        if (isset($page['thumbnail']['source'])) {
            return $page['thumbnail']['source'];
        }
    }

    return null;
}

function downloadThumbnail(string $url): ?array
{
    [$status, $body, $finalUrl] = httpGet($url);
    if ($status !== 200 || strlen($body) < 2000) {
        return null;
    }

    $ext = strtolower(pathinfo(parse_url($finalUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
        $ext = 'jpg';
    }

    $temp = UPLOAD_DIR . '/.tmp_' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (file_put_contents($temp, $body) === false) {
        return null;
    }

    return [$temp, $ext];
}

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0775, true);
}

$ids = DB::table('ingredients_master')->select('id', 'name')->whereNull('image_path')->orderBy('id')->get();

$ok = 0;
$fail = 0;

foreach ($ids as $ingredient) {
    $thumb = null;
    foreach ($MAP[$ingredient->name] ?? [] as [$lang, $title]) {
        $thumb = wikiThumbnail($lang, $title);
        if ($thumb) {
            break;
        }
    }
    if (!$thumb) {
        $thumb = searchThumbnail('id', $ingredient->name) ?? searchThumbnail('en', $ingredient->name);
    }

    if (!$thumb) {
        $fail++;
        echo "MISS\t{$ingredient->id}\t{$ingredient->name}\n";
        continue;
    }

    $result = downloadThumbnail($thumb);
    if (!$result) {
        $fail++;
        echo "DLFAIL\t{$ingredient->id}\t{$ingredient->name}\t{$thumb}\n";
        continue;
    }

    [$temp, $ext] = $result;
    $filename = "{$ingredient->id}-" . Str::slug($ingredient->name, '-') . ".{$ext}";
    $target = UPLOAD_DIR . '/' . $filename;

    if (!rename($temp, $target)) {
        @unlink($temp);
        $fail++;
        echo "RENAMEFAIL\t{$ingredient->id}\t{$ingredient->name}\n";
        continue;
    }

    DB::table('ingredients_master')
        ->where('id', $ingredient->id)
        ->update(['image_path' => "ingredients/{$filename}"]);

    $ok++;
    usleep(80000);
}

echo "\nDone. OK={$ok} FAIL={$fail}\n";