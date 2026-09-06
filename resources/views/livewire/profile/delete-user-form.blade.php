<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Xóa tài khoản
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Sau khi tài khoản bị xóa, toàn bộ dữ liệu và tài nguyên sẽ bị xóa vĩnh viễn. Trước khi xóa, vui lòng tải xuống bất kỳ dữ liệu nào bạn muốn lưu lại.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Xóa tài khoản</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                Bạn có chắc muốn xóa tài khoản không?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Sau khi xóa, toàn bộ dữ liệu sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu để xác nhận.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Mật khẩu" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Mật khẩu"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Hủy
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Xóa tài khoản
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
