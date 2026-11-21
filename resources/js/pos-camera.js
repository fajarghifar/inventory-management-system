import { Html5Qrcode, Html5QrcodeSupportedFormats } from "html5-qrcode";

// Export globally for Alpine.js
window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeSupportedFormats = Html5QrcodeSupportedFormats;

class PosCameraScanner {
    constructor() {
        this.html5QrCode = null;
        this.isScanning = false;
        this.torchOn = false;
        this.hasTorch = false;
        this.lastScannedCode = null;
        this.lastScanTime = 0;
    }

    async start(elementId, onScanSuccess, onScanError) {
        if (this.isScanning) {
            await this.stop();
        }

        // Configure supported retail formats (1D retail barcodes + 2D codes)
        const formatsToSupport = [
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93,
            Html5QrcodeSupportedFormats.CODABAR,
            Html5QrcodeSupportedFormats.ITF,
            Html5QrcodeSupportedFormats.QR_CODE,
            Html5QrcodeSupportedFormats.DATA_MATRIX,
        ];

        this.html5QrCode = new Html5Qrcode(elementId, {
            formatsToSupport: formatsToSupport,
            verbose: false,
            experimentalFeatures: {
                // Use native device BarcodeDetector API for 60fps hardware-accelerated scanning on mobile
                useBarCodeDetectorIfSupported: true,
            },
        });

        const handleSuccess = (decodedText, decodedResult) => {
            if (!decodedText) return;
            const cleanCode = decodedText.trim();
            const now = Date.now();

            // Throttle duplicate reads of the exact same code within 1.5 seconds
            if (this.lastScannedCode === cleanCode && (now - this.lastScanTime) < 1500) {
                return;
            }
            this.lastScannedCode = cleanCode;
            this.lastScanTime = now;

            // Haptic vibration feedback on mobile
            if (navigator.vibrate) {
                try { navigator.vibrate(50); } catch (e) {}
            }

            if (typeof onScanSuccess === "function") {
                onScanSuccess(cleanCode, decodedResult);
            }
        };

        const config = {
            fps: 20, // 20 frames per second for smooth scanning
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                // Wide, generous active scan zone so user doesn't have to align perfectly
                const width = Math.floor(viewfinderWidth * 0.92);
                const height = Math.floor(Math.min(260, viewfinderHeight * 0.75));
                return { width: width, height: height };
            },
            aspectRatio: 1.0,
            videoConstraints: {
                facingMode: { ideal: "environment" },
                focusMode: { ideal: "continuous" },
            },
        };

        try {
            await this.html5QrCode.start(
                { facingMode: "environment" },
                config,
                handleSuccess,
                (errorMessage) => {
                    if (typeof onScanError === "function") {
                        onScanError(errorMessage);
                    }
                }
            );

            this.isScanning = true;

            // Check if torch/flashlight is supported
            try {
                const capabilities = this.html5QrCode.getRunningTrackCapabilities();
                this.hasTorch = Boolean(capabilities && capabilities.torch);
            } catch (e) {
                this.hasTorch = false;
            }

            return true;
        } catch (err) {
            console.warn("[POS Camera] Start error:", err);
            this.isScanning = false;
            throw err;
        }
    }

    async toggleTorch() {
        if (!this.html5QrCode || !this.isScanning) return false;
        try {
            this.torchOn = !this.torchOn;
            await this.html5QrCode.applyVideoConstraints({
                advanced: [{ torch: this.torchOn }]
            });
            return this.torchOn;
        } catch (err) {
            console.warn("[POS Camera] Torch error:", err);
            this.torchOn = false;
            return false;
        }
    }

    async stop() {
        if (this.html5QrCode && this.isScanning) {
            try {
                await this.html5QrCode.stop();
            } catch (err) {
                console.warn("[POS Camera] Stop error:", err);
            }
            try {
                this.html5QrCode.clear();
            } catch (e) {}
        }
        this.isScanning = false;
        this.torchOn = false;
        this.lastScannedCode = null;
    }
}

window.posCamera = new PosCameraScanner();
