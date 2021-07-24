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

    public static function resolveLessonInstructorName(string $text, Lesson $lesson): string
    {
        $instructorOfLesson = InstructorRepository::getInstance()->getById($lesson->instructorId);
        return str_replace('[lesson:instructor_name]', $instructorOfLesson->firstname, $text);
    }

    public static function resolveLessonInstructorLink(string $text, Lesson $lesson)
    {
        $instructorOfLesson = InstructorRepository::getInstance()->getById($lesson->instructorId);
        return str_replace('[instructor_link]', 'instructors/' . $instructorOfLesson->id . '-' . urlencode($instructorOfLesson->firstname), $text);
    }

    public static function resolveLessonStartDate(string $text, Lesson $lesson): string
    {
        return str_replace('[lesson:start_date]', $lesson->startTime->format('d/m/Y'), $text);
    }

    public static function resolveLessonStartTime(string $text, Lesson $lesson): string
    {
        return str_replace('[lesson:start_time]', $lesson->startTime->format('H:i'), $text);
    }

    public static function resolveLessonEndTime(string $text, Lesson $lesson): string
    {
        return str_replace('[lesson:end_time]', $lesson->endTime->format('H:i'), $text);
    }

    public static function resolveLessonMeetingPoint(string $text, Lesson $lesson): string
    {
        $meetingPoint = MeetingPointRepository::getInstance()->getById($lesson->meetingPointId);
        return str_replace('[lesson:meeting_point]', $meetingPoint->name, $text);
    }

    public static function resolveLessonSummary(string $text, Lesson $lesson)
    {
        $lessonFromRepository = LessonRepository::getInstance()->getById($lesson->id);
        return $text = str_replace('[lesson:summary]', Lesson::renderText($lessonFromRepository), $text);
    }

    public static function resolveLessonSummaryHtml(string $text, Lesson $lesson)
    {
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