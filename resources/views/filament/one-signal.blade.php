@if (filament()->auth()->check() && filled(config('services.one_signal.app_id')))
    <div
        id="one-signal-filament-config"
        hidden
        data-app-id="{{ config('services.one_signal.app_id') }}"
        data-safari-web-id="{{ config('services.one_signal.safari_web_id') }}"
        data-external-id="user:{{ filament()->auth()->id() }}"
        data-store-url="{{ route('one-signal.subscriptions.store') }}"
        data-destroy-url="{{ route('one-signal.subscriptions.destroy') }}"
        data-logout-url="{{ filament()->getLogoutUrl() }}"
        data-csrf-token="{{ csrf_token() }}"
    ></div>

    <script
        src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js"
        defer
    ></script>

    @vite('resources/js/filament-onesignal.js')
@endif
