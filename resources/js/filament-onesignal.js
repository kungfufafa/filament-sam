const configElement = document.getElementById('one-signal-filament-config');

if (configElement) {
    window.OneSignalDeferred = window.OneSignalDeferred || [];

    const request = async (url, method, subscriptionId) => {
        if (!subscriptionId) {
            return;
        }

        await fetch(url, {
            method,
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': configElement.dataset.csrfToken,
            },
            body: JSON.stringify({
                subscription_id: subscriptionId,
            }),
        });
    };

    const storeSubscription = (subscriptionId) => request(
        configElement.dataset.storeUrl,
        'POST',
        subscriptionId,
    );

    const destroySubscription = (subscriptionId) => request(
        configElement.dataset.destroyUrl,
        'DELETE',
        subscriptionId,
    );

    window.OneSignalDeferred.push(async (OneSignal) => {
        const options = {
            appId: configElement.dataset.appId,
            allowLocalhostAsSecureOrigin: true,
            notifyButton: {
                enable: true,
            },
        };

        if (configElement.dataset.safariWebId) {
            options.safari_web_id = configElement.dataset.safariWebId;
        }

        await OneSignal.init(options);

        const synchronizeSubscription = async (subscriptionId) => {
            try {
                await storeSubscription(subscriptionId);
            } catch (error) {
                console.warn('OneSignal subscription could not be synchronized.', error);
            }
        };

        OneSignal.User.PushSubscription.addEventListener('change', (event) => {
            void synchronizeSubscription(event.current.id);
        });

        await OneSignal.login(configElement.dataset.externalId);
        await synchronizeSubscription(OneSignal.User.PushSubscription.id);
    });

    const runOneSignalLogout = () => new Promise((resolve) => {
        let completed = false;

        const finish = () => {
            if (completed) {
                return;
            }

            completed = true;
            resolve();
        };

        window.setTimeout(finish, 2000);

        window.OneSignalDeferred.push(async (OneSignal) => {
            try {
                await destroySubscription(OneSignal.User.PushSubscription.id);
            } catch (error) {
                console.warn('OneSignal subscription could not be removed.', error);
            }

            try {
                await OneSignal.logout();
            } finally {
                finish();
            }
        });
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target;

        if (
            !(form instanceof HTMLFormElement)
            || form.action !== configElement.dataset.logoutUrl
            || form.dataset.oneSignalLogoutCompleted === 'true'
        ) {
            return;
        }

        event.preventDefault();
        await runOneSignalLogout();
        form.dataset.oneSignalLogoutCompleted = 'true';
        form.submit();
    });
}
