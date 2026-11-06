<?php

declare(strict_types=1);

namespace Glavweb\FrameworkBundle\Util;

/**
 * Class SearchWords.
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class SearchWords
{
    public static function prepareSearch(string $search): array
    {
        if (!$search) {
            return [];
        }

        $stopWords = [
            'что', 'как', 'все', 'она', 'так', 'его', 'только', 'мне', 'было', 'вот',
            'меня', 'еще', 'нет', 'ему', 'теперь', 'когда', 'даже', 'вдруг', 'если',
            'уже', 'или', 'быть', 'был', 'него', 'вас', 'нибудь', 'опять', 'вам', 'ведь',
            'там', 'потом', 'себя', 'может', 'они', 'тут', 'где', 'есть', 'надо', 'ней',
            'для', 'тебя', 'чем', 'была', 'сам', 'чтоб', 'без', 'будто', 'чего', 'раз',
            'тоже', 'себе', 'под', 'будет', 'тогда', 'кто', 'этот', 'того', 'потому',
            'этого', 'какой', 'ним', 'этом', 'один', 'почти', 'мой', 'тем', 'чтобы',
            'нее', 'были', 'куда', 'зачем', 'всех', 'можно', 'при', 'два', 'другой',
            'хоть', 'после', 'над', 'больше', 'тот', 'через', 'эти', 'нас', 'про', 'них',
            'какая', 'много', 'разве', 'три', 'эту', 'моя', 'свою', 'этой', 'перед',
            'чуть', 'том', 'такой', 'более', 'всю',
        ];

        $search = strtr($search, ['ё' => 'е']);
        $search = trim((string) preg_replace('/ +/', ' ', $search));

        $words = explode(' ', $search);
        $words = array_filter($words, static function ($word) use ($stopWords): bool {
            if (mb_strlen($word, 'UTF-8') < 3 || in_array($word, $stopWords)) {
                return false;
            }

            return true;
        });

        $stemRussian = new StemRussian();
        $words = array_map(static function (string $word) use ($stemRussian): string {
            return $stemRussian->stem_string($word);
        }, $words);

        return $words;
    }
}
