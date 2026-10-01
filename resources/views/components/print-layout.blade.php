@props([
    'title' => 'Official Health Facility Report',
    'subtitle' => null,
    'period' => 'Current / All Time',
    'facility' => null,
    'generatedBy' => null,
    'dateGenerated' => null,
    'paperSize' => 'auto',
    'confidentiality' => null,
    'leftLogo' => null,
    'rightLogo' => null,
    'republicLine' => null,
    'provinceLine' => null,
    'facilityName' => null,
    'systemName' => null,

    'isFullPage' => true,
])

@php
    // Single Source of Truth for Facility & System Configuration
    $republicLine = $republicLine ?? \App\Models\SiteSetting::get('topbar_republic', 'Republic of the Philippines');
    $provinceLine = $provinceLine ?? \App\Models\SiteSetting::get('topbar_province', 'Province of Cavite');
    $municipality = \App\Models\SiteSetting::get('topbar_municipality', 'Municipality of Silang');
    $facilityName = $facilityName ?? ($municipality . ' — Rural Health Unit');
    $systemName = $systemName ?? 'Rural Health Unit Management Information System (RHU MIS)';
    
    $facility = $facility ?? $facilityName;
    $generatedBy = $generatedBy ?? (auth()->check() ? auth()->user()->name : 'System Generated');
    $dateGenerated = $dateGenerated ?? now()->format('M d, Y h:i A');
    
    $leftLogo = $leftLogo ?? asset('assets/images/logo.png');
    
    $confidentiality = $confidentiality ?? 'CONFIDENTIAL HEALTH & ADMINISTRATIVE RECORD — Contains protected health information subject to Republic Act No. 10173 (Data Privacy Act of 2012). Unauthorized disclosure, copying, or distribution is strictly prohibited.';
@endphp

@if($isFullPage)
<!DOCTYPE html>
<html lang="en" data-paper="{{ $paperSize }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ e($title) }} — RHU MIS</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Shared Unified Print Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">

    <!-- Tailwind CSS Engine for Utility Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <!-- Scripts -->
    <script src="{{ asset('js/print-helper.js') }}"></script>
    @stack('styles')
</head>
<body class="rhu-print-preview-body">
@endif



    @if($isFullPage)
    {{-- Paper Container for Full-Page Standalone / Preview Mode --}}
    <div class="rhu-paper-sheet {{ $paperSize === 'letter' ? 'preview-letter' : '' }}">
    @endif
        
        {{-- Multi-page Repeating Table Architecture --}}
        <table class="rhu-print-table {{ !$isFullPage ? 'in-page' : '' }}">
            
            {{-- Templated Header (Repeats on every printed page via display: table-header-group) --}}
            <thead class="rhu-print-thead {{ !$isFullPage ? 'in-page' : '' }}">
                <tr>
                    <td class="rhu-print-header-cell">
                        <header class="rhu-print-header">
                            <div class="rhu-letterhead">
                                <!-- Left Logo -->
                                <div class="rhu-logo-left">
                                    <img src="{{ $leftLogo }}" alt="Municipal Seal" class="rhu-letterhead-logo" onerror="this.style.display='none'">
                                </div>

                                <!-- Centered Hierarchy -->
                                <div class="rhu-letterhead-center">
                                    <div class="rhu-lh-republic">{{ e($republicLine) }}</div>
                                    <div class="rhu-lh-province">{{ e($provinceLine) }}</div>
                                    <h2 class="rhu-lh-facility">{{ e($facilityName) }}</h2>
                                    <div class="rhu-lh-system">{{ e($systemName) }}</div>
                                </div>

                                <!-- Right Logo (DOH / Agency Seal or graceful vector emblem) -->
                                <div class="rhu-logo-right">
                                    @if($rightLogo)
                                        <img src="{{ $rightLogo }}" alt="Agency Seal" class="rhu-letterhead-logo" onerror="this.style.display='none'">
                                    @else
                                        {{-- Official Philippine Health Caduceus Vector Emblem --}}
                                        <svg class="rhu-letterhead-logo" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Official Health Seal">
                                            <circle cx="32" cy="32" r="30" stroke="#0f6b57" stroke-width="2.5" fill="#f0fdf4"/>
                                            <circle cx="32" cy="32" r="25" stroke="#0f6b57" stroke-width="1" stroke-dasharray="2 2"/>
                                            <!-- Medical Cross with Staff -->
                                            <path d="M32 14v36M22 24h20M24 38h16" stroke="#0f6b57" stroke-width="3" stroke-linecap="round"/>
                                            <circle cx="32" cy="13" r="3" fill="#0f6b57"/>
                                            <path d="M26 21c3-2 9-2 12 0M26 29c3-2 9-2 12 0M26 37c3-2 9-2 12 0" stroke="#0f6b57" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Thin Double Rule in Brand Color (#0f6b57) -->
                            <div class="rhu-letterhead-rule"></div>
                        </header>
                    </td>
                </tr>
            </thead>

            {{-- Main Content Body --}}
            <tbody class="rhu-print-tbody {{ !$isFullPage ? 'in-page' : '' }}">
                <tr>
                    <td class="rhu-print-body-cell">
                        <main class="rhu-print-content">
                            
                            {{-- First-Page Report Title Block --}}
                            <div class="rhu-title-block {{ !$isFullPage ? 'in-page' : '' }}">
                                <h1 class="rhu-report-title">{{ e($title) }}</h1>
                                @if($subtitle)
                                    <p class="rhu-report-subtitle">{{ e($subtitle) }}</p>
                                @endif

                                <div class="rhu-meta-strip">
                                    <div class="rhu-meta-item">
                                        <span class="rhu-meta-label">Reporting Period</span>
                                        <span class="rhu-meta-value">{{ e($period) }}</span>
                                    </div>
                                    <div class="rhu-meta-item">
                                        <span class="rhu-meta-label">Facility</span>
                                        <span class="rhu-meta-value">{{ e($facility) }}</span>
                                    </div>
                                    <div class="rhu-meta-item">
                                        <span class="rhu-meta-label">Generated By</span>
                                        <span class="rhu-meta-value">{{ e($generatedBy) }}</span>
                                    </div>
                                    <div class="rhu-meta-item">
                                        <span class="rhu-meta-label">Date Generated</span>
                                        <span class="rhu-meta-value">{{ e($dateGenerated) }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Printable Content Slot --}}
                            <div class="rhu-report-slot">
                                {{ $slot }}
                            </div>

                        </main>
                    </td>
                </tr>
            </tbody>

            {{-- Templated Footer (Repeats on every printed page via display: table-footer-group) --}}
            <tfoot class="rhu-print-tfoot {{ !$isFullPage ? 'in-page' : '' }}">
                <tr>
                    <td class="rhu-print-footer-cell">
                        <footer class="rhu-print-footer">
                            <div class="rhu-footer-rule"></div>
                            <div class="rhu-footer-content">
                                <div class="rhu-footer-left">
                                    {{ e($confidentiality) }}
                                </div>
                                <div class="rhu-footer-right">
                                    <div>{{ e($title) }}</div>
                                    <div>Printed: {{ e($dateGenerated) }} &bull; {{ e($generatedBy) }}</div>
                                </div>
                            </div>
                        </footer>
                    </td>
                </tr>
            </tfoot>

        </table>

    @if($isFullPage)
    </div>
    @endif

@if($isFullPage)
    @stack('scripts')
</body>
</html>
@endif
