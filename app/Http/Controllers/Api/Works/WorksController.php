<?php

namespace App\Http\Controllers\Api\Works;

use App\Http\Controllers\Controller;
use App\Http\Resources\Works\WorkStepGroupIndexResource;
use App\Http\Resources\Works\WorkStepIndexResource;
use App\Models\UserWorksCompletion;
use App\Models\WorkField;
use App\Models\WorkFieldUser;
use App\Models\WorkStep;
use App\Models\WorkStepGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WorksController extends Controller
{
    /**
     * Get all Work Groups
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $workStepGroups = WorkStepGroup::all();
        return response()->json([
            'status' => true,
            'message' => 'Successfully get all work step groups',
            'data' => WorkStepGroupIndexResource::collection($workStepGroups),
        ]);
    }
    /**
     * Get Work Groups by ID
     * @param \App\Models\WorkStepGroup $workStepGroup
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(WorkStepGroup $workStepGroup, Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Successfully get a work',
            'data' => new WorkStepGroupIndexResource($workStepGroup)
        ]);
    }

    /**
     * Mark step as Complete
     */
    public function completeStep(WorkStepGroup $workStepGroup, Request $request)
    {
        try {
            $request->validate([
                'work_step_id' => 'required|int|exists:work_steps,id',
                'is_completed' => 'required|boolean',
                'result' => 'nullable|string'
            ]);

            if (!$workStepGroup->workSteps()->where('id', $request->work_step_id)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'The work step with id : ' . $request->work_step_id . ' is not a child of ' . $workStepGroup->title,
                ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 400);
        }

        $workStep = WorkStep::find($request->work_step_id);

        if ($workStep->field?->type == 'text') {
            if (!$request->result) {
                return response()->json([
                    'status' => false,
                    'message' => 'This step completion requires a text result',
                ], 400);
            }

            WorkFieldUser::updateOrCreate([
                'user_id' => $request->user()->id,
                'work_field_id' => $workStep->field?->id,
            ], [
                'text' => $request->result
            ]);
        }

        UserWorksCompletion::updateOrCreate([
            'user_id' => $request->user()->id,
            'work_step_id' => $workStep->id,
        ], [
            'is_completed' => $request->is_completed
        ]);



        return response()->json([
            'status' => true,
            'message' => 'User work step progress updated',
            'data' => new WorkStepIndexResource($workStep)
        ]);
    }

    /**
     * Upload work result capture
     */
    public function uploadFile(WorkStepGroup $workStepGroup, Request $request)
    {
        try {
            $request->validate([
                'work_field_id' => 'required|int|exists:work_fields,id',
                'file' => 'required|file',
            ]);

            if (!$workStepGroup->fields()->where('id', $request->work_field_id)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'The work field with id : ' . $request->work_field_id . ' is not a child of ' . $workStepGroup->title,
                ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 400);
        }

        $workField = WorkField::find($request->work_field_id);
        $path = $request->file('file')?->store('praktikum', 'public');

        WorkFieldUser::updateOrCreate([
            'user_id' => $request->user()->id,
            'work_field_id' => $workField->id,
        ], [
            'file' => $path
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User progress updated',
            'data' => new WorkStepIndexResource($workField->workStep)
        ]);
    }
}
