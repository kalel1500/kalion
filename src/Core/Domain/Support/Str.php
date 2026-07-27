<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

use Illuminate\Support\Traits\Macroable;

class Str
{
    use Macroable;

    public static function htmlToText(?string $html, bool $preserveParagraphs = false): string
    {
        if (blank($html)) {
            return '';
        }

        $blockTags = [
            'div', 'p', 'li', 'dd', 'dt', 'tr', 'blockquote', 'pre',
            'ul', 'ol', 'table', 'section', 'article', 'header', 'footer',
            'aside', 'nav', 'fieldset', 'address', 'figure', 'figcaption',
            'details', 'summary', 'hr',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        ];
        $tagsPattern = implode('|', array_map('preg_quote', $blockTags));

        // Insertar salto de línea tanto en apertura como en cierre de bloques,
        // y en <br>, para no depender de qué venga justo antes de un bloque.
        $text = preg_replace(
            "/<\/?(?:{$tagsPattern}|br)\b[^>]*>/i",
            "\n",
            $html
        );

        // Quitar el resto de etiquetas y comentarios
        $text = strip_tags($text);

        // Decodificar entidades (&lt;, &amp;, &nbsp;, etc.)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);

        // Normalizar NBSP (\u{A0}) a espacio normal
        $text = str_replace("\u{A0}", ' ', $text);

        // Colapsar espacios repetidos
        $text = preg_replace('/[ \t]+/', ' ', $text);

        // Colapsar saltos de línea repetidos según preferencia
        $text = $preserveParagraphs
            ? preg_replace('/\n{2,}/', "\n", $text)
            : preg_replace('/\n{3,}/', "\n\n", $text);

        // Sin espacios pegados al salto de línea
        $text = preg_replace('/ *\n */', "\n", $text);

        return trim($text);
    }
}
