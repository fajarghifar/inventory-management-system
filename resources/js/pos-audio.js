/**
 * POS Audio Feedback Engine using native Web Audio API
 * No external audio files or network requests required.
 */
class PosAudio {
    constructor() {
        this.ctx = null;
        this.muted = localStorage.getItem('pos_sound_muted') === 'true';
    }

    initContext() {
        if (!this.ctx) {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                this.ctx = new AudioCtx();
            }
        }
        if (this.ctx && this.ctx.state === 'suspended') {
            this.ctx.resume();
        }
    }

    isMuted() {
        return this.muted;
    }

    toggleMute() {
        this.muted = !this.muted;
        localStorage.setItem('pos_sound_muted', this.muted ? 'true' : 'false');
        return this.muted;
    }

    /**
     * Standard POS barcode scanner beep (880Hz sine wave, 70ms)
     */
    playBeep() {
        if (this.muted) return;
        try {
            this.initContext();
            if (!this.ctx) return;

            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, this.ctx.currentTime); // A5

            gain.gain.setValueAtTime(0.15, this.ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + 0.08);

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start();
            osc.stop(this.ctx.currentTime + 0.08);
        } catch (e) {
            console.debug('POS Audio Beep not supported:', e);
        }
    }

    /**
     * Cash register bell / completion chime
     */
    playCashChime() {
        if (this.muted) return;
        try {
            this.initContext();
            if (!this.ctx) return;

            const now = this.ctx.currentTime;

            // First tone
            const osc1 = this.ctx.createOscillator();
            const gain1 = this.ctx.createGain();
            osc1.type = 'triangle';
            osc1.frequency.setValueAtTime(987.77, now); // B5
            gain1.gain.setValueAtTime(0.2, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
            osc1.connect(gain1);
            gain1.connect(this.ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.4);

            // Second higher harmonic
            const osc2 = this.ctx.createOscillator();
            const gain2 = this.ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(1975.53, now + 0.08); // B6
            gain2.gain.setValueAtTime(0.2, now + 0.08);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
            osc2.connect(gain2);
            gain2.connect(this.ctx.destination);
            osc2.start(now + 0.08);
            osc2.stop(now + 0.6);
        } catch (e) {
            console.debug('POS Audio Chime not supported:', e);
        }
    }

    /**
     * Error / out-of-stock warning buzz
     */
    playError() {
        if (this.muted) return;
        try {
            this.initContext();
            if (!this.ctx) return;

            const now = this.ctx.currentTime;
            const osc = this.ctx.createOscillator();
            const gain = this.ctx.createGain();

            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(220, now); // Low A3

            gain.gain.setValueAtTime(0.18, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);

            osc.connect(gain);
            gain.connect(this.ctx.destination);

            osc.start(now);
            osc.stop(now + 0.25);
        } catch (e) {
            console.debug('POS Audio Error not supported:', e);
        }
    }
}

export const posAudio = new PosAudio();
window.posAudio = posAudio;

