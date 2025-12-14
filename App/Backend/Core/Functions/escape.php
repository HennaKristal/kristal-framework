<?php

function esc_html($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function esc_url($value): string
{
    $url = (string) $value;
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
    
    if (preg_match('/^\s*(javascript|vbscript):/i', $url))
    {
        return '#';
    }

    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}

function esc_js($value): string
{
    return json_encode($value, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

function esc_css($value): string
{
    return preg_replace('/[^a-zA-Z0-9\-\_\#\(\)\.\s]/', '', (string) $value);
}
