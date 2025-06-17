<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Backup;
use App\Models\ClassSubject;
use App\Models\DigitalLibraryResource;
use App\Models\DigitalResourceAccess;
use App\Models\District;
use App\Models\Ethnicity;
use App\Models\Examination;
use App\Models\Expense;
use App\Models\FeeType;
use App\Models\Grade;
use App\Models\Income;
use App\Models\Message;
use App\Models\Nationality;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Province;
use App\Models\Religion;
use App\Models\ReportTemplate;
use App\Models\GeneratedReport;
use App\Models\Request;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\SchoolStoreItem;
use App\Models\Setting;
use App\Models\StoreSale;
use App\Models\StudentAchievement;
use App\Models\StudentActivity;
use App\Models\StudentBehaviorRecord;
use App\Models\StudentDiscount;
use App\Models\StudentDocument;
use App\Models\StudentEmergencyContact;
use App\Models\StudentEnrollment;
use App\Models\StudentFee;
use App\Models\StudentHealthRecord;
use App\Models\StudentInterest;
use App\Models\StudentParent;
use App\Models\StudentPreviousEducation;
use App\Models\StudentPreviousLocation;
use App\Models\StudentSibling;
use App\Models\StudentSpecialNeed;
use App\Models\Subject;
use App\Models\SystemLog;
use App\Models\TeacherDocument;
use App\Models\Village;
use App\Policies\UserPolicy;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\AcademicYearPolicy;
use App\Policies\AnnouncementPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\BackupPolicy;
use App\Policies\ClassSubjectPolicy;
use App\Policies\DigitalLibraryResourcePolicy;
use App\Policies\DigitalResourceAccessPolicy;
use App\Policies\DistrictPolicy;
use App\Policies\EthnicityPolicy;
use App\Policies\ExaminationPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\FeeTypePolicy;
use App\Policies\GradePolicy;
use App\Policies\IncomePolicy;
use App\Policies\MessagePolicy;
use App\Policies\NationalityPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProvincePolicy;
use App\Policies\ReligionPolicy;
use App\Policies\ReportTemplatePolicy;
use App\Policies\GeneratedReportPolicy;
use App\Policies\RequestPolicy;
use App\Policies\SchedulePolicy;
use App\Policies\SchoolClassPolicy;
use App\Policies\SchoolStoreItemPolicy;
use App\Policies\SettingsPolicy;
use App\Policies\StoreSalePolicy;
use App\Policies\StudentAchievementPolicy;
use App\Policies\StudentActivityPolicy;
use App\Policies\StudentBehaviorRecordPolicy;
use App\Policies\StudentDiscountPolicy;
use App\Policies\StudentDocumentPolicy;
use App\Policies\StudentEmergencyContactPolicy;
use App\Policies\StudentEnrollmentPolicy;
use App\Policies\StudentFeePolicy;
use App\Policies\StudentHealthRecordPolicy;
use App\Policies\StudentInterestPolicy;
use App\Policies\StudentParentsPolicy;
use App\Policies\StudentPreviousEducationPolicy;
use App\Policies\StudentPreviousLocationPolicy;
use App\Policies\StudentSiblingPolicy;
use App\Policies\StudentSpecialNeedPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\SystemLogPolicy;
use App\Policies\TeacherDocumentPolicy;
use App\Policies\VillagePolicy;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Student::class => StudentPolicy::class,
        Teacher::class => TeacherPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        AcademicYear::class => AcademicYearPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
        Attendance::class => AttendancePolicy::class,
        Backup::class => BackupPolicy::class,
        ClassSubject::class => ClassSubjectPolicy::class,
        DigitalLibraryResource::class => DigitalLibraryResourcePolicy::class,
        DigitalResourceAccess::class => DigitalResourceAccessPolicy::class,
        District::class => DistrictPolicy::class,
        Ethnicity::class => EthnicityPolicy::class,
        Examination::class => ExaminationPolicy::class,
        Expense::class => ExpensePolicy::class,
        FeeType::class => FeeTypePolicy::class,
        Grade::class => GradePolicy::class,
        Income::class => IncomePolicy::class,
        Message::class => MessagePolicy::class,
        Nationality::class => NationalityPolicy::class,
        Notification::class => NotificationPolicy::class,
        Payment::class => PaymentPolicy::class,
        Province::class => ProvincePolicy::class,
        Religion::class => ReligionPolicy::class,
        ReportTemplate::class => ReportTemplatePolicy::class,
        GeneratedReport::class => GeneratedReportPolicy::class,
        Request::class => RequestPolicy::class,
        Schedule::class => SchedulePolicy::class,
        SchoolClass::class => SchoolClassPolicy::class,
        SchoolStoreItem::class => SchoolStoreItemPolicy::class,
        Setting::class => SettingsPolicy::class,
        StoreSale::class => StoreSalePolicy::class,
        StudentAchievement::class => StudentAchievementPolicy::class,
        StudentActivity::class => StudentActivityPolicy::class,
        StudentBehaviorRecord::class => StudentBehaviorRecordPolicy::class,
        StudentDocument::class => StudentDocumentPolicy::class,
        StudentEmergencyContact::class => StudentEmergencyContactPolicy::class,
        StudentEnrollment::class => StudentEnrollmentPolicy::class,
        StudentHealthRecord::class => StudentHealthRecordPolicy::class,
        StudentInterest::class => StudentInterestPolicy::class,
        StudentParent::class => StudentParentsPolicy::class,
        StudentPreviousEducation::class => StudentPreviousEducationPolicy::class,
        StudentPreviousLocation::class => StudentPreviousLocationPolicy::class,
        StudentSibling::class => StudentSiblingPolicy::class,
        StudentSpecialNeed::class => StudentSpecialNeedPolicy::class,
        Subject::class => SubjectPolicy::class,
        SystemLog::class => SystemLogPolicy::class,
        TeacherDocument::class => TeacherDocumentPolicy::class,
        Village::class => VillagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ກຳນົດສິດທິພື້ນຖານສຳລັບ Filament
        Gate::define('viewAny', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('view', function ($user, $model) {
            return $user->hasRole('admin') || $user->id === $model->user_id;
        });

        Gate::define('create', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('update', function ($user, $model) {
            return $user->hasRole('admin') || $user->id === $model->user_id;
        });

        Gate::define('delete', function ($user, $model) {
            return $user->hasRole('admin');
        });

        Gate::define('restore', function ($user, $model) {
            return $user->hasRole('admin');
        });

        Gate::define('forceDelete', function ($user, $model) {
            return $user->hasRole('admin');
        });
    }
}
