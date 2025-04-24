import es from './lang/es.json';
import en from './lang/en.json';
import { DefaultTranslations, TranslationReplacements, Translator } from '@kalel1500/kalion-js';

export interface AppTranslations extends DefaultTranslations {
    'test_message': string;
    'test_message_VARIABLE': string;
    'the_tag': string;
}

const translator = Translator.getInstance<AppTranslations>();
translator.registerTranslations('es', es);
translator.registerTranslations('en', en);

export const __ = (key: keyof AppTranslations, replacements?: TranslationReplacements) => translator.get(key, replacements);
