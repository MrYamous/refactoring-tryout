<?php

namespace App;

use App\Entity\Lesson;
use App\Entity\Template;

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeSubject($replaced->subject, $data);
        $replaced->content = $this->computeContent($replaced->content, $data);

        return $replaced;
    }

    private function computeSubject(string $text, array $data): string
    {
        if (strpos($text, '[lesson:instructor_name]') !== false) {
            $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
            $text = TemplatePlaceholderResolver::resolveLessonInstructorName($text, $lesson);
        }

        return $text;
    }

    private function computeContent(string $text, array $data): string
    {
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;

        if (false !== strpos($text, '[user:first_name]')) {
            $text = TemplatePlaceholderResolver::resolveUserFirstname($text, $data);
        }

        if (false !== strpos($text, '[lesson:start_date]')) {
            $text = TemplatePlaceholderResolver::resolveLessonStartDate($text, $lesson);
        }

        if (false !== strpos($text, '[lesson:start_time]')) {
            $text = TemplatePlaceholderResolver::resolveLessonStartTime($text, $lesson);
        }

        if (false !== strpos($text, '[lesson:end_time]')) {
            $text = TemplatePlaceholderResolver::resolveLessonEndTime($text, $lesson);
        }

        if (false !== strpos($text, '[lesson:meeting_point]')) {
            $text = TemplatePlaceholderResolver::resolveLessonMeetingPoint($text, $lesson);
        }

        if (false !== strpos($text, '[lesson:instructor_name]')) {
            $text = TemplatePlaceholderResolver::resolveLessonInstructorName($text, $lesson);
        }

        return $text;
    }
}
