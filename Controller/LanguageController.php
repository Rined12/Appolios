<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/LanguageModel.php';

class LanguageController extends BaseController
{
    public function switchLanguage(): void
    {
        $model = new LanguageModel();
        $code = strtolower(trim((string) ($_GET['lang'] ?? '')));
        if ($model->isValid($code)) {
            $model->setLang($code);
        }
        $return = (string) ($_GET['return'] ?? '');
        $route = $model->sanitizeReturnRoute($return);
        $this->redirect($route);
    }
}
