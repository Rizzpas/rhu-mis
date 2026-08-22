{{-- 
    Image Cropper Modal Partial
    
    Usage: @include('partials.image-cropper')
    
    This partial provides a global Alpine.js component `$store.imageCropper` that can be 
    invoked from any page. It loads Cropper.js via CDN and renders a full-screen modal 
    with crop controls.
    
    To trigger cropping from a dropzone or file input:
    
        $store.imageCropper.open(file, {
            aspectRatio: 1,          // 1 = square, 16/9 = wide, NaN = free
            circular: false,         // show circular mask overlay (cosmetic only)
            onApply(blob, previewUrl) {
                // blob = cropped image Blob
                // previewUrl = object URL for preview
            }
        });
--}}

{{-- Cropper.js CDN --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

{{-- Crop Modal --}}
<div x-data x-show="$store.imageCropper.isOpen" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0"
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center"
     @keydown.escape.window="$store.imageCropper.cancel()">
    
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="$store.imageCropper.cancel()"></div>

    {{-- Modal Panel --}}
    <div x-show="$store.imageCropper.isOpen"
         x-transition:enter="transition ease-out duration-300 delay-75"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-2xl mx-4 overflow-hidden flex flex-col max-h-[90vh]"
         @click.stop>
        
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-teal-500/25">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">Crop Image</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400" x-text="$store.imageCropper.subtitle"></p>
                </div>
            </div>
            <button @click="$store.imageCropper.cancel()" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Ratio Selector Bar (Hidden for avatars) --}}
        <div x-show="!$store.imageCropper.circular" class="flex items-center justify-between px-6 py-2 bg-slate-100 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-xs overflow-x-auto gap-2">
            <span class="font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-[10px] whitespace-nowrap">Aspect Ratio:</span>
            <div class="flex items-center gap-1.5">
                <button type="button" @click="$store.imageCropper.setRatio(NaN)"
                        :class="isNaN($store.imageCropper.currentRatio) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg font-bold text-[11px] transition-all">
                    Free / Custom
                </button>
                <button type="button" @click="$store.imageCropper.selectAll()"
                        class="px-2.5 py-1 rounded-lg font-bold text-[11px] bg-teal-600/10 dark:bg-teal-500/20 text-teal-700 dark:text-teal-300 hover:bg-teal-600 hover:text-white transition-all flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                    Cover Whole Image
                </button>
                <button type="button" @click="$store.imageCropper.setRatio(16/9)"
                        :class="Math.abs($store.imageCropper.currentRatio - 16/9) < 0.01 ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg font-bold text-[11px] transition-all">
                    16:9 Landscape
                </button>
                <button type="button" @click="$store.imageCropper.setRatio(4/5)"
                        :class="Math.abs($store.imageCropper.currentRatio - 4/5) < 0.01 ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg font-bold text-[11px] transition-all">
                    4:5 Poster
                </button>
                <button type="button" @click="$store.imageCropper.setRatio(1)"
                        :class="$store.imageCropper.currentRatio === 1 ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg font-bold text-[11px] transition-all">
                    1:1 Square
                </button>
            </div>
        </div>

        {{-- Crop Area --}}
        <div class="flex-1 overflow-hidden bg-slate-950 relative min-h-[320px] max-h-[55vh]"
             :class="{ 'cropper-circular-mask': $store.imageCropper.circular }">
            <img id="cropper-image" class="max-w-full block" style="display: none;">
        </div>

        {{-- Toolbar --}}
        <div class="flex items-center justify-between px-6 py-3 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-1">
                {{-- Zoom In --}}
                <button type="button" @click="$store.imageCropper.zoom(0.1)" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-white transition-all" title="Zoom In">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                </button>
                {{-- Zoom Out --}}
                <button type="button" @click="$store.imageCropper.zoom(-0.1)" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-white transition-all" title="Zoom Out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg>
                </button>
                
                <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>
                
                {{-- Rotate Left --}}
                <button type="button" @click="$store.imageCropper.rotate(-90)" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-white transition-all" title="Rotate Left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l4 4M3 10l4-4"></path></svg>
                </button>
                {{-- Rotate Right --}}
                <button type="button" @click="$store.imageCropper.rotate(90)" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-white transition-all" title="Rotate Right">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4 4m4-4l-4-4"></path></svg>
                </button>
                
                <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>

                {{-- Flip Horizontal --}}
                <button type="button" @click="$store.imageCropper.flipH()" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-white transition-all" title="Flip Horizontal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                </button>
                {{-- Flip Vertical --}}
                <button type="button" @click="$store.imageCropper.flipV()" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-white transition-all" title="Flip Vertical">
                    <svg class="w-5 h-5 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                </button>
                
                <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>

                {{-- Reset --}}
                <button type="button" @click="$store.imageCropper.reset()" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-white transition-all" title="Reset">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>

            <p class="text-[11px] text-slate-400 hidden sm:block">Drag edges or corners to adjust</p>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
            <button type="button" @click="$store.imageCropper.cancel()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition-all">
                Cancel
            </button>
            <button type="button" @click="$store.imageCropper.apply()" class="px-6 py-2.5 rounded-xl text-sm font-bold bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-lg shadow-teal-600/25 hover:shadow-teal-600/40 hover:-translate-y-0.5 transition-all active:scale-95">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Apply Crop
                </span>
            </button>
        </div>
    </div>
