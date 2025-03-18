<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserApplyFineRequest;
use App\Repositories\Contracts\ICommonRepository;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService, protected ICommonRepository $commonRepository)
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
        $users = $this->commonRepository->getAllUsers();

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
        $user = $this->userService->getUser($id);

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

        $data = $request->only(['name', 'email', 'phone', 'role', 'status']);

        if ($request->has('remove_fine') && $request->input('remove_fine')) {
            $data['fine_until'] = null;
        }

        $this->userService->updateProfile($id, $data, $request->file('avatar'));

        return redirect()->route('admin.users.index')
            ->with('success', 'Foydalanuvchi ma\'lumoti yangilandi.');
    }

    /**
     * Show the form for applying a fine to a user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showApplyFineForm($id)
    {
        $user = $this->userService->getUser($id);

        return view('admin.users.apply-fine', compact('user'));
    }

    /**
     * Apply a fine to the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applyFine(UserApplyFineRequest $request, $id)
    {
        $request->validated();

        $this->userService->applyManualFine(
            $id,
            $request->input('reason'),
            $request->input('duration_days')
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'Foydalanuvchiga jarima qo\'llandi.');
    }

    /**
     * Remove a fine from the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFine($id)
    {
        $this->userService->removeFine($id);

        return redirect()->route('admin.users.index')
            ->with('success', 'Foydalanuvchining jarimasi bekor qilindi.');
    }
}
