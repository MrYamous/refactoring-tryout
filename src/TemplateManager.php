<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\Template;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeSubject($replaced->subject, $data);
        $replaced->content = $this->computeContent($replaced->content, $data);

        return $replaced;
    }

    private function computeSubject($text, array $data)
    {
        if (strpos($text, '[lesson:instructor_name]') !== false) {
            $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
            $instructorOfLesson = InstructorRepository::getInstance()->getById($lesson->instructorId);
            $text = str_replace('[lesson:instructor_name]', $instructorOfLesson->firstname, $text);
        }

        return $text;
    }

    private function computeContent($text, array $data)
    {
        $applicationContext = ApplicationContext::getInstance();
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        $learner = (isset($data['user']) and ($data['user'] instanceof Learner)) ? $data['user'] : $applicationContext->getCurrentUser();
        $usefulObject = MeetingPointRepository::getInstance()->getById($lesson->meetingPointId);
        $instructorOfLesson = InstructorRepository::getInstance()->getById($lesson->instructorId);

        if ($learner) {
            if (false !== strpos($text, '[user:first_name]')) {
                $text = str_replace('[user:first_name]', ucfirst(strtolower($learner->firstname)), $text);
            }
        }

        if ($lesson) {
            if (false !== strpos($text, '[lesson:start_date]')) {
                $text = str_replace('[lesson:start_date]', $lesson->startTime->format('d/m/Y'), $text);
            }

            if (false !== strpos($text, '[lesson:start_time]')) {
                $text = str_replace('[lesson:start_time]', $lesson->startTime->format('H:i'), $text);
            }

            if (false !== strpos($text, '[lesson:end_time]')) {
                $text = str_replace('[lesson:end_time]', $lesson->endTime->format('H:i'), $text);
            }

            if (false !== strpos($text, '[lesson:meeting_point]')) {
                $text = str_replace('[lesson:meeting_point]', $usefulObject->name, $text);
            }

            if (false !== strpos($text, '[lesson:instructor_name]')) {
                $text = str_replace('[lesson:instructor_name]', $instructorOfLesson->firstname, $text);
            }
        }

        return $text;
    }

    private function computeText($text, array $data)
    {

        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;

        if ($lesson) {
            $lessonFromRepository = LessonRepository::getInstance()->getById($lesson->id);
            $instructorOfLesson = InstructorRepository::getInstance()->getById($lesson->instructorId);

            if (false !== strpos($text, '[lesson:instructor_link]')) {
                $text = str_replace('[instructor_link]', 'instructors/' . $instructorOfLesson->id . '-' . urlencode($instructorOfLesson->firstname), $text);
            }

            $containsSummaryHtml = strpos($text, '[lesson:summary_html]');
            $containsSummary = strpos($text, '[lesson:summary]');

            if (false !== $containsSummaryHtml || false !== $containsSummary) {
                if (false !== $containsSummaryHtml) {
                    $text = str_replace(
                        '[lesson:summary_html]',
                        Lesson::renderHtml($lessonFromRepository),
                        $text
                    );
                }
                if (false !== $containsSummary) {
                    $text = str_replace(
                        '[lesson:summary]',
                        Lesson::renderText($lessonFromRepository),
                        $text
                    );
                }
            }
        }

        if (isset($data['instructor']) and ($data['instructor'] instanceof Instructor)) {
            $text = str_replace('[instructor_link]', 'instructors/' . $data['instructor']->id . '-' . urlencode($data['instructor']->firstname), $text);
        } else {
            $text = str_replace('[instructor_link]', '', $text);
        }

        return $text;
    }
}
