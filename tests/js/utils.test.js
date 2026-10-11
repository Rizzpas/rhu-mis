const SecureImageValidator = require('../../public/js/secure-image-validator.js');
require('../../public/js/print-helper.js');

describe('Utility Functions', () => {
  describe('SecureImageValidator (public/js/secure-image-validator.js)', () => {
    // Helper to create a fake File / Blob with byte slice simulation
    function createMockFile(name, type, bytes = [0xFF, 0xD8, 0xFF, 0xE0, 0x00, 0x10, 0x4A, 0x46, 0x49, 0x46, 0x00, 0x01]) {
      const buffer = new Uint8Array(bytes).buffer;
      const file = new File([buffer], name, { type });
      // Ensure file.slice works in JSDOM environment
      file.slice = function (start, end) {
        return new Blob([new Uint8Array(bytes).subarray(start, end)], { type });
      };
      return file;
    }

    it('accepts valid JPEG image file with valid magic bytes (FF D8 FF)', async () => {
      const jpegBytes = [0xFF, 0xD8, 0xFF, 0xE0, 0x00, 0x10, 0x4A, 0x46, 0x49, 0x46, 0x00, 0x01];
      const file = createMockFile('photo.jpg', 'image/jpeg', jpegBytes);

      const result = await SecureImageValidator.validateFile(file);
      expect(result.valid).toBe(true);
      expect(result.message).toBe('');
    });

    it('accepts valid PNG image file with valid magic bytes (89 50 4E 47 0D 0A 1A 0A)', async () => {
      const pngBytes = [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A, 0x00, 0x00, 0x00, 0x0D];
      const file = createMockFile('chart.png', 'image/png', pngBytes);

      const result = await SecureImageValidator.validateFile(file);
      expect(result.valid).toBe(true);
    });

    it('accepts valid WebP image file with valid RIFF...WEBP signature', async () => {
      // 0x52 0x49 0x46 0x46 (RIFF), 4 bytes length, 0x57 0x45 0x42 0x50 (WEBP)
      const webpBytes = [
        0x52, 0x49, 0x46, 0x46, 0x00, 0x00, 0x00, 0x00,
        0x57, 0x45, 0x42, 0x50,
      ];
      const file = createMockFile('banner.webp', 'image/webp', webpBytes);

      const result = await SecureImageValidator.validateFile(file);
      expect(result.valid).toBe(true);
    });

    it('rejects files without an extension', async () => {
      const file = createMockFile('avatar', 'image/jpeg');
      const result = await SecureImageValidator.validateFile(file);

      expect(result.valid).toBe(false);
      expect(result.message).toContain('Invalid file type');
    });

    it('rejects files with double extensions (e.g. image.png.exe)', async () => {
      const file = createMockFile('invoice.pdf.jpg', 'image/jpeg');
      const result = await SecureImageValidator.validateFile(file);

      expect(result.valid).toBe(false);
      expect(result.message).toContain('Invalid file type');
    });

    it('rejects disallowed file extensions (e.g. .exe, .sh, .gif, .pdf)', async () => {
      const pdfFile = createMockFile('document.pdf', 'application/pdf');
      const resultPdf = await SecureImageValidator.validateFile(pdfFile);
      expect(resultPdf.valid).toBe(false);

      const gifFile = createMockFile('animation.gif', 'image/gif');
      const resultGif = await SecureImageValidator.validateFile(gifFile);
      expect(resultGif.valid).toBe(false);
    });

    it('rejects spoofed files whose magic bytes do not match the extension', async () => {
      // File named .jpg but contains non-JPEG bytes (e.g., zeroed or random)
      const corruptedBytes = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
      const fakeFile = createMockFile('fake.jpg', 'image/jpeg', corruptedBytes);

      const result = await SecureImageValidator.validateFile(fakeFile);
      expect(result.valid).toBe(false);
      expect(result.message).toContain('Invalid file type');
    });

    it('handles validateFiles for batch uploads', async () => {
      const validJpeg = createMockFile('photo1.jpg', 'image/jpeg', [0xFF, 0xD8, 0xFF, 0x00]);
      const invalidPdf = createMockFile('bad.pdf', 'application/pdf');

      // Both valid and invalid
      const batchResult = await SecureImageValidator.validateFiles([validJpeg, invalidPdf]);
      expect(batchResult.valid).toBe(false);
      expect(batchResult.validFiles.length).toBe(1);
      expect(batchResult.rejectedFiles.length).toBe(1);
      expect(batchResult.rejectedNames).toContain('bad.pdf');

      // Empty list
      const emptyResult = await SecureImageValidator.validateFiles([]);
      expect(emptyResult.valid).toBe(true);
      expect(emptyResult.validFiles).toEqual([]);
    });

    it('initializes input attributes in autoInit', () => {
      const input = document.createElement('input');
      input.type = 'file';
      document.body.appendChild(input);

      SecureImageValidator.autoInit();
      expect(input.getAttribute('accept')).toContain('.jpg');
      expect(input.getAttribute('accept')).toContain('image/png');
    });
  });

  describe('RHUPrint Helper (public/js/print-helper.js)', () => {
    beforeEach(() => {
      document.head.innerHTML = '';
      document.body.innerHTML = '';
      window.print = jest.fn();
    });

    it('exposes window.RHUPrint and global print methods', () => {
      expect(window.RHUPrint).toBeDefined();
      expect(typeof window.printReport).toBe('function');
      expect(typeof window.printIsolated).toBe('function');
    });

    it('sets paper size and injects dynamic @page style tag', () => {
      window.RHUPrint.setPaperSize('a4');
      let styleTag = document.getElementById('rhu-print-page-style');
      expect(styleTag).not.toBeNull();
      expect(styleTag.textContent).toContain('size: A4 portrait');

      window.RHUPrint.setPaperSize('letter');
      expect(styleTag.textContent).toContain('size: letter portrait');

      window.RHUPrint.setPaperSize('auto');
      expect(styleTag.textContent).toContain('size: auto');
    });

    it('updates .rhu-paper-sheet preview-letter class on paper size change', () => {
      const sheet = document.createElement('div');
      sheet.className = 'rhu-paper-sheet';
      document.body.appendChild(sheet);

      window.RHUPrint.setPaperSize('letter');
      expect(sheet.classList.contains('preview-letter')).toBe(true);

      window.RHUPrint.setPaperSize('a4');
      expect(sheet.classList.contains('preview-letter')).toBe(false);
    });

    it('rasterizes canvas charts to replacement img elements and restores them', () => {
      const container = document.createElement('div');
      const canvas = document.createElement('canvas');
      Object.defineProperty(canvas, 'offsetWidth', { value: 300, configurable: true });
      Object.defineProperty(canvas, 'offsetHeight', { value: 150, configurable: true });
      container.appendChild(canvas);
      document.body.appendChild(container);

      // Rasterize
      const replacements = window.RHUPrint.rasterizeCharts(container);
      expect(replacements.length).toBe(1);

      const replacementImg = container.querySelector('img.rhu-print-chart-replacement');
      expect(replacementImg).not.toBeNull();
      expect(replacementImg.src).toContain('data:image/png;base64');
      expect(canvas.style.display).toBe('none');

      // Restore
      window.RHUPrint.restoreCharts(replacements);
      expect(container.querySelector('img.rhu-print-chart-replacement')).toBeNull();
      expect(canvas.style.display).toBe('');
    });

    it('waits for images to load before printing', async () => {
      const container = document.createElement('div');
      const img = document.createElement('img');
      Object.defineProperty(img, 'complete', { value: false, configurable: true });
      container.appendChild(img);
      document.body.appendChild(container);

      const waitPromise = window.RHUPrint.waitForAssets(container);

      // Trigger load event
      img.onload();

      await expect(waitPromise).resolves.toBeDefined();
    });

    it('invokes window.print in printReport workflow', async () => {
      jest.useFakeTimers();

      const printPromise = window.printReport({ paperSize: 'a4' });
      // Advance timers to trigger setTimeout(..., 80)
      await jest.advanceTimersByTimeAsync(150);
      await printPromise;

      expect(window.print).toHaveBeenCalled();
      jest.useRealTimers();
    });

    it('creates an isolated iframe and initiates print in printIsolated', async () => {
      jest.useFakeTimers();
      const content = '<div id="test-report"><h1>Official Clinic Report</h1></div>';
      
      const promise = window.printIsolated(content, { paperSize: 'letter', title: 'Test Report' });
      
      const iframe = document.getElementById('rhu-print-isolated-frame');
      expect(iframe).not.toBeNull();
      expect(iframe.style.position).toBe('fixed');
      expect(iframe.style.width).toBe('0px');
      expect(iframe.style.border).toBe('0px');

      if (iframe.contentWindow) {
        iframe.contentWindow.print = jest.fn();
        iframe.contentWindow.focus = jest.fn();
      }

      await jest.advanceTimersByTimeAsync(200);
      await promise;
      jest.useRealTimers();
    });
  });
});
