# RHU MIS — Print System Developer Guide & Migration

This document explains the unified print system for **Rural Health Unit Management Information System (RHU MIS)**, Silang, Cavite.

All printable views now share **ONE print stylesheet (`public/css/print.css`)** and **ONE layout component (`<x-print-layout>`)**. No view may define its own `@page` rule, letterhead, or ad-hoc print CSS.

---

## 🚀 How to Make Any View Printable in Under 10 Lines

### Pattern A: Dedicated Printable Report View (Full Page)
For treatment records, audit summaries, or dedicated report pages that open in a tab:

```blade
<x-print-layout 
    title="Quarterly Immunization Coverage Report"
    subtitle="EPI Antigen Administration & Defaulter Tracking"
    period="Q3 2026 (July – September)"
>
    {{-- Your report tables, KPI metrics, or charts go here --}}
    <div class="rhu-kpi-grid">...</div>
    <table class="rhu-data-table">...</table>
</x-print-layout>
```

---

### Pattern B: Embedded Printable View (Inside Existing Web App View)
For pages that live within standard staff portal layouts (e.g. Admin, Pharmacy, Laboratory, Analytics) that should print cleanly when the user clicks **"Print Report"** or presses `Ctrl+P`:

```blade
<x-print-layout 
    :isFullPage="false"
    title="Weekly Triage & Consultation Summary"
    period="Week 40, October 2026"
>
    {{-- On screen: renders normally. On print: automatically prepends repeating letterhead & footer --}}
    <div class="space-y-6">
        @include('partials.report-body')
    </div>
</x-print-layout>
```

And trigger it with:
```blade
<button onclick="window.printReport({ title: 'Weekly Triage Summary' })">
    Print Report
</button>
```

---

### Pattern C: JavaScript Isolated Modal/Section Print
For printing a modal dialog or dynamic DOM element without parent page background leakage:

```javascript
window.printIsolated(document.getElementById('my-modal-content').innerHTML, {
    title: 'Diagnostic Examination Report',
    paperSize: 'auto' // 'auto' | 'a4' | 'letter'
});
```

---

## 📋 Available Component Props (`<x-print-layout>`)

| Prop | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `title` | `string` | `'Official Health Facility Report'` | Main document title (bold 15pt in print). |
| `subtitle` | `string\|null` | `null` | Explanatory subheader directly under title. |
| `period` | `string` | `'Current / All Time'` | Displayed in 4-column metadata strip. |
| `facility` | `string` | *Loaded from SiteSetting* | Name of Rural Health Unit facility. |
| `generatedBy` | `string` | *Logged-in user name* | Name and title of generating staff. |
| `dateGenerated` | `string` | `now()->format('M d, Y h:i A')` | Timestamp stamped on header and footer. |
| `paperSize` | `'auto'\|'a4'\|'letter'` | `'auto'` | Sets CSS `@page` paper dimension. |
| `isFullPage` | `boolean` | `true` | `true` for standalone preview sheet; `false` for in-page embedded views. |
| `showPreviewBar` | `boolean` | `true` | Displays interactive paper sheet toolbar (screen only). |

---

## 🎨 Recommended Print Utility Classes

| Class | Purpose | Behavior in Print |
| :--- | :--- | :--- |
| `.avoid-break` | Clean page breaks | Applies `break-inside: avoid;` to cards, table rows, and panels. |
| `.page-break` | Forced page break | Forces content to start on the next sheet (`break-before: page;`). |
| `.rhu-kpi-grid` | KPI stat tiles | Responsive 4-column grid that automatically collapses to 2 columns on narrow widths or Letter paper. |
| `.rhu-kpi-card` | KPI stat card | Bordered card with uppercase header and high-contrast tabular number. |
| `.rhu-data-table` | Data table | Standardized report table with repeating `thead`, clean cell borders, and right-aligned numeric cells. |
| `.rhu-signature-strip` | Signature block | Multi-box signature strip (`avoid-break`) with formal lines and titles. |
| `.print:hidden` / `.no-print` | UI suppression | Completely hides buttons, forms, tooltips, toasts, and navigation. |

---

## ⚡ Charts & Dynamic Canvases
`window.printReport()` automatically:
1. Freezes Chart.js animations.
2. Rasterizes all `<canvas>` elements to high-DPI (2x) PNG images before the print dialog opens.
3. Automatically restores live interactive canvases when the print dialog closes (`afterprint`).
