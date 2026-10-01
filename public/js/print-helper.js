/**
 * RHU MIS - Unified Print Helper
 * Rural Health Unit Management Information System (Silang, Cavite)
 * 
 * Provides:
 * - window.printReport(options)
 * - window.printIsolated(target, options)
 * - Automatic Chart.js rasterization before print
 * - Font & asset readiness detection
 * - Dynamic paper size injection (Auto, A4, Letter)
 */

(function () {
    'use strict';

    // Global Registry & State
    window.RHUPrint = {
        config: {
            brandColor: '#0f6b57',
            defaultPaperSize: 'auto',
            cssPath: '/css/print.css'
        },

        /**
         * Sets dynamic paper size by injecting/updating an @page style rule.
         * @param {'auto'|'a4'|'letter'} paperSize 
         */
        setPaperSize: function (paperSize) {
            paperSize = (paperSize || 'auto').toLowerCase();
            let styleTag = document.getElementById('rhu-print-page-style');
            if (!styleTag) {
                styleTag = document.createElement('style');
                styleTag.id = 'rhu-print-page-style';
                document.head.appendChild(styleTag);
            }

            if (paperSize === 'a4') {
                styleTag.textContent = '@page { size: A4 portrait !important; margin: 12mm 14mm 16mm 14mm !important; }';
            } else if (paperSize === 'letter') {
                styleTag.textContent = '@page { size: letter portrait !important; margin: 12mm 14mm 16mm 14mm !important; }';
            } else {
                styleTag.textContent = '@page { size: auto !important; margin: 12mm 14mm 16mm 14mm !important; }';
            }

            // Also update any screen preview container if present
            const sheet = document.querySelector('.rhu-paper-sheet');
            if (sheet) {
                sheet.classList.remove('preview-letter');
                if (paperSize === 'letter') {
                    sheet.classList.add('preview-letter');
                }
            }
        },

        /**
         * Converts all Chart.js or HTML5 canvases within a root element to high-res static images
         * to prevent blank canvases, animation clipping, or vector distortions in print.
         * @param {HTMLElement} rootElement 
         * @returns {Array<{canvas: HTMLCanvasElement, img: HTMLImageElement}>}
         */
        rasterizeCharts: function (rootElement) {
            rootElement = rootElement || document.body;
            const replacements = [];
            const canvases = rootElement.querySelectorAll('canvas');

            canvases.forEach(canvas => {
                // If canvas is already hidden or zero dimension, skip
                if (!canvas.offsetWidth || !canvas.offsetHeight) return;

                try {
                    // If Chart.js instance is attached, freeze animation
                    if (window.Chart && typeof window.Chart.getChart === 'function') {
                        const chart = window.Chart.getChart(canvas);
                        if (chart) {
                            chart.stop();
                            chart.render();
                        }
                    }

                    const dataUrl = canvas.toDataURL('image/png', 2.0);
                    const img = document.createElement('img');
                    img.src = dataUrl;
                    img.className = 'rhu-print-chart-replacement';
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '280px';
                    img.style.height = 'auto';
                    img.style.display = 'block';
                    img.style.margin = '0 auto';
                    img.alt = 'Chart graphic';

                    // Insert image and hide canvas
                    canvas.parentNode.insertBefore(img, canvas);
                    canvas.setAttribute('data-print-original-display', canvas.style.display || '');
                    canvas.style.display = 'none';

                    replacements.push({ canvas, img });
                } catch (e) {
                    console.warn('[RHU MIS Print] Failed to rasterize canvas chart:', e);
                }
            });

            return replacements;
        },

        /**
         * Restores canvases and removes temporary raster images after print dialog closes.
         * @param {Array<{canvas: HTMLCanvasElement, img: HTMLImageElement}>} replacements 
         */
        restoreCharts: function (replacements) {
            if (!replacements || !replacements.length) return;
            replacements.forEach(item => {
                if (item.img && item.img.parentNode) {
                    item.img.parentNode.removeChild(item.img);
                }
                if (item.canvas) {
                    item.canvas.style.display = item.canvas.getAttribute('data-print-original-display') || '';
                    item.canvas.removeAttribute('data-print-original-display');
                }
            });
        },

        /**
         * Waits for all web fonts and images in rootElement to finish loading.
         * @param {HTMLElement} rootElement 
         * @returns {Promise<void>}
         */
        waitForAssets: function (rootElement) {
            rootElement = rootElement || document;
            const promises = [];

            // 1. Web Fonts
            if (document.fonts && document.fonts.ready) {
                promises.push(document.fonts.ready);
            }

            // 2. Images
            const images = rootElement.querySelectorAll('img');
            images.forEach(img => {
                if (!img.complete) {
                    promises.push(new Promise(resolve => {
                        img.onload = img.onerror = resolve;
                        // 1.5s fallback timeout per image
                        setTimeout(resolve, 1500);
                    }));
                }
            });

            return Promise.all(promises);
        }
    };

    /**
     * Primary Print Trigger: Prepares DOM, finalizes charts, sets paper size, and opens print dialog.
     * @param {Object} options
     * @param {'auto'|'a4'|'letter'} [options.paperSize='auto']
     * @param {string|HTMLElement} [options.target]
     */
    window.printReport = async function (options) {
        options = options || {};
        const paperSize = options.paperSize || window.RHUPrint.selectedPaperSize || 'auto';
        window.RHUPrint.setPaperSize(paperSize);

        const targetEl = typeof options.target === 'string' 
            ? document.querySelector(options.target) 
            : (options.target || document.body);

        // Wait for fonts & images
        await window.RHUPrint.waitForAssets(targetEl);

        // Convert canvases to sharp static images
        const replacements = window.RHUPrint.rasterizeCharts(targetEl);

        // Clean up charts after print completes
        const cleanup = function () {
            window.RHUPrint.restoreCharts(replacements);
            window.removeEventListener('afterprint', cleanup);
        };
        window.addEventListener('afterprint', cleanup, { once: true });

        // Minor tick to allow DOM repaint
        setTimeout(function () {
            try {
                window.print();
            } finally {
                // Safari fallback if afterprint doesn't fire
                setTimeout(cleanup, 2000);
            }
        }, 80);
    };

    /**
     * Prints isolated markup or an element inside a clean, hidden iframe.
     * Prevents any parent application styles, sidebars, or themes from leaking into the print output.
     * @param {HTMLElement|string} content
     * @param {Object} options
     */
    window.printIsolated = async function (content, options) {
        options = options || {};
        const paperSize = options.paperSize || 'auto';
        const title = options.title || 'RHU MIS Official Report';

        let htmlBody = '';
        if (typeof content === 'string') {
            htmlBody = content;
        } else if (content && content.outerHTML) {
            htmlBody = content.outerHTML;
        } else {
            console.error('[RHU MIS Print] Invalid content passed to printIsolated');
            return;
        }

        // Create isolated iframe
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        iframe.id = 'rhu-print-isolated-frame';
        document.body.appendChild(iframe);

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write(`
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>${title}</title>
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="/css/print.css">
                <style>
                    @page {
                        size: ${paperSize === 'a4' ? 'A4 portrait' : (paperSize === 'letter' ? 'letter portrait' : 'auto')} !important;
                        margin: 12mm 14mm 16mm 14mm !important;
                    }
                </style>
            </head>
            <body class="rhu-print-isolated">
                ${htmlBody}
            </body>
            </html>
        `);
        doc.close();

        // Wait for iframe resources
        await window.RHUPrint.waitForAssets(doc);

        setTimeout(function () {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
            // Remove iframe after print dialog completes
            setTimeout(function () {
                if (iframe.parentNode) {
                    iframe.parentNode.removeChild(iframe);
                }
            }, 3000);
        }, 150);
    };

    // Auto-bind toolbar elements if on a preview page
    document.addEventListener('DOMContentLoaded', function () {
        const paperSelect = document.getElementById('rhu-paper-size-select');
        if (paperSelect) {
            paperSelect.addEventListener('change', function () {
                window.RHUPrint.selectedPaperSize = this.value;
                window.RHUPrint.setPaperSize(this.value);
            });
        }

        const grayscaleToggle = document.getElementById('rhu-grayscale-toggle');
        if (grayscaleToggle) {
            grayscaleToggle.addEventListener('change', function () {
                const sheet = document.querySelector('.rhu-paper-sheet');
                if (sheet) {
                    sheet.classList.toggle('preview-grayscale', this.checked);
                }
            });
        }
    });

})();
