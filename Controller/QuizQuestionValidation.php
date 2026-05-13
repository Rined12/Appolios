<?php

/**
 * Validation métier des formulaires quiz et banque de questions.
 * Utilisée par les contrôleurs (règles hors des vues).
 */
class QuizQuestionValidation
{
    public const TITLE_MAX = 255;
    public const TAGS_MAX = 500;
    public const QUESTION_TEXT_MAX = 8000;
    public const OPTION_TEXT_MAX = 500;
    public const TIME_LIMIT_MAX_SEC = 86400;

    /** @var string[] */
    public const DIFFICULTIES = ['beginner', 'intermediate', 'advanced'];

    public static function validateQuizMeta(array $post): array
    {
        $errors = [];
        $title = trim((string) ($post['title'] ?? ''));
        if ($title === '') {
            $errors[] = 'Le titre du quiz est obligatoire.';
        } elseif (self::strLen($title) > self::TITLE_MAX) {
            $errors[] = 'Le titre est trop long (' . self::TITLE_MAX . ' caractères maximum).';
        }

        $tagsRaw = isset($post['tags']) ? trim((string) $post['tags']) : '';
        if (self::strLen($tagsRaw) > self::TAGS_MAX) {
            $errors[] = 'Les tags sont trop longs (' . self::TAGS_MAX . ' caractères maximum).';
        }
        $tags = $tagsRaw === '' ? null : $tagsRaw;

        $diff = self::normalizeDifficulty($post['difficulty'] ?? 'beginner');

        $timeLimit = null;
        $tl = $post['time_limit_sec'] ?? '';
        if ($tl !== '' && $tl !== null) {
            if (!is_numeric($tl)) {
                $errors[] = 'Le temps limite doit être un nombre entier ou vide.';
            } else {
                $n = (int) $tl;
                if ($n < 0) {
                    $errors[] = 'Le temps limite ne peut pas être négatif.';
                } elseif ($n > self::TIME_LIMIT_MAX_SEC) {
                    $errors[] = 'Le temps limite ne peut pas dépasser 24 heures (' . self::TIME_LIMIT_MAX_SEC . ' s).';
                } elseif ($n > 0) {
                    $timeLimit = $n;
                }
            }
        }

        return [
            'errors' => $errors,
            'title' => $title,
            'tags' => $tags,
            'difficulty' => $diff,
            'time_limit_sec' => $timeLimit,
        ];
    }

    public static function validateNormalizedQuestions(array $questions): array
    {
        $errors = [];
        foreach ($questions as $q) {
            if (self::strLen((string) ($q['question'] ?? '')) > self::QUESTION_TEXT_MAX) {
                $errors[] = 'Une question dépasse la longueur autorisée (' . self::QUESTION_TEXT_MAX . ' caractères).';
                return $errors;
            }
            foreach (($q['options'] ?? []) as $o) {
                if (self::strLen((string) $o) > self::OPTION_TEXT_MAX) {
                    $errors[] = 'Une option de réponse dépasse la longueur autorisée (' . self::OPTION_TEXT_MAX . ' caractères).';
                    return $errors;
                }
            }
        }

        return $errors;
    }

    public static function validateQuestionBankFields(
        string $title,
        string $questionText,
        array $opts,
        int $correctIndex,
        string $tags
    ): array {
        $errors = [];
        if (self::strLen($title) > self::TITLE_MAX) {
            $errors[] = 'Le titre est trop long.';
        }
        if ($questionText === '') {
            $errors[] = 'Le texte de la question est obligatoire.';
        } elseif (self::strLen($questionText) > self::QUESTION_TEXT_MAX) {
            $errors[] = 'Le texte de la question est trop long.';
        }
        if (self::strLen($tags) > self::TAGS_MAX) {
            $errors[] = 'Les tags sont trop longs.';
        }
        if (count($opts) < 2) {
            $errors[] = 'Au moins deux options de réponse sont requises.';
        } else {
            foreach ($opts as $o) {
                if (self::strLen((string) $o) > self::OPTION_TEXT_MAX) {
                    $errors[] = 'Une option est trop longue.';
                    break;
                }
            }
            if ($correctIndex < 0 || $correctIndex >= count($opts)) {
                $errors[] = 'L’index de la bonne réponse est invalide (doit correspondre à une option).';
            }
        }

        return $errors;
    }

    public static function validateStudentQuizAnswers($answers, array $questions): ?string
    {
        if (!is_array($answers)) {
            return 'Données de réponses invalides.';
        }
        foreach ($questions as $i => $q) {
            $nopts = count($q['options'] ?? []);
            if ($nopts < 1) {
                continue;
            }
            if (!array_key_exists($i, $answers)) {
                return 'Veuillez répondre à toutes les questions.';
            }
            $given = (int) $answers[$i];
            if ($given < 0 || $given >= $nopts) {
                return 'Une réponse sélectionnée est invalide.';
            }
        }

        return null;
    }

    public static function normalizeDifficulty($raw): string
    {
        $s = is_string($raw) ? trim($raw) : '';
        return in_array($s, self::DIFFICULTIES, true) ? $s : 'beginner';
    }

    private static function strLen(string $s): int
    {
        if (function_exists('mb_strlen')) {
            return (int) mb_strlen($s, 'UTF-8');
        }
        return strlen($s);
    }
}

