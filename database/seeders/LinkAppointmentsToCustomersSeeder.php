<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class LinkAppointmentsToCustomersSeeder extends Seeder
{
    public function run()
    {
        $appointments = Appointment::whereNotNull('customer_name')->get();
        
        foreach ($appointments as $appointment) {
            $customer = Customer::where('name', $appointment->customer_name)->first();
            if ($customer) {
                $appointment->customer_id = $customer->id;
                $appointment->save();
                echo "Gekoppeld: {$appointment->customer_name} aan customer ID {$customer->id}\n";
            } else {
                echo "Geen klant gevonden voor: {$appointment->customer_name}\n";
            }
        }
        
        echo "Klaar met koppelen van appointments aan klanten!\n";
    }
}
