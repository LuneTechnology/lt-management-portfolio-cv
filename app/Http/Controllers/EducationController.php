<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::guard('web')->user();

        if ($user->id_role == 2) {

            $educations = Education::with([
                'work.workTag',
                'user',
            ])->get();

        } else {

            $educations = Education::where(
                'id_user',
                $user->id_user
            )
            ->with([
                'work.workTag',
                'user',
            ])
            ->get();

        }

        return response()->json(
            $educations
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ) {

        $user =
            Auth::guard('web')->user();


        $validated =
            $request->validate([
                'id_work' => [
                    'required',
                    'integer',
                    'exists:works,id_work',
                ],

                'major' =>
                    'required|string|max:50',

                'level' =>
                    'required|string|max:20',

                'date_in' =>
                    'required|date',

                'date_out' =>
                    'nullable|date|after_or_equal:date_in',

                'gpa' =>
                    'nullable|numeric|min:0|max:4',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Make sure selected Work is Education
        |--------------------------------------------------------------------------
        */

        $work =
            Work::with('workTag')
                ->findOrFail(
                    $validated['id_work']
                );


        if (
            !$work->workTag ||
            strtolower(
                $work->workTag->name
            ) !== 'education'
        ) {

            return response()->json([
                'message' =>
                    'Selected Work is not an Education institution.',
            ], 422);

        }


        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        if (
            $user->id_role == 1
        ) {

            $validated['id_user'] =
                $user->id_user;

        }


        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        if (
            $user->id_role == 2
        ) {

            $request->validate([
                'id_user' => [
                    'required',
                    'integer',
                    'exists:users,id_user',
                ],
            ]);

            $validated['id_user'] =
                $request->id_user;

        }


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $education =
            Education::create(
                $validated
            );


        return response()->json(
            $education->load([
                'work.workTag',
                'user',
            ]),
            201
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        string $id
    ) {

        $user =
            Auth::guard('web')->user();


        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        if (
            $user->id_role == 2
        ) {

            $education =
                Education::with([
                    'work.workTag',
                    'user',
                ])
                ->findOrFail(
                    $id
                );

            return response()->json(
                $education
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $education =
            Education::where(
                'id_education',
                $id
            )
            ->where(
                'id_user',
                $user->id_user
            )
            ->with([
                'work.workTag',
                'user',
            ])
            ->firstOrFail();


        return response()->json(
            $education
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        string $id
    ) {

        $user =
            Auth::guard('web')->user();


        /*
        |--------------------------------------------------------------------------
        | Find Education
        |--------------------------------------------------------------------------
        */

        if (
            $user->id_role == 2
        ) {

            $education =
                Education::findOrFail(
                    $id
                );

        } else {

            $education =
                Education::where(
                    'id_education',
                    $id
                )
                ->where(
                    'id_user',
                    $user->id_user
                )
                ->firstOrFail();

        }


        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([
                'id_work' => [
                    'sometimes',
                    'required',
                    'integer',
                    'exists:works,id_work',
                ],

                'major' =>
                    'sometimes|required|string|max:50',

                'level' =>
                    'sometimes|required|string|max:20',

                'date_in' =>
                    'sometimes|required|date',

                'date_out' =>
                    'nullable|date|after_or_equal:date_in',

                'gpa' =>
                    'nullable|numeric|min:0|max:4',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Check Work
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $validated['id_work']
            )
        ) {

            $work =
                Work::with('workTag')
                    ->findOrFail(
                        $validated['id_work']
                    );


            if (
                !$work->workTag ||
                strtolower(
                    $work->workTag->name
                ) !== 'education'
            ) {

                return response()->json([
                    'message' =>
                        'Selected Work is not an Education institution.',
                ], 422);

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Super Admin can change owner
        |--------------------------------------------------------------------------
        */

        if (
            $user->id_role == 2 &&
            $request->has('id_user')
        ) {

            $request->validate([
                'id_user' => [
                    'required',
                    'integer',
                    'exists:users,id_user',
                ],
            ]);

            $validated['id_user'] =
                $request->id_user;

        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $education->update(
            $validated
        );


        return response()->json(
            $education->load([
                'work.workTag',
                'user',
            ])
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        string $id
    ) {

        $user =
            Auth::guard('web')->user();


        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        if (
            $user->id_role == 2
        ) {

            $education =
                Education::findOrFail(
                    $id
                );

        } else {

            /*
            |--------------------------------------------------------------------------
            | Admin
            |--------------------------------------------------------------------------
            */

            $education =
                Education::where(
                    'id_education',
                    $id
                )
                ->where(
                    'id_user',
                    $user->id_user
                )
                ->firstOrFail();

        }


        $education->delete();


        return response()->json(
            null,
            204
        );
    }
}