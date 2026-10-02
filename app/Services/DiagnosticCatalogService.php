<?php

namespace App\Services;

class DiagnosticCatalogService
{
    /**
     * Get allowed tests for a given diagnostic service type.
     */
    public static function getAllowedTests(string $type): array
    {
        $catalog = config('clinic.diagnostic_catalog', [
            'Laboratory' => [
                'Complete Blood Count (CBC)',
                'Urinalysis',
                'Fecalysis',
                'Blood Chemistry (FBS / Lipid Profile)',
            ],
            'Radiology' => [
                'Chest X-Ray',
            ],
        ]);

        return $catalog[$type] ?? [];
    }

    /**
     * Check if a given test name is valid within the RHU catalog.
     */
    public static function isValidTest(string $type, string $testName): bool
    {
        return in_array($testName, self::getAllowedTests($type), true);
    }
}
