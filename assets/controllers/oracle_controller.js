import { Controller } from '@hotwired/stimulus';

const COVER_TEXT = 'Jack, reveal what I need to know';

export default class extends Controller {
    static targets = ['petition', 'question', 'result', 'secret', 'submit'];

    connect() {
        this.secretAnswer = '';
        this.coverIndex = 0;
        this.recording = false;
    }

    capture(event) {
        if (!event.data || event.inputType === 'deleteContentBackward') {
            return;
        }

        if (event.data === '.') {
            event.preventDefault();
            this.recording = !this.recording;
            return;
        }

        if (!this.recording) {
            return;
        }

        event.preventDefault();
        this.secretAnswer += event.data;
        this.secretTarget.value = this.secretAnswer;
        this.petitionTarget.value += this.nextCoverCharacter();
    }

    sync() {
        if (this.petitionTarget.value.length === 0) {
            this.secretAnswer = '';
            this.secretTarget.value = '';
            this.coverIndex = 0;
            this.recording = false;
        }
    }

    shortcut(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            this.questionTarget.focus();
        }
    }

    submit() {
        this.submitTarget.disabled = true;
        this.submitTarget.textContent = 'Listening';
    }

    finish() {
        this.submitTarget.disabled = false;
        this.submitTarget.textContent = 'Ask';
    }

    reset() {
        this.secretAnswer = '';
        this.secretTarget.value = '';
        this.petitionTarget.value = '';
        this.questionTarget.value = '';
        this.coverIndex = 0;
        this.recording = false;
        this.resultTarget.src = this.resultTarget.dataset.idleUrl;
        this.petitionTarget.focus();
    }

    nextCoverCharacter() {
        const character = COVER_TEXT[this.coverIndex] ?? ' ';
        this.coverIndex += 1;

        return character;
    }
}
