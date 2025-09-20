<?php

namespace App\Http\Controllers\Api\Works;

use App\Http\Controllers\Controller;
use App\Http\Resources\Works\WorkStepGroupIndexResource;
use App\Http\Resources\Works\WorkStepIndexResource;
use App\Models\UserWorksCompletion;
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
                'result' => 'string|nullable',
            ]);

            if (!$workStepGroup->workSteps()->where('id', $request->work_step_id)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'The work step with id : '.$request->work_step_id.' is not a child of '.$workStepGroup->title,
                ], 400);
            }
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 400);
        }

        $workStep = WorkStep::find($request->work_step_id);

        UserWorksCompletion::updateOrCreate([
            'user_id' => $request->user()->id,
            'work_step_id' => $workStep->id,
        ], [
            'is_completed' => $request->is_completed,
            'result' => $request->result,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User work step progress updated',
            'data' => new WorkStepIndexResource($workStep)
        ]);
    }
}
