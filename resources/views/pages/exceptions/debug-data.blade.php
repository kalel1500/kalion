@php /** @var array<string, string> $debugData */ @endphp

<section style="position:relative;z-index:2147483647;margin:24px;padding:20px;border:1px solid #ef4444;border-radius:8px;background:#18181b;color:#f4f4f5;font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;">
    <h2 style="margin:0 0 16px;color:#fca5a5;font-size:18px;font-weight:700;">
        Kalion debug data
    </h2>

    @foreach($debugData as $name => $value)
        <details open style="margin-top:12px;">
            <summary style="cursor:pointer;color:#fde68a;font-weight:600;">{{ $name }}</summary>
            <pre style="margin:8px 0 0;overflow:auto;white-space:pre-wrap;word-break:break-word;color:#e4e4e7;">{{ $value }}</pre>
        </details>
    @endforeach
</section>

