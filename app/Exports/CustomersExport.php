<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all customers
     */
    public function collection()
    {
        return Customer::all();
    }

    /**
     * Map each row for export
     */
    public function map($customer): array
    {
        return [
            $customer->name,
            $customer->username,
            $customer->password,
            $customer->link,
            $customer->status ? 'Active' : 'Inactive',
            $customer->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Headings for Excel file
     */
    public function headings(): array
    {
        return [
            'Name',
            'Username',
            'Password',
            'Link',
            'Status',
            'Created At',
        ];
    }
}
