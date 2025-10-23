<?php

use App\Models\Customer;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $id = $request->query("id");
    $scans = Scan::all();
    $currentScan = null;
    $customers = null;
    if ($id) {
        $currentScan = Scan::find($id);
        if ($currentScan) {
            $currentScan->load('customers');
            $customers = $currentScan->customers;
        }
    } else {
        $currentScan = Scan::latest()->first();
        if ($currentScan) {
            $currentScan->load('customers');
            $customers = $currentScan->customers;
        }
    }
    
    
    return view('welcome', ['scans' => $scans, 'currentScan' => $currentScan, 'customers' => $customers]);
});

Route::get("/do_scan", function () {
    // do an http request to an external service to get scan data
    $res = Http::get('http://localhost:8080/api/v1/customers');

    $resStatus = $res->status();

    if($resStatus != 200) {
        return redirect("/")->withErrors(['msg' => 'Failed to fetch scan data.']);
    }

    $resJson = $res->json();

    if ($resJson['success'] !== true) {
        return redirect("/")->withErrors(['msg' => 'Scan service returned an error.']);
    }

    $customersData = $resJson['customers'];

    if (empty($customersData)) {
        return redirect("/")->withErrors(['msg' => 'No customers found in scan data.']);
    }



    $scan = Scan::create();

    // Save customers to database
    foreach ($customersData as $customerData) {
        $customer = new Customer();
        $customer->customerId = $customerData['customerId'];
        $customer->firstName = $customerData['firstName'];
        $customer->lastName = $customerData['lastName'];
        $customer->ip = $customerData['ipAddress'];
        $customer->iban = $customerData['iban'];
        $customer->phoneNumber = $customerData['phoneNumber'];

        // there has to be a better way to do this, but I don't know how...
        $bDay = date_parse_from_format("d-m-Y", $customerData['dateOfBirth']);
        $customer->dateOfBirth = $bDay['year'] . "-" . str_pad($bDay['month'], 2, '0', STR_PAD_LEFT) . "-" . str_pad($bDay['day'], 2, '0', STR_PAD_LEFT);

        $customer->scan()->associate($scan);
        $customer->save();
    }

    $scan->save();

    // We need all the customers in the database to be able to run the validation logic. 
    foreach ($scan->customers as $customer) {
        $customer->valid = empty($customer->isValid(true));
        $customer->save();
    }



    return redirect("/");
});

Route::get("/scans", function () {
    return Scan::all()->toResourceCollection();
});

Route::get("/scans/{id}", function ($id) {
    $scan = Scan::find($id);
    if (!$scan) {
        return response()->json(['error' => 'Scan not found'], 404);
    }
    return $scan->toResource();
});

Route::get("/scans/{id}/customers", function ($id) {
    $scan = Scan::find($id);
    if (!$scan) {
        return response()->json(['error' => 'Scan not found'], 404);
    }
    return $scan->customers()->get()->toResourceCollection();
});