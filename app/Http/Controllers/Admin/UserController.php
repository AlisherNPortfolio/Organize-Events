<?php

namespace App\Http\Controllers\Admin;

use App\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserFacade $userFacade)
    {
    }

    public static function middleware()
    {
        return [
            'role:admin'
        ];
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = $this->userFacade->getAllUsers();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = $this->userFacade->getUser($id);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,creator,user',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $user = $this->userFacade->getUser($id);

        $data = $request->only(['name', 'email', 'phone', 'role', 'status']);

        // If removing a fine, clear the fine_until date
        if ($request->has('remove_fine') && $request->input('remove_fine')) {
            $data['fine_until'] = null;
        }

        $this->userFacade->updateProfile($id, $data, $request->file('avatar'));

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Show the form for applying a fine to a user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showApplyFineForm($id)
    {
        $user = $this->userFacade->getUser($id);

        return view('admin.users.apply-fine', compact('user'));
    }

    /**
     * Apply a fine to the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applyFine(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1|max:90',
        ]);

        $user = $this->userFacade->getUser($id);

        // Create a manual fine record
        $this->userFacade->applyManualFine(
            $id,
            $request->input('reason'),
            $request->input('duration_days')
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Fine applied to user successfully.');
    }

    /**
     * Remove a fine from the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFine($id)
    {
        $user = $this->userFacade->getUser($id);

        $this->userFacade->removeFine($id);

        return redirect()->route('admin.users.index')
            ->with('success', 'Fine removed from user successfully.');
    }
}
