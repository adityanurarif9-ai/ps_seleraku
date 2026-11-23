@php($session = $getRecord())
<div style="padding: .7rem 1rem; font-variant-numeric: tabular-nums; font-weight: 600; white-space: nowrap;"
    wire:key="timer-{{ $session->id }}-{{ $session->updated_at->getTimestamp() }}-{{ $session->remaining() }}"
    x-data="{ left: {{ $session->remaining() }}, anchor: Date.now(), initial: {{ $session->remaining() }}, ticker: null,
        init() { if (@js($session->status === 'running')) this.ticker = setInterval(() => { this.left = Math.max(0, this.initial - Math.floor((Date.now() - this.anchor) / 1000)); }, 1000); },
        destroy() { clearInterval(this.ticker); },
        text() { return [Math.floor(this.left/3600), Math.floor(this.left%3600/60), this.left%60].map(v => String(v).padStart(2,'0')).join(':'); }
    }">
    <span x-text="text()">{{ $session->countdown() }}</span>
</div>
