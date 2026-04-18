<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            // 東京都 / 都心・城南
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '新宿区',   'slug' => 'shinjuku'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '渋谷区',   'slug' => 'shibuya'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '港区',     'slug' => 'minato'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '千代田区', 'slug' => 'chiyoda'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '中央区',   'slug' => 'chuo'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '品川区',   'slug' => 'shinagawa'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '目黒区',   'slug' => 'meguro'],
            ['prefecture' => '東京都', 'region' => '都心・城南', 'name' => '大田区',   'slug' => 'ota'],
            // 東京都 / 城東
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '江東区',   'slug' => 'koto'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '墨田区',   'slug' => 'sumida'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '江戸川区', 'slug' => 'edogawa'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '葛飾区',   'slug' => 'katsushika'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '足立区',   'slug' => 'adachi'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '荒川区',   'slug' => 'arakawa'],
            ['prefecture' => '東京都', 'region' => '城東', 'name' => '台東区',   'slug' => 'taito'],
            // 東京都 / 城北
            ['prefecture' => '東京都', 'region' => '城北', 'name' => '豊島区', 'slug' => 'toshima'],
            ['prefecture' => '東京都', 'region' => '城北', 'name' => '北区',   'slug' => 'kita'],
            ['prefecture' => '東京都', 'region' => '城北', 'name' => '板橋区', 'slug' => 'itabashi'],
            ['prefecture' => '東京都', 'region' => '城北', 'name' => '練馬区', 'slug' => 'nerima'],
            // 東京都 / 城西
            ['prefecture' => '東京都', 'region' => '城西', 'name' => '杉並区', 'slug' => 'suginami'],
            ['prefecture' => '東京都', 'region' => '城西', 'name' => '中野区', 'slug' => 'nakano'],
            ['prefecture' => '東京都', 'region' => '城西', 'name' => '世田谷区', 'slug' => 'setagaya'],
            ['prefecture' => '東京都', 'region' => '城西', 'name' => '文京区', 'slug' => 'bunkyo'],
            // 東京都 / 多摩
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '八王子市',  'slug' => 'hachioji'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '立川市',    'slug' => 'tachikawa'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '町田市',    'slug' => 'machida'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '府中市',    'slug' => 'fuchu'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '調布市',    'slug' => 'chofu'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '三鷹市',    'slug' => 'mitaka'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '武蔵野市',  'slug' => 'musashino'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '西東京市',  'slug' => 'nishitokyo'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '東村山市',  'slug' => 'higashimurayama'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '小平市',    'slug' => 'kodaira'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '日野市',    'slug' => 'hino'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '多摩市',    'slug' => 'tama'],
            ['prefecture' => '東京都', 'region' => '多摩', 'name' => '稲城市',    'slug' => 'inagi'],
            // 沖縄県 / 那覇・南部
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '那覇市',   'slug' => 'naha'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '豊見城市', 'slug' => 'tomigusuku'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '糸満市',   'slug' => 'itoman'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '南城市',   'slug' => 'nanjo'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '八重瀬町', 'slug' => 'yaese'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '南風原町', 'slug' => 'haebaru'],
            ['prefecture' => '沖縄県', 'region' => '那覇・南部', 'name' => '与那原町', 'slug' => 'yonabaru'],
            // 沖縄県 / 中部
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '浦添市',   'slug' => 'urasoe'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '宜野湾市', 'slug' => 'ginowan'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '沖縄市',   'slug' => 'okinawa_city'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => 'うるま市', 'slug' => 'uruma'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '北谷町',   'slug' => 'chatan'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '嘉手納町', 'slug' => 'kadena'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '読谷村',   'slug' => 'yomitan'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '北中城村', 'slug' => 'kitanakagusuku'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '中城村',   'slug' => 'nakagusuku'],
            ['prefecture' => '沖縄県', 'region' => '中部', 'name' => '西原町',   'slug' => 'nishihara'],
            // 沖縄県 / 北部
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '名護市',   'slug' => 'nago'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '恩納村',   'slug' => 'onna'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '今帰仁村', 'slug' => 'nakijin'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '本部町',   'slug' => 'motobu'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '金武町',   'slug' => 'kin'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '宜野座村', 'slug' => 'ginoza'],
            ['prefecture' => '沖縄県', 'region' => '北部', 'name' => '大宜味村', 'slug' => 'ogimi'],
            // 沖縄県 / 離島
            ['prefecture' => '沖縄県', 'region' => '離島', 'name' => '石垣市',  'slug' => 'ishigaki'],
            ['prefecture' => '沖縄県', 'region' => '離島', 'name' => '宮古島市', 'slug' => 'miyakojima'],
            ['prefecture' => '沖縄県', 'region' => '離島', 'name' => '久米島町', 'slug' => 'kumejima'],
        ];

        foreach ($areas as $i => $area) {
            DB::table('master_areas')->insertOrIgnore(array_merge($area, [
                'sort_order' => $i + 1,
                'is_active'  => true,
                'created_at' => now(),
            ]));
        }
    }
}
