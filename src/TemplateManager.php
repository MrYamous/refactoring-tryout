<?php

namespace App;

use App\Entity\Lesson;
use App\Entity\Template;

class TemplateManager
{
    private TemplatePlaceholderResolver $templatePlaceholderResolver;

    public function __construct(TemplatePlaceholderResolver $templatePlaceholderResolver)
    {
        $this->templatePlaceholderResolver = $templatePlaceholderResolver;
    }

    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $replaced = clone($tpl);
        $replaced->subject = $this->computeSubject($replaced->subject, $data);
        $replaced->content = $this->computeContent($replaced->content, $data);

        return $replaced;
    }

    private function computeSubject(string $text, array $data): string
    {
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        $text = $this->templatePlaceholderResolver->resolveLessonInstructorName($text, $lesson);

        return $text;
    }

    private function computeContent(string $text, array $data): string
    {
        $placeholderResolvers = [];

        foreach ($placeholderResolvers as $placeholderResolver) {
            if ($placeholderResolver->canResolve($text)) {
                $text = $placeholderResolver->resolvePlaceholder($text, $data);
            }
        }

        return $text;
    }
}
