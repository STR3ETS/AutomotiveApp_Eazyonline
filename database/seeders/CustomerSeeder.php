<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            [
                'name' => 'P. van der Berg',
                'email' => 'p.vandenberg@email.com',
                'phone' => '06-12345678',
                'address' => 'Hoofdstraat 123, 1234 AB Amsterdam'
            ],
            [
                'name' => 'J. Bakker',
                'email' => 'j.bakker@gmail.com',
                'phone' => '06-87654321',
                'address' => 'Kerkstraat 45, 5678 CD Utrecht'
            ],
            [
                'name' => 'M. de Vries',
                'email' => 'm.devries@hotmail.com',
                'phone' => '06-55566677',
                'address' => 'Dorpsplein 12, 9876 EF Den Haag'
            ],
            [
                'name' => 'A. Jansen',
                'email' => 'a.jansen@outlook.com',
                'phone' => '06-99988877',
                'address' => 'Stationsweg 78, 1111 GH Rotterdam'
            ],
            [
                'name' => 'S. van Dijk',
                'email' => 's.vandijk@example.com',
                'phone' => '06-44433322',
                'address' => 'Nieuwe Weg 234, 2222 IJ Eindhoven'
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        echo "Test klanten toegevoegd!\n";
    }
}
