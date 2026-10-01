/**
 * Secure Image Upload Validator
 * 
 * Validates images client-side before upload:
 * - Allowed extensions: JPEG, JPG, PNG, WebP (case-insensitive)
 * - Allowed MIME types: image/jpeg, image/png, image/webp
 * - Verifies magic bytes / file signatures via FileReader
 * - Rejects GIF, BMP, SVG, TIFF, HEIC, PDF, files without extensions, and double extensions (e.g. image.png.exe)
 * - Provides consistent error messaging: "Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted."
 * - Handles both single and multiple file selections and drag-and-drop.
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.SecureImageValidator = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    const ERROR_MESSAGE = 'Invalid file type. Only JPEG, JPG, PNG, and WebP images are accepted.';
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    const ALLOWED_MIMES = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/webp', 'image/x-webp'];
    const FORBIDDEN_MIMES = ['image/gif', 'image/bmp', 'image/svg+xml', 'image/tiff', 'image/heic', 'image/heif', 'application/pdf'];
    const ACCEPT_ATTRIBUTE = '.jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp';

    /**
     * Reads first N bytes of a file asynchronously as Uint8Array
     */
    function readFirstBytes(file, byteCount = 12) {
        return new Promise((resolve, reject) => {
            if (!file || typeof file.slice !== 'function') {
                return resolve(null);
            }
            const reader = new FileReader();
            const slice = file.slice(0, byteCount);

            reader.onload = function (e) {
                if (e.target && e.target.result) {
                    resolve(new Uint8Array(e.target.result));
                } else {
                    resolve(null);
                }
            };
            reader.onerror = function () {
                resolve(null);
            };
            reader.readAsArrayBuffer(slice);
        });
    }

    /**
     * Check if bytes match JPEG signature (FF D8 FF)
     */
    function isJpegBytes(bytes) {
        return bytes && bytes.length >= 3 &&
            bytes[0] === 0xFF && bytes[1] === 0xD8 && bytes[2] === 0xFF;
    }

    /**
     * Check if bytes match PNG signature (89 50 4E 47 0D 0A 1A 0A)
     */
    function isPngBytes(bytes) {
        return bytes && bytes.length >= 8 &&
            bytes[0] === 0x89 && bytes[1] === 0x50 && bytes[2] === 0x4E && bytes[3] === 0x47 &&
            bytes[4] === 0x0D && bytes[5] === 0x0A && bytes[6] === 0x1A && bytes[7] === 0x0A;
    }

    /**
     * Check if bytes match WebP signature (RIFF....WEBP)
     */
    function isWebpBytes(bytes) {
        if (!bytes || bytes.length < 12) return false;
        // Check RIFF
        const isRiff = bytes[0] === 0x52 && bytes[1] === 0x49 && bytes[2] === 0x46 && bytes[3] === 0x46;
        // Check WEBP at offset 8
        const isWebp = bytes[8] === 0x57 && bytes[9] === 0x45 && bytes[10] === 0x42 && bytes[11] === 0x50;
        return isRiff && isWebp;
    }

    /**
     * Validate a single file object
     * @param {File} file 
     * @returns {Promise<{valid: boolean, message: string, rejectedName?: string}>}
     */
    async function validateFile(file) {
        if (!file || !(file instanceof File || file instanceof Blob)) {
            return { valid: false, message: ERROR_MESSAGE, rejectedName: file?.name || 'unknown' };
        }

        const fileName = file.name || '';

        // 1. Must have a filename and extension
        if (!fileName || !fileName.includes('.')) {
            return { valid: false, message: ERROR_MESSAGE, rejectedName: fileName || 'file_without_name' };
        }

        // 2. Reject double extensions (e.g. image.png.exe, shell.php.jpg, photo.jpg.png)
        // A single dot before extension is standard; multiple dots indicate potential double extension attack
        const dotParts = fileName.split('.');
        if (dotParts.length > 2) {
            return { valid: false, message: ERROR_MESSAGE, rejectedName: fileName };
        }

        const extension = (dotParts.pop() || '').toLowerCase().trim();

        // Check extension is in allowed list
        if (!ALLOWED_EXTENSIONS.includes(extension)) {
            return { valid: false, message: ERROR_MESSAGE, rejectedName: fileName };
        }

        // 3. MIME type check (if present on client)
        if (file.type) {
            const clientMime = file.type.toLowerCase().trim();
            if (FORBIDDEN_MIMES.includes(clientMime)) {
                return { valid: false, message: ERROR_MESSAGE, rejectedName: fileName };
            }

            // Verify extension and client MIME correspondence if an image MIME is explicitly reported
            if ((extension === 'jpg' || extension === 'jpeg') && clientMime.startsWith('image/') && clientMime !== 'image/jpeg' && clientMime !== 'image/pjpeg') {
                return { valid: false, message: ERROR_MESSAGE, rejectedName: fileName };
            }
            if (extension === 'png' && clientMime.startsWith('image/') && clientMime !== 'image/png' && clientMime !== 'image/x-png') {
                return { valid: false, message: ERROR_MESSAGE, rejectedName: fileName };
            }
            if (extension === 'webp' && clientMime.startsWith('image/') && clientMime !== 'image/webp' && clientMime !== 'image/x-webp') {
                return { valid: false, message: ERROR_MESSAGE, rejectedName: fileName };
            }
        }

        // 4. Magic bytes verification via FileReader
        const bytes = await readFirstBytes(file, 12);
        if (!bytes) {
            return { valid: false, message: ERROR_MESSAGE, rejectedName: fileName };
        }

        let magicBytesValid = false;
        if (extension === 'jpg' || extension === 'jpeg') {
            magicBytesValid = isJpegBytes(bytes);
        } else if (extension === 'png') {
            magicBytesValid = isPngBytes(bytes);
        } else if (extension === 'webp') {
            magicBytesValid = isWebpBytes(bytes);
        }

        if (!magicBytesValid) {
            return { valid: false, message: ERROR_MESSAGE, rejectedName: fileName };
        }

        return { valid: true, message: '', file: file };
    }

    /**
     * Validate multiple files (e.g. FileList or File[])
     * @param {FileList|File[]} files
     * @returns {Promise<{valid: boolean, validFiles: File[], rejectedFiles: File[], rejectedNames: string[], message: string}>}
     */
    async function validateFiles(files) {
        if (!files || files.length === 0) {
            return { valid: true, validFiles: [], rejectedFiles: [], rejectedNames: [], message: '' };
        }

        const fileArray = Array.from(files);
        const validFiles = [];
        const rejectedFiles = [];
        const rejectedNames = [];

        for (const file of fileArray) {
            const res = await validateFile(file);
            if (res.valid) {
                validFiles.push(file);
            } else {
                rejectedFiles.push(file);
                rejectedNames.push(file.name || 'unnamed');
            }
        }

        if (rejectedFiles.length > 0) {
            // Include rejected names if multiple files were uploaded
            let msg = ERROR_MESSAGE;
            if (fileArray.length > 1 && rejectedNames.length > 0) {
                msg = `${ERROR_MESSAGE} (Rejected: ${rejectedNames.join(', ')})`;
            }
            return {
                valid: false,
                validFiles: validFiles,
                rejectedFiles: rejectedFiles,
                rejectedNames: rejectedNames,
                message: msg
            };
        }

        return {
            valid: true,
            validFiles: validFiles,
            rejectedFiles: [],
            rejectedNames: [],
            message: ''
        };
    }

    /**
     * Show an error message near an upload field
     * @param {HTMLElement} inputOrZone
     * @param {string} message
     */
    function showError(inputOrZone, message) {
        if (!inputOrZone) return;

        // Try to find existing error container
        const container = inputOrZone.closest('.upload-zone, .form-group, label, div') || inputOrZone.parentElement;
        if (!container) return;

        let errorEl = container.querySelector('.secure-image-error');
        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'secure-image-error text-rose-600 dark:text-rose-400 text-xs font-semibold mt-2 flex items-center gap-1.5 transition-all';
            errorEl.setAttribute('role', 'alert');

            // Insert after input or container
            if (inputOrZone.nextSibling) {
                inputOrZone.parentNode.insertBefore(errorEl, inputOrZone.nextSibling);
            } else {
                container.appendChild(errorEl);
            }
        }

        errorEl.innerHTML = `
            <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>${message}</span>
        `;
        errorEl.style.display = 'flex';
    }

    /**
     * Clear error message near an upload field
     * @param {HTMLElement} inputOrZone
     */
    function clearError(inputOrZone) {
        if (!inputOrZone) return;
        const container = inputOrZone.closest('.upload-zone, .form-group, label, div') || inputOrZone.parentElement;
        if (!container) return;

        const errorEl = container.querySelector('.secure-image-error');
        if (errorEl) {
            errorEl.innerHTML = '';
            errorEl.style.display = 'none';
        }
    }

    /**
     * Attach automatic validation to an input element
     */
    function attachAutoValidation(input) {
        if (!input || input.dataset.secureValidated) return;
        input.dataset.secureValidated = 'true';

        // Enforce required accept attribute
        input.setAttribute('accept', ACCEPT_ATTRIBUTE);

        input.addEventListener('change', async function (e) {
            if (!this.files || this.files.length === 0) {
                clearError(this);
                return;
            }

            const result = await validateFiles(this.files);
            if (!result.valid) {
                // Reject: clear input so invalid files are not submitted/stored
                this.value = '';
                showError(this, result.message);
                e.stopImmediatePropagation();
                e.preventDefault();
            } else {
                clearError(this);
            }
        }, true); // Capture phase to prevent subsequent handlers from running on invalid files
    }

    /**
     * Auto-init on DOMContentLoaded for all file inputs
     */
    function autoInit() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            // Apply to all file inputs or image inputs
            input.setAttribute('accept', ACCEPT_ATTRIBUTE);
            attachAutoValidation(input);
        });
    }

    if (typeof document !== 'undefined') {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', autoInit);
        } else {
            autoInit();
        }
    }

    return {
        ERROR_MESSAGE,
        ALLOWED_EXTENSIONS,
        ALLOWED_MIMES,
        ACCEPT_ATTRIBUTE,
        validateFile,
        validateFiles,
        showError,
        clearError,
        attachAutoValidation,
        autoInit
    };
}));
