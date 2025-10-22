<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    public function scan()
    {
        return $this->belongsTo(Scan::class);
    }

    /**
     * Check if the customer is valid according to the fraud rules
     * 
     * Checks performed:
     * - Phone number must be Dutch, +31 as a prefix
     * - Customer must be at least 18 years old at the time of the scan
     * - IP address must be unique within the scan
     * - IBAN must be unique within the scan
     * 
     * @return array An array of error messages. Empty if the customer is valid.
     */
    public function isValid(bool $forDb = false): array
    {
        // we skip checks if have already determined the customer is valid for db storage
        if (!$forDb && $this->valid) {
            return [];
        }
        $errors = [];
        if (! (str_starts_with($this->phoneNumber, '+31'))) {
            $errors[] = 'Not a Dutch phone number';
        }

        $customerDate = new \DateTime($this->dateOfBirth);
        $now = new \DateTime;
        $ageInterval = $now->diff($customerDate);
        if ($ageInterval->y < 18) {
            $errors[] = 'Customer is under 18 years old';
        }

        if ($forDb) {
            // if we want to calculate validity for database storage, 
            // we can ignore what customers have the issue and simply check if there are duplicates
            $ipCount = Customer::where('scan_id', $this->scan_id)
                ->where('ip', $this->ip)
                ->count();
            if ($ipCount > 1) {
                $errors[] = 'IP address is not unique in this scan';
            }
            $ibanCount = Customer::where('scan_id', $this->scan_id)
                ->where('iban', $this->iban)
                ->count();
            if ($ibanCount > 1) {
                $errors[] = 'IBAN is not unique in this scan';
            }
        } else {
            // however if we want to display the errors to the user,
            // we need to know which other customers have the same IP/IBAN

            // check if the IP address occurs more than one time in this scan
            $sameIp = Customer::where('scan_id', $this->scan_id)
                ->where('ip', $this->ip)->get();
            if (count($sameIp) > 1) {
                // display the names of the other customers with the same ip
                $otherNames = [];
                foreach ($sameIp as $cust) {
                    if ($cust->id != $this->id) {
                        $otherNames[] = $cust->firstName . ' ' . $cust->lastName;
                    }
                }
                // Record the names of other customers with the same IP
                $errors[] = "IP address also used by: " . implode(", ", $otherNames);
            }

            // and do the same for the IBAN field
            $sameIban = Customer::where('scan_id', $this->scan_id)
                ->where('iban', $this->iban)
                ->get();
            if (count($sameIban) > 1) {
                $otherNames = [];
                foreach ($sameIban as $cust) {
                    if ($cust->id != $this->id) {
                        $otherNames[] = $cust->firstName . ' ' . $cust->lastName;
                    }
                }
                $errors[] = "IBAN also used by: " . implode(", ", $otherNames);
            }
        }
        

        return $errors;
    }
}
