import { Constants, DefaultConstants } from '@kalel1500/kalion-js';

interface AppConstants extends DefaultConstants {
    anotherSetting: string;
}

const constants = Constants.getInstance<AppConstants>();
constants.extend({
    appIcon:                      new URL('/resources/images/favicon.ico', import.meta.url).href,

    VITE_BROADCASTING_ENABLED:    import.meta.env.VITE_BROADCASTING_ENABLED === 'true',

    VITE_REVERB_APP_KEY:          import.meta.env.VITE_REVERB_APP_KEY,
    VITE_REVERB_HOST:             import.meta.env.VITE_REVERB_HOST,
    VITE_REVERB_PORT:             import.meta.env.VITE_REVERB_PORT === undefined ? undefined : parseInt(import.meta.env.VITE_REVERB_PORT),
    VITE_REVERB_SCHEME:           import.meta.env.VITE_REVERB_SCHEME,

    VITE_APP_ENV:                 import.meta.env.VITE_APP_ENV,
    VITE_APP_NAME:                import.meta.env.VITE_APP_NAME,
    VITE_APP_CODE:                import.meta.env.VITE_APP_CODE,

    VITE_TS_STORAGE_VERSION:      import.meta.env.VITE_TS_STORAGE_VERSION,
    VITE_TS_USE_BOOSTRAP_CLASSES: import.meta.env.VITE_TS_USE_BOOSTRAP_CLASSES === 'true',

    anotherSetting:               'newCustomValue',
});

export const _const = <T extends keyof AppConstants>(key: T): AppConstants[T] => constants.get(key);
