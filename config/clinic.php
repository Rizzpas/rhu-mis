<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Clinic Operating Days & Hours
    |--------------------------------------------------------------------------
    | Days when the Rural Health Unit (RHU) is open for scheduled consultations.
    | 0 = Sunday, 1 = Monday, ..., 6 = Saturday.
    */
    'operating_days' => [1, 2, 3, 4, 5], // Monday - Friday

    /*
    |--------------------------------------------------------------------------
    | Annual Philippine Statutory & Public Holidays (MM-DD)
    |--------------------------------------------------------------------------
    | Regular & Special non-working holidays observed by government health offices.
    */
    'annual_holidays' => [
        '01-01' => "New Year's Day",
        '04-09' => 'Araw ng Kagitingan (Day of Valor)',
        '05-01' => 'Labor Day',
        '06-12' => 'Independence Day',
        '08-21' => 'Ninoy Aquino Day',
        '11-01' => "All Saints' Day",
        '11-02' => "All Souls' Day",
        '11-30' => 'Bonifacio Day',
        '12-08' => 'Feast of the Immaculate Conception',
        '12-24' => 'Christmas Eve',
        '12-25' => 'Christmas Day',
        '12-30' => 'Rizal Day',
        '12-31' => 'New Year\'s Eve',
    ],

    /*
    |--------------------------------------------------------------------------
    | Specific Date Clinic Closures (YYYY-MM-DD)
    |--------------------------------------------------------------------------
    | One-off closures such as LGU declarations, weather emergencies,
    | local town fiestas, or facility sanitization.
    */
    'specific_closures' => [
        // e.g. '2026-02-02' => 'Silang Town Fiesta',
    ],

    /*
    |--------------------------------------------------------------------------
    | Diagnostic Services Catalog (Accredited RHU Laboratory & Radiology)
    |--------------------------------------------------------------------------
    | Authoritative list of laboratory and radiological procedures performed
    | on-premise at the Municipal Health Office.
    */
    'diagnostic_catalog' => [
        'Laboratory' => [
            'Complete Blood Count (CBC)',
            'Urinalysis',
            'Fecalysis',
            'Blood Chemistry (FBS / Lipid Profile)',
        ],
        'Radiology' => [
            'Chest X-Ray',
        ],
    ],
];
