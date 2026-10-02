<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prompt = "Bahan: Telur Ayam. Buat SATU resep sederhana. Kembalikan HANYA JSON: {\"name\":\"x\",\"description\":\"x\",\"instructions\":[\"x\"],\"cook_time_minutes\":30,\"ingredients\":[{\"ingredient_name\":\"x\",\"quantity\":1.5,\"unit\":\"gram\"}]}";
$key = config("services.gemini.api_key");
$model = config("services.gemini.model");

for ($i=0; $i<3; $i++) {
    $response = Http::timeout(60)->withQueryParameters(["key" => $key])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
        "contents" => [["parts" => [["text" => $prompt]]]],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 2048,
            "responseMimeType" => "application/json",
        ]
    ]);
    echo "STATUS: " . $response->status() . "\n";
    if ($response->status() == 200) {
        echo "BODY: " . $response->json("candidates.0.content.parts.0.text") . "\n";
        break;
    }
    sleep(3);
}

