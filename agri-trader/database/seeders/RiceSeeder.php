<?php

namespace Database\Seeders;

use App\Models\Produce;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        include_once("simple_html_dom.php");

        $html = file_get_html("http://philfoodie.blogspot.com/2011/05/philippine-rice-varieties.html");

        foreach ($html->find('div[style="float: left; height: 150px; width: 200px;"]') as $lists) {
            foreach ($lists->children() as $items) {
                foreach ($items->children() as $item) {
                    if($item->plaintext == 'Dinurado / Denorado'){
                        Produce::create([
                            'prod_name' => 'Dinurado',
                            'prod_timeOfHarvest' => '130-136 days',
                            'prod_type' => 'Rice'
                        ]);
                    }
                    else if($item->plaintext != 'C4 (UPLB C4-63G)' && $item->plaintext != 'IR42' && $item->plaintext != 'IR64'){
                        Produce::create([
                            'prod_name' => trim($item->plaintext),
                            'prod_timeOfHarvest' => '130-136 days',
                            'prod_type' => 'Rice'
                        ]);
                    }

                }
            }
        }
    }
}
