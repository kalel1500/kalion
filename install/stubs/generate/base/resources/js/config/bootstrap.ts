import 'flowbite';
import '@/config/constants';
import '@/config/translations';
import { EnvVariables, Route, UtilitiesServiceProvider } from '@kalel1500/kalion-js';
import { defineRoutes } from '@/config/routes';

// Declare .env variables
declare global {
    interface ImportMeta {
        readonly env: EnvVariables & {
            readonly VITE_OTHER?: string
        };
    }
}

// Definir que acciones ejecutar del paquete
UtilitiesServiceProvider.features(['registerGlobalError', 'enableNotifications', 'startLayoutListeners']);

// Definimos y ejecutamos las rutas de JS
defineRoutes();
Route.dispatch();
