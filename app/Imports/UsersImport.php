<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithHeadingRow
{
    protected $agency_id;
    protected $skippedRows = []; // To track skipped rows

    // Constructor to accept the agency_id
    public function __construct($agency_id)
    {
        $this->agency_id = $agency_id;
    }

    // Retrieve skipped rows
    public function getSkippedRows()
    {
        return $this->skippedRows;
    }

    public function model(array $row)
    {
        // Check if the email already exists
        $existingUser = User::where('email', $row['email'])->first();

        if ($existingUser) {
            // Add the row email to skipped rows
            $this->skippedRows[] = $row['email'];
            return null; // Skip this row
        }

        // Create a new user if not a duplicate
        return new User([
            'first_name'   => $row['first_name'],
            'last_name'    => $row['last_name'],
            'email'        => $row['email'],
            'user_name'    => $row['user_name'], 
            'phone_number' => $row['phone_number'],
            'zipcode'      => $row['zipcode'],
            'password'     => Hash::make($row['password']),
            'agency_id'    => $this->agency_id, // Use the agency_id from the constructor
           
            

            'role'         => 'Viewer',
        ]);
    }
}

