<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_items', function (Blueprint $t) {
            $t->string('platform', 20)->nullable();
            $t->text('description')->nullable();
            $t->string('image_path', 500)->nullable();
            $t->json('games')->nullable();
            $t->boolean('is_visible')->default(true);
        });
        $defaults = json_decode(<<<'JSON'
{
  "consoles": [
    {
      "name": "PlayStation 5",
      "platform": "ps5",
      "description": "PS5 Disc Edition Ultra • 4K 120Hz Ray Tracing • SSD Ultra High Speed • 2 Unit DualSense Wireless (Haptic)",
      "image_path": "images/ps5-ultra.png",
      "games": [
        "FC 24",
        "Spider-Man 2",
        "Tekken 8",
        "God of War Ragnarok",
        "Gran Turismo 7",
        "Horizon Forbidden West",
        "Final Fantasy XVI",
        "Ghost of Tsushima Director Cut"
      ]
    },
    {
      "name": "PlayStation 4",
      "platform": "ps4",
      "description": "PS4 Pro 1TB • Dynamic 4K Boost Mode • HDR Gaming • 2 Unit DualShock 4 Wireless Original",
      "image_path": "images/ps4-pro.png",
      "games": [
        "PES 2024 Patch Terbaru",
        "Red Dead Redemption 2",
        "FIFA 23",
        "Ghost of Tsushima",
        "Spider-Man",
        "The Last of Us Part II",
        "Uncharted 4"
      ]
    },
    {
      "name": "PlayStation 3",
      "platform": "ps3",
      "description": "PlayStation 3 Slim 500GB HDD • 2 Stik DualShock 3 Wireless • HDMI Full HD 1080p",
      "image_path": "images/ps3-unit.png",
      "games": [
        "eFootball PES 2024 Patch",
        "FIFA 23 Legacy",
        "GTA V",
        "Tekken Tag Tournament 2",
        "Need for Speed Most Wanted",
        "God of War 3"
      ]
    },
    {
      "name": "Nintendo Switch",
      "platform": "nintendo",
      "description": "Nintendo Switch OLED White 64GB • 2 Joy-Con + Pro Controller • TV Dock Mode & Handheld",
      "image_path": "images/nintendo-unit.png",
      "games": [
        "Mario Kart 8 Deluxe",
        "Zelda: Tears of the Kingdom",
        "Super Smash Bros Ultimate",
        "Super Mario Bros Wonder",
        "Splatoon 3"
      ]
    }
  ],
  "foods": [
    {
      "name": "Indomie Goreng Double + Telur Kornet",
      "category": "Makanan",
      "price": 18000,
      "description": "Mi goreng jumbo dobel porsi dengan telur mata sapi setengah matang, kornet tumis gurih, dan taburan bawang renyah.",
      "image_path": "images/fnb-indomie.jpg"
    },
    {
      "name": "Kentang Goreng Truffle Crinkle",
      "category": "Camilan",
      "price": 15000,
      "description": "Kentang crinkle renyah dengan semprotan minyak truffle asli, taburan keju parmesan gurih dan garlic dip.",
      "image_path": "images/fnb-kentang.jpg"
    },
    {
      "name": "Nasi Goreng Spesial Seleraku",
      "category": "Makanan",
      "price": 28000,
      "description": "Nasi goreng rempah istimewa dengan suwiran ayam, sosis sapi panggang, telur dadar iris, dan kerupuk udang renyah.",
      "image_path": "images/fnb-nasgor.jpg"
    },
    {
      "name": "Chicken Popcorn Spicy Mayo",
      "category": "Camilan",
      "price": 22000,
      "description": "Daging paha ayam tanpa tulang renyah berbalut bumbu rahasia dengan cocolan saus spicy mayo pedas manis nagih.",
      "image_path": "images/fnb-chicken.jpg"
    },
    {
      "name": "Kopi Susu Gula Aren Barista",
      "category": "Minuman",
      "price": 16000,
      "description": "Double shot espresso arabika pilihan dipadukan susu segar pasteurisasi dan legitnya sirup gula aren organik.",
      "image_path": "images/fnb-kopi.jpg"
    },
    {
      "name": "Es Teh Manis Jumbo Lemonade",
      "category": "Minuman",
      "price": 14000,
      "description": "Seduhan teh melati wangi khas porsi disajikan dalam gelas jumbo 700ml dengan perasan jeruk lemon asli yang segar.",
      "image_path": "images/fnb-esteh.jpg"
    }
  ]
}
JSON, true);
        foreach ($defaults['consoles'] as $item) {
            $name = $item['name'];
            unset($item['name']);
            $item['games'] = json_encode($item['games']);
            DB::table('catalog_items')->where('name', $name)->where('category', 'Konsol')->update($item);
        }
        foreach ($defaults['foods'] as $item) {
            if (! DB::table('catalog_items')->where('name', $item['name'])->where('category', $item['category'])->exists()) {
                DB::table('catalog_items')->insert($item + ['stock' => 0, 'is_visible' => true, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('catalog_items', fn (Blueprint $t) => $t->dropColumn(['platform', 'description', 'image_path', 'games', 'is_visible']));
    }
};