</div>

{{-- Circular mask CSS for avatar cropping --}}
<style>
    .cropper-circular-mask .cropper-view-box,
    .cropper-circular-mask .cropper-face {
        border-radius: 50%;
    }
    .cropper-circular-mask .cropper-view-box {
        outline: 0;
        box-shadow: 0 0 0 1px rgba(20, 184, 166, 0.8);
    }
    /* Make crop area background darker for better visibility */
    .cropper-modal {
        background-color: rgba(15, 23, 42, 0.75) !important;
    }
    /* Style the dashed crop border */
    .cropper-dashed {
        border-color: rgba(20, 184, 166, 0.5) !important;
    }
    .cropper-point {
        background-color: rgb(20, 184, 166) !important;
    }
    .cropper-line {
        background-color: rgba(20, 184, 166, 0.4) !important;
    }
</style>

{{-- Alpine Store for Image Cropper --}}
<script>
(function() {
    function registerImageCropperStore() {
        if (!window.Alpine || typeof Alpine.store !== 'function') return;
        if (Alpine.store('imageCropper')) return; // already registered

        Alpine.store('imageCropper', {
            isOpen: false,
            circular: false,
            subtitle: '',
            currentRatio: NaN,
            _cropper: null,
            _onApply: null,
            _flipX: 1,
            _flipY: 1,

            /**
             * Open the crop modal with a file.
             */
            open(file, options = {}) {
                const { aspectRatio = NaN, circular = false, subtitle = '', onApply = null } = options;
                
                this.circular = circular;
                this.currentRatio = aspectRatio;
                this.subtitle = subtitle || this._getSubtitle(aspectRatio);
                this._onApply = onApply;
                this._flipX = 1;
                this._flipY = 1;

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.isOpen = true;

                    // Wait for modal animation and DOM rendering
                    setTimeout(() => {
                        const img = document.getElementById('cropper-image');
                        if (!img) return;
                        img.src = e.target.result;
                        img.style.display = 'block';

                        // Destroy previous cropper instance if any
                        if (this._cropper) {
                            this._cropper.destroy();
                            this._cropper = null;
                        }

                        if (typeof Cropper !== 'undefined') {
                            this._cropper = new Cropper(img, {
                                aspectRatio: aspectRatio,
                                viewMode: 1,
                                dragMode: 'move',
                                autoCropArea: 0.95,
                                restore: false,
                                guides: true,
                                center: true,
                                highlight: false,
                                cropBoxMovable: true,
                                cropBoxResizable: true,
                                toggleDragModeOnDblclick: true,
                                responsive: true,
                                background: true,
                            });
                        } else {
                            console.error('Cropper.js library is not yet loaded.');
                        }
                    }, 120);
                };
                reader.readAsDataURL(file);
            },

            setRatio(ratio) {
                this.currentRatio = ratio;
                this.subtitle = this._getSubtitle(ratio);
                if (this._cropper) {
                    this._cropper.setAspectRatio(ratio);
                }
            },

            selectAll() {
                this.currentRatio = NaN;
                this.subtitle = 'Full image selection';
                if (this._cropper) {
                    this._cropper.setAspectRatio(NaN);
                    const canvasData = this._cropper.getCanvasData();
                    this._cropper.setCropBoxData({
                        left: canvasData.left,
                        top: canvasData.top,
                        width: canvasData.width,
                        height: canvasData.height
                    });
                }
            },

            /**
             * Apply the crop and invoke callback
             */
            apply() {
                if (!this._cropper) return;

                const canvas = this._cropper.getCroppedCanvas({
                    maxWidth: 2048,
                    maxHeight: 2048,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                if (canvas) {
                    canvas.toBlob((blob) => {
                        if (blob && this._onApply) {
                            const previewUrl = URL.createObjectURL(blob);
                            this._onApply(blob, previewUrl);
                        }
                        this._destroy();
                    }, 'image/jpeg', 0.92);
                } else {
                    this._destroy();
                }
            },

            /**
             * Cancel without applying
             */
            cancel() {
                this._destroy();
            },

            zoom(ratio) {
                if (this._cropper) this._cropper.zoom(ratio);
            },

            rotate(deg) {
                if (this._cropper) this._cropper.rotate(deg);
            },

            flipH() {
                if (!this._cropper) return;
                this._flipX = this._flipX * -1;
                this._cropper.scaleX(this._flipX);
            },

            flipV() {
                if (!this._cropper) return;
                this._flipY = this._flipY * -1;
                this._cropper.scaleY(this._flipY);
            },

            reset() {
                if (this._cropper) {
                    this._cropper.reset();
                    this._flipX = 1;
                    this._flipY = 1;
                }
            },

            _destroy() {
                if (this._cropper) {
                    this._cropper.destroy();
                    this._cropper = null;
                }
                const img = document.getElementById('cropper-image');
                if (img) {
                    img.src = '';
                    img.style.display = 'none';
                }
                this.isOpen = false;
                this._onApply = null;
            },

            _getSubtitle(ratio) {
                if (isNaN(ratio)) return 'Free crop — drag edges or click "Cover Whole Image"';
                if (ratio === 1) return 'Square crop (1:1)';
                if (Math.abs(ratio - 16/9) < 0.01) return 'Landscape crop (16:9)';
                if (Math.abs(ratio - 4/5) < 0.01) return 'Portrait poster (4:5)';
                return 'Custom crop ratio';
            }
        });
    }

    if (window.Alpine) {
        registerImageCropperStore();
    }
    document.addEventListener('alpine:init', registerImageCropperStore);
    document.addEventListener('DOMContentLoaded', registerImageCropperStore);
})();

/**
 * Global function to open the image cropper from any JS function or script.
 */
window.openImageCropper = function(file, options) {
    if (window.Alpine && typeof Alpine.store === 'function') {
        const store = Alpine.store('imageCropper');
        if (store) {
            store.open(file, options);
            return;
        }
    }
    // If not ready yet, retry briefly
    setTimeout(() => {
        if (window.Alpine && typeof Alpine.store === 'function') {
            const store = Alpine.store('imageCropper');
            if (store) {
                store.open(file, options);
                return;
            }
        }
        console.error('ImageCropper store could not be found.');
    }, 100);
};

/**
 * Helper: Set a cropped File on an input element programmatically.
 */
function setCroppedFile(inputEl, blob, filename) {
    if (!inputEl) return;
    const file = new File([blob], filename || 'cropped.jpg', { type: 'image/jpeg' });
    const dt = new DataTransfer();
    dt.items.add(file);
    inputEl.files = dt.files;
    return file;
}

/**
 * Helper: Set multiple Files on an input element.
 */
function setCroppedFiles(inputEl, filesArray) {
    if (!inputEl) return;
    const dt = new DataTransfer();
    filesArray.forEach(f => dt.items.add(f));
    inputEl.files = dt.files;
}
</script>
