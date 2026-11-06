<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Util;

/**
 * Class StemRussian.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class StemRussian
{
    private string $VERSION = '0.02';
    private bool $unset_predlog = true; // удалять пердлоги ?
    private $Stem_Caching = 0;
    private $Stem_Cache = [];
    private string $VOWEL = '/аеиоуыэюя/u';
    private string $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/u';
    private string $REFLEXIVE = '/(с[яь])$/u';
    private string $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/u';
    private string $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/u';
    private string $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/u';
    private string $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/u';
    private string $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/u';
    private string $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/u';
    private string $PREDLOG = '/(^|\s)(и|для|в|на|под|из|с|по)(\s|$)/u';

    private function s(&$s, string $re, string $to): bool
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);

        return $orig !== $s;
    }

    private function m($s, string $re): int|false
    {
        return preg_match($re, $s);
    }

    public function stem_string($words): string // string stem_string( string $words )
    {
        $word = explode(' ', $words);
        for ($i = 0; $i < count($word); ++$i) {
            if (true === $this->unset_predlog) {
                $word[$i] = preg_replace($this->PREDLOG, '', $word[$i]);
            }
            $word[$i] = $this->stem_word($word[$i]);
            if (empty($word[$i])) {
                unset($word[$i]);
            }
        }

        return implode(' ', $word); // if you need return array change on -> return $word;
    }

    private function stem_word($word)
    {
        mb_regex_encoding('UTF-8');
        mb_internal_encoding('UTF-8');
        $word = mb_strtolower($word);
        $word = str_ireplace('ё', 'е', $word);

        // Check against cache of stemmed words
        if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
            return $this->Stem_Cache[$word];
        }

        $stem = $word;
        do {
            if (!preg_match($this->RVRE, $word, $p)) {
                break;
            }
            $start = $p[1];
            $RV = $p[2];
            if (!$RV) {
                break;
            }

            // Step 1
            if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
                $this->s($RV, $this->REFLEXIVE, '');

                if ($this->s($RV, $this->ADJECTIVE, '')) {
                    $this->s($RV, $this->PARTICIPLE, '');
                } else {
                    if (!$this->s($RV, $this->VERB, '')) {
                        $this->s($RV, $this->NOUN, '');
                    }
                }
            }

            // Step 2
            $this->s($RV, '/и$/', '');

            // Step 3
            if ($this->m($RV, $this->DERIVATIONAL)) {
                $this->s($RV, '/ость?$/', '');
            }

            // Step 4
            if (!$this->s($RV, '/ь$/', '')) {
                $this->s($RV, '/ейше?/', '');
                $this->s($RV, '/нн$/', 'н');
            }

            $stem = $start.$RV;
        } while (false);

        if ($this->Stem_Caching) {
            $this->Stem_Cache[$word] = $stem;
        }

        return $stem;
    }

    private function stem_caching($parm_ref)
    {
        $caching_level = @$parm_ref['-level'];
        if ($caching_level) {
            if (!$this->m($caching_level, '/^[012]$/')) {
                exit(__CLASS__."::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }

        return $this->Stem_Caching;
    }

    public function clear_stem_cache(): void
    {
        $this->Stem_Cache = [];
    }
}
