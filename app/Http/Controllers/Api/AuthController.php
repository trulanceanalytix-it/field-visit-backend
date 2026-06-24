<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\EmployeeBeatOutletMap;
use Carbon\Carbon;
use Illuminate\Auth\EloquentUserProvider;

class AuthController extends Controller
{
    public function employeeName($emp_id)
    {
        $employee = Employee::where('emp_id', $emp_id)->first();
        if ($employee && $employee->status === 'INACTIVE') {
            return response()->json([
                'name' => 'Employee ID deactivated'
            ]);
        }
        return response()->json([
            'name' => $employee ? $employee->emp_name : null
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|digits:5',
            'password' => 'required'
        ]);

        $user = Employee::where('emp_id', $request->user_id)->first();
        if ($user && $user->status === 'INACTIVE') {
            return response()->json([
                'status' => false,
                'message' => 'Employee ID has been deactivated. Contact head office for further procedures.'
            ], 403);
        }

        if (!Auth::attempt([
            'emp_id' => $request->user_id,
            'password' => $request->password,
        ])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Employee::where('emp_id', $request->user_id)->first();
        $hasDirectBeats = EmployeeBeatOutletMap::where('employee_id', $user->emp_id)->exists();

        // revoke old tokens
        // $user->tokens()->delete();
        $reportingTo = trim(sprintf(
            ' %s_%s',
            $user->reporting_mgr_name,
            $user->mgr_designation
        ));

        // ✅ Create token
        $token = $user->createToken('five-mobile');
        // ✅ Set expiry to today 11:00 PM
        $token->accessToken->expires_at = Carbon::today()->setTime(23, 58, 0);
        $token->accessToken->save();
        return response()->json([
            'status' => true,
            'message' => 'Login success',
            'token' => $token->plainTextToken,
            'user' => [
                'emp_id' => $user->emp_id,
                'emp_name' => $user->emp_name,
                'reporting_to' => $reportingTo,
                'is_admin' => $user->is_admin,
                'has_direct_beats' => $hasDirectBeats,  // ✅ ADD THIS
            ]
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'emp_id' => 'required|digits:5',
            'password' => 'required|string|min:6'
        ]);

        $employee = Employee::where('emp_id', $request->emp_id)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee ID not found'
            ], 404);
        }

        if ($employee->status === 'INACTIVE') {
            return response()->json([
                'status' => false,
                'message' => 'Employee ID has been deactivated. Contact head office.'
            ], 403);
        }

        $employee->password = bcrypt($request->password);
        $employee->save();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully'
        ]);
    }
}
