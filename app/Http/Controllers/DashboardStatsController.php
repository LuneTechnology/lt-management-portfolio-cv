<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Role;
use App\Models\Stack;
use App\Models\StackType;
use App\Models\Task;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkTag;
use App\Models\WorkType;
use App\Models\PositionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardStatsController extends Controller
{
    private function isSuperAdmin($user): bool
    {
        return (int) ($user?->id_role) === 2;
    }

    /**
     * Portfolio entities are scoped to records owned by the signed-in user.
     * Projects and works have no owner column in this schema, so their ownership
     * is derived through the user's experiences/education relations.
     */
    private function scopeExperiences(Builder $query, $user, bool $superAdmin): Builder
    {
        return $superAdmin ? $query : $query->where('id_user', $user->id_user);
    }

    private function scopeProjects(Builder $query, $user, bool $superAdmin): Builder
    {
        if ($superAdmin) {
            return $query;
        }

        return $query->whereHas('experiences', fn (Builder $experiences) =>
            $experiences->where('id_user', $user->id_user)
        );
    }

    private function scopeWorks(Builder $query, $user, bool $superAdmin): Builder
    {
        if ($superAdmin) {
            return $query;
        }

        return $query->where(function (Builder $workQuery) use ($user) {
            $workQuery->whereHas('experiences', fn (Builder $q) =>
                $q->where('id_user', $user->id_user)
            )->orWhereHas('educations', fn (Builder $q) =>
                $q->where('id_user', $user->id_user)
            );
        });
    }

    private function monthlyCreated(Builder $query, string $table, Carbon $now): array
    {
        if (!Schema::hasColumn($table, 'created_at')) {
            return [];
        }

        $start = $now->copy()->startOfMonth()->subMonths(5);
        $result = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $start->copy()->addMonths($i);
            $result[] = [
                'month' => $month->format('M'),
                'count' => (clone $query)
                    ->whereBetween('created_at', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth(),
                    ])
                    ->count(),
            ];
        }

        return $result;
    }

    private function monthlyChange(Builder $query, string $table, Carbon $now): array
    {
        if (!Schema::hasColumn($table, 'created_at')) {
            return ['value' => null, 'trend' => 'same', 'label' => 'tren tidak tersedia'];
        }

        $current = (clone $query)
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        $previousMonth = $now->copy()->subMonth();
        $previous = (clone $query)
            ->whereBetween('created_at', [$previousMonth->copy()->startOfMonth(), $previousMonth->copy()->endOfMonth()])
            ->count();

        $difference = $current - $previous;

        return [
            'value' => $difference,
            'trend' => $difference > 0 ? 'up' : ($difference < 0 ? 'down' : 'same'),
            'label' => 'dibanding bulan lalu',
        ];
    }

    private function countCreatedThisMonth(Builder $query, string $table, Carbon $now): int
    {
        if (!Schema::hasColumn($table, 'created_at')) {
            return 0;
        }

        return (clone $query)
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();
    }

    private function metric(int $total, array $change, array $chart): array
    {
        return [
            'total' => $total,
            'change' => $change['value'],
            'trend' => $change['trend'],
            'changeLabel' => $change['label'],
            'chartData' => array_column($chart, 'count'),
            'chartLabels' => array_column($chart, 'month'),
        ];
    }

    private function activityItems(Builder $query, string $table, Carbon $month, callable $labeler): array
    {
        if (!Schema::hasColumn($table, 'created_at')) {
            return [];
        }

        return $query->whereBetween('created_at', [
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth(),
            ])->get()->map($labeler)->filter()->values()->all();
    }

    /**
     * Portfolio trend is based on the date range during which a project was active,
     * not its database created_at timestamp. A null date_out means the project is
     * still active through the current month. A project ending in September is not
     * counted in October.
     */
    private function activityTrend(Builder $projects, Builder $works, Builder $experiences, Carbon $now): array
    {
        $start = $now->copy()->startOfMonth()->subMonths(5);
        $months = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $start->copy()->addMonths($i);
            $monthStart = $month->copy()->startOfMonth()->toDateString();
            $monthEnd = $month->copy()->endOfMonth()->toDateString();

            // A project is active during a month when its date range overlaps that month.
            $activeProjects = (clone $projects)
                ->with('work')
                ->whereDate('date_in', '<=', $monthEnd)
                ->where(function (Builder $q) use ($monthStart) {
                    $q->whereNull('date_out')->orWhereDate('date_out', '>=', $monthStart);
                })
                ->get();

            $projectItems = $activeProjects->map(fn ($project) => trim(
                (string) ($project->name ?? 'Project #' . $project->id_project)
                . ' — ' . (string) ($project->work?->name ?? 'Work belum ditentukan')
            ))->values()->all();

            $activeProjectIds = $activeProjects->pluck('id_project')->all();
            $activeExperiences = (clone $experiences)
                ->with(['project', 'work'])
                ->whereIn('id_project', $activeProjectIds)
                ->get();

            $experienceItems = $activeExperiences->map(fn ($item) => trim(
                (string) ($item->project?->name ?? 'Project')
                . ' — ' . (string) ($item->work?->name ?? 'Work')
            ))->unique()->values()->all();

            // Group projects by their parent Work for a nested tooltip presentation.
            $workGroups = $activeProjects
                ->filter(fn ($project) => $project->work !== null)
                ->groupBy(fn ($project) => (string) $project->id_work)
                ->map(function ($projectsInWork) {
                    $work = $projectsInWork->first()->work;
                    return [
                        'id' => $work->id_work,
                        'name' => $work->name ?: 'Work #' . $work->id_work,
                        'projects' => $projectsInWork
                            ->map(fn ($project) => $project->name ?: 'Project #' . $project->id_project)
                            ->unique()
                            ->values()
                            ->all(),
                    ];
                })
                ->values()
                ->all();

            // Keep orphan projects visible too, instead of silently omitting them.
            $orphanProjects = $activeProjects
                ->filter(fn ($project) => $project->work === null)
                ->map(fn ($project) => $project->name ?: 'Project #' . $project->id_project)
                ->values()
                ->all();

            $workItems = collect($workGroups)->pluck('name')->values()->all();

            $months[] = [
                'key' => $month->format('Y-m'),
                'label' => $month->translatedFormat('M Y'),
                'projects' => count($projectItems),
                'works' => count($workItems),
                'experiences' => count($experienceItems),
                'projectsItems' => $projectItems,
                'worksItems' => $workItems,
                'experiencesItems' => $experienceItems,
                'workGroups' => $workGroups,
                'unassignedProjects' => $orphanProjects,
            ];
        }

        return ['months' => $months];
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $superAdmin = $this->isSuperAdmin($user);
        $now = now();

        $projects = $this->scopeProjects(Project::query(), $user, $superAdmin);
        $works = $this->scopeWorks(Work::query(), $user, $superAdmin);
        $experiences = $this->scopeExperiences(Experience::query(), $user, $superAdmin);
        $education = Education::query();
        $achievements = Achievement::query();

        if (!$superAdmin) {
            $education->where('id_user', $user->id_user);
            $achievements->where('id_user', $user->id_user);
        }

        $taskQuery = Task::query()->whereIn(
            'id_experience',
            (clone $experiences)->select('id_experience')
        );

        $experienceStackCount = DB::table('experience_stack')
            ->whereIn('id_experience', (clone $experiences)->select('id_experience'))
            ->distinct('id_stack')
            ->count('id_stack');

        $projectCount = (clone $projects)->count();
        $workCount = (clone $works)->count();
        $experienceCount = (clone $experiences)->count();

        $data = [
            'scope' => $superAdmin ? 'all' : 'own',
            'activityTrend' => $this->activityTrend(clone $projects, clone $works, clone $experiences, $now),
            'role' => [
                'id' => (int) $user->id_role,
                'name' => $user->role?->name,
                'isSuperAdmin' => $superAdmin,
            ],
            'cards' => [
                'projects' => $this->metric(
                    $projectCount,
                    $this->monthlyChange(clone $projects, 'project', $now),
                    $this->monthlyCreated(clone $projects, 'project', $now)
                ),
                'works' => $this->metric(
                    $workCount,
                    $this->monthlyChange(clone $works, 'works', $now),
                    $this->monthlyCreated(clone $works, 'works', $now)
                ),
                'experiences' => $this->metric(
                    $experienceCount,
                    $this->monthlyChange(clone $experiences, 'experiences', $now),
                    $this->monthlyCreated(clone $experiences, 'experiences', $now)
                ),
                'users' => $superAdmin ? $this->metric(
                    User::query()->count(),
                    $this->monthlyChange(User::query(), 'users', $now),
                    $this->monthlyCreated(User::query(), 'users', $now)
                ) : null,
            ],
            'secondary' => [
                'projectsAddedThisMonth' => $this->countCreatedThisMonth(clone $projects, 'project', $now),
                'worksAddedThisMonth' => $this->countCreatedThisMonth(clone $works, 'works', $now),
                'taskCount' => (clone $taskQuery)->count(),
                'educationCount' => (clone $education)->count(),
                'achievementCount' => (clone $achievements)->count(),
                'achievementsThisYear' => (clone $achievements)->whereYear('date', $now->year)->count(),
                'educationStartedThisYear' => (clone $education)->whereYear('date_in', $now->year)->count(),
                'contributorCount' => (clone $experiences)->distinct('id_user')->count('id_user'),
                'stackCount' => $experienceStackCount,
            ],
            'masterData' => [
                'categories' => Category::query()->count(),
                'categoriesUsed' => Category::query()->whereHas('projects')->count(),
                'stacks' => Stack::query()->count(),
                'stacksUsed' => Stack::query()->whereHas('experiences', fn (Builder $q) => $this->scopeExperiences($q, $user, $superAdmin))->count(),
                'stackTypes' => StackType::query()->count(),
                'stackTypesUsed' => StackType::query()->whereHas('stacks.experiences', fn (Builder $q) => $this->scopeExperiences($q, $user, $superAdmin))->count(),
                'workTypes' => WorkType::query()->count(),
                'workTypesUsed' => WorkType::query()->whereHas('experiences', fn (Builder $q) => $this->scopeExperiences($q, $user, $superAdmin))->count(),
                'positionTypes' => PositionType::query()->count(),
                'positionTypesUsed' => PositionType::query()->whereHas('experiences', fn (Builder $q) => $this->scopeExperiences($q, $user, $superAdmin))->count(),
                'workTags' => WorkTag::query()->count(),
                'workTagsUsed' => WorkTag::query()->whereHas('works', fn (Builder $q) => $this->scopeWorks($q, $user, $superAdmin))->count(),
                'roles' => $superAdmin ? Role::query()->count() : null,
                'usersPerRole' => $superAdmin ? User::query()->select('id_role', DB::raw('COUNT(*) as total'))->groupBy('id_role')->pluck('total', 'id_role') : null,
            ],
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }
}
