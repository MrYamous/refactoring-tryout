<?php


namespace App;


use App\Context\ApplicationContext;
use App\Entity\Learner;

class UserFirstname implements ResolverInterface
{

    public function canResolve(string $text): bool
    {
        return false !== strpos($text, '[user:first_name]');
    }

    public function resolvePlaceholder(string $text, array $data): string
    {
        $applicationContext = ApplicationContext::getInstance();
        $learner = (isset($data['user']) and ($data['user'] instanceof Learner)) ? $data['user'] : $applicationContext->getCurrentUser();
        $text = str_replace('[user:first_name]', ucfirst(strtolower($learner->firstname)), $text);

        return $text;
    }

}