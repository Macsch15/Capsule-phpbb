<?php
if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    'PLURAL_RULE' => 9,
    'CAPSULE_TITLE' => 'Hosting grafiki',
    'CAPSULE_MAXIMUM_SIZE' => 'Maksymalny rozmiar:',
    'CAPSULE_ALLOWED_EXTENSIONS' => 'Dozwolone rozszerzenia:',
    'CAPSULE_BUTTON_SEND' => 'Wyślij grafikę',
    'CAPSULE_EMPTY_DB' => 'Aktualnie nie masz jeszcze żadnych grafik.',
    'CAPSULE_ERROR_MIME' => 'Transfer zakończył się niepowodzeniem. Niedozwolone rozszerzenie pliku.',
    'CAPSULE_ERROR_SIZE' => 'Transfer zakończył się niepowodzeniem. Plik jest za duży.',
    'CAPSULE_ERROR_GENERAL' => 'Transfer zakończył się niepowodzeniem. Spróbuj ponownie.',
    'CAPSULE_ERROR_FILE_TOO_LARGE' => 'Wysłany plik jest za duży',
    'CAPSULE_ERROR_PARTIAL' => 'Wysyłanie pliku nie powiodło się',
    'CAPSULE_ERROR_NO_FILE' => 'Nie wysłano pliku',
    'CAPSULE_ERROR_INTERNAL' => 'Błąd wewnętrzny',
    'CAPSULE_IMAGES' => [
        1 => '%d grafika',
        2 => '%d grafiki',
        3 => '%d grafik'
    ],
    'CAPSULE_DONE' => '<strong>Gotowe!</strong> Wysłana grafika jest gotowa do udostępnienia.',
    'CAPSULE_DIRECT_LINK' => 'Bezpośredni link:',
    'CAPSULE_BBCODE_LINK' => 'BBCode:',
    'CAPSULE_THUMB_TITLE' => 'Miniaturka',
    'CAPSULE_NAME' => 'Nazwa pliku',
    'CAPSULE_TIME' => 'Wysłany',
    'CAPSULE_SIZE' => 'Rozmiar',
    'CAPSULE_SHOW_HIDE_BBCODES' => 'Pokaż/ukryj kody bbcode'
));