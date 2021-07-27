<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;

class TemplatePlaceholderResolver
{

    public function resolveLessonInstructorName(string $text, array $data): string
    {
        if (false === strpos($text, '[lesson:instructor_name]')) {
            return $text;
        }
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        $instructorOfLesson = InstructorRepository::getInstance()->getById($lesson->instructorId);
        return str_replace('[lesson:instructor_name]', $instructorOfLesson->firstname, $text);
    }

    public function resolveLessonInstructorLink(string $text, array $data): string
    {
        if (false === strpos($text, '[lesson:instructor_link]')) {
            return $text;
        }
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        $instructorOfLesson = InstructorRepository::getInstance()->getById($lesson->instructorId);
        return str_replace('[instructor_link]', 'instructors/' . $instructorOfLesson->id . '-' . urlencode($instructorOfLesson->firstname), $text);
    }

    public function resolveLessonStartDate(string $text, array $data): string
    {
        if (false === strpos($text, '[lesson:start_date]')) {
            return $text;
        }
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        return str_replace('[lesson:start_date]', $lesson->startTime->format('d/m/Y'), $text);
    }

    public function resolveLessonStartTime(string $text, array $data): string
    {
        if (false === strpos($text, '[lesson:start_time]')) {
            return $text;
        }
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        return str_replace('[lesson:start_time]', $lesson->startTime->format('H:i'), $text);
    }

    public function resolveLessonEndTime(string $text, array $data): string
    {
        if (false === strpos($text, '[lesson:end_time]')) {
            return $text;
        }
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        return str_replace('[lesson:end_time]', $lesson->endTime->format('H:i'), $text);
    }

    public function resolveLessonMeetingPoint(string $text, array $data): string
    {
        if (false === strpos($text, '[lesson:meeting_point]')) {
            return $text;
        }
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        $meetingPoint = MeetingPointRepository::getInstance()->getById($lesson->meetingPointId);
        return str_replace('[lesson:meeting_point]', $meetingPoint->name, $text);
    }

    public function resolveLessonSummary(string $text, array $data): string
    {
        if (false === strpos($text, '[lesson:summary]')) {
            return $text;
        }
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        $lessonFromRepository = LessonRepository::getInstance()->getById($lesson->id);
        return $text = str_replace('[lesson:summary]', Lesson::renderText($lessonFromRepository), $text);
    }

    public function resolveLessonSummaryHtml(string $text, array $data): string
    {
        if (false === strpos($text, '[lesson:summary_html]')) {
            return $text;
        }
        $lesson = (isset($data['lesson']) and ($data['lesson'] instanceof Lesson)) ? $data['lesson'] : null;
        $lessonFromRepository = LessonRepository::getInstance()->getById($lesson->id);
        return str_replace('[lesson:summary_html]', Lesson::renderHtml($lessonFromRepository), $text);
    }

    public static function resolveUserFirstname(string $text, array $data): string
    {
        $applicationContext = ApplicationContext::getInstance();
        $learner = (isset($data['user']) and ($data['user'] instanceof Learner)) ? $data['user'] : $applicationContext->getCurrentUser();
        $text = str_replace('[user:first_name]', ucfirst(strtolower($learner->firstname)), $text);

        return $text;
    }


}