<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            // London
            ['name' => 'London',              'region' => 'London',         'postcode_prefix' => 'E',   'lat' => 51.5074,  'lng' => -0.1278],
            ['name' => 'North London',        'region' => 'London',         'postcode_prefix' => 'N',   'lat' => 51.5600,  'lng' => -0.1000],
            ['name' => 'South London',        'region' => 'London',         'postcode_prefix' => 'SE',  'lat' => 51.4900,  'lng' => -0.0800],
            ['name' => 'West London',         'region' => 'London',         'postcode_prefix' => 'W',   'lat' => 51.5100,  'lng' => -0.2200],
            ['name' => 'East London',         'region' => 'London',         'postcode_prefix' => 'E',   'lat' => 51.5300,  'lng' => -0.0500],
            // South East
            ['name' => 'Brighton',            'region' => 'South East',     'postcode_prefix' => 'BN',  'lat' => 50.8225,  'lng' => -0.1372],
            ['name' => 'Southampton',         'region' => 'South East',     'postcode_prefix' => 'SO',  'lat' => 50.9097,  'lng' => -1.4044],
            ['name' => 'Oxford',              'region' => 'South East',     'postcode_prefix' => 'OX',  'lat' => 51.7520,  'lng' => -1.2577],
            ['name' => 'Reading',             'region' => 'South East',     'postcode_prefix' => 'RG',  'lat' => 51.4543,  'lng' => -0.9781],
            // South West
            ['name' => 'Bristol',             'region' => 'South West',     'postcode_prefix' => 'BS',  'lat' => 51.4545,  'lng' => -2.5879],
            ['name' => 'Exeter',              'region' => 'South West',     'postcode_prefix' => 'EX',  'lat' => 50.7184,  'lng' => -3.5339],
            ['name' => 'Plymouth',            'region' => 'South West',     'postcode_prefix' => 'PL',  'lat' => 50.3755,  'lng' => -4.1427],
            // Midlands
            ['name' => 'Birmingham',          'region' => 'Midlands',       'postcode_prefix' => 'B',   'lat' => 52.4862,  'lng' => -1.8904],
            ['name' => 'Coventry',            'region' => 'Midlands',       'postcode_prefix' => 'CV',  'lat' => 52.4068,  'lng' => -1.5197],
            ['name' => 'Nottingham',          'region' => 'Midlands',       'postcode_prefix' => 'NG',  'lat' => 52.9540,  'lng' => -1.1550],
            ['name' => 'Leicester',           'region' => 'Midlands',       'postcode_prefix' => 'LE',  'lat' => 52.6369,  'lng' => -1.1398],
            ['name' => 'Derby',               'region' => 'Midlands',       'postcode_prefix' => 'DE',  'lat' => 52.9225,  'lng' => -1.4746],
            // East of England
            ['name' => 'Cambridge',           'region' => 'East of England','postcode_prefix' => 'CB',  'lat' => 52.2053,  'lng' => 0.1218],
            ['name' => 'Norwich',             'region' => 'East of England','postcode_prefix' => 'NR',  'lat' => 52.6309,  'lng' => 1.2974],
            ['name' => 'Ipswich',             'region' => 'East of England','postcode_prefix' => 'IP',  'lat' => 52.0567,  'lng' => 1.1482],
            // North West
            ['name' => 'Manchester',          'region' => 'North West',     'postcode_prefix' => 'M',   'lat' => 53.4808,  'lng' => -2.2426],
            ['name' => 'Liverpool',           'region' => 'North West',     'postcode_prefix' => 'L',   'lat' => 53.4084,  'lng' => -2.9916],
            ['name' => 'Preston',             'region' => 'North West',     'postcode_prefix' => 'PR',  'lat' => 53.7632,  'lng' => -2.7031],
            ['name' => 'Blackpool',           'region' => 'North West',     'postcode_prefix' => 'FY',  'lat' => 53.8175,  'lng' => -3.0357],
            // North East
            ['name' => 'Newcastle upon Tyne', 'region' => 'North East',     'postcode_prefix' => 'NE',  'lat' => 54.9783,  'lng' => -1.6178],
            ['name' => 'Sunderland',          'region' => 'North East',     'postcode_prefix' => 'SR',  'lat' => 54.9069,  'lng' => -1.3838],
            ['name' => 'Middlesbrough',       'region' => 'North East',     'postcode_prefix' => 'TS',  'lat' => 54.5742,  'lng' => -1.2350],
            // Yorkshire
            ['name' => 'Leeds',               'region' => 'Yorkshire',      'postcode_prefix' => 'LS',  'lat' => 53.8008,  'lng' => -1.5491],
            ['name' => 'Sheffield',           'region' => 'Yorkshire',      'postcode_prefix' => 'S',   'lat' => 53.3811,  'lng' => -1.4701],
            ['name' => 'Bradford',            'region' => 'Yorkshire',      'postcode_prefix' => 'BD',  'lat' => 53.7950,  'lng' => -1.7594],
            ['name' => 'Hull',                'region' => 'Yorkshire',      'postcode_prefix' => 'HU',  'lat' => 53.7676,  'lng' => -0.3274],
            // Wales
            ['name' => 'Cardiff',             'region' => 'Wales',          'postcode_prefix' => 'CF',  'lat' => 51.4816,  'lng' => -3.1791],
            ['name' => 'Swansea',             'region' => 'Wales',          'postcode_prefix' => 'SA',  'lat' => 51.6214,  'lng' => -3.9436],
            // Scotland
            ['name' => 'Glasgow',             'region' => 'Scotland',       'postcode_prefix' => 'G',   'lat' => 55.8642,  'lng' => -4.2518],
            ['name' => 'Edinburgh',           'region' => 'Scotland',       'postcode_prefix' => 'EH',  'lat' => 55.9533,  'lng' => -3.1883],
            ['name' => 'Aberdeen',            'region' => 'Scotland',       'postcode_prefix' => 'AB',  'lat' => 57.1497,  'lng' => -2.0943],
            ['name' => 'Dundee',              'region' => 'Scotland',       'postcode_prefix' => 'DD',  'lat' => 56.4620,  'lng' => -2.9707],
            // Northern Ireland
            ['name' => 'Belfast',             'region' => 'Northern Ireland','postcode_prefix' => 'BT', 'lat' => 54.5973,  'lng' => -5.9301],
            ['name' => 'Derry / Londonderry', 'region' => 'Northern Ireland','postcode_prefix' => 'BT', 'lat' => 54.9966,  'lng' => -7.3086],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(
                ['slug' => Str::slug($loc['name'])],
                [
                    'name'            => $loc['name'],
                    'region'          => $loc['region'],
                    'country'         => 'GB',
                    'postcode_prefix' => $loc['postcode_prefix'],
                    'latitude'        => $loc['lat'],
                    'longitude'       => $loc['lng'],
                    'active'          => true,
                ]
            );
        }
    }
}
