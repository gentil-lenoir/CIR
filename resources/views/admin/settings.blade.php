<x-admin-layout
    title="Admin Settings"
    eyebrow="Admin Account"
    heading="Settings"
    subheading="Choose the dashboard experience that suits your admin workflow."
>
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <section>
            <article class="admin-dashboard-surface p-7">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-emerald-600">Appearance</p>
                <h3 class="mt-3 text-2xl font-black text-slate-900">Dashboard theme preference</h3>
                <p class="mt-2 text-sm text-slate-500">Save the visual mode you want the admin workspace to remember.</p>

                <div class="mt-6">
                    <label class="admin-form-label" for="theme_preference">Interface Theme</label>
                    <select class="admin-form-input" id="theme_preference" name="theme_preference">
                        <option value="light" @selected(old('theme_preference', $admin->theme_preference ?? 'light') === 'light')>Light</option>
                        <option value="dark" @selected(old('theme_preference', $admin->theme_preference ?? 'light') === 'dark')>Dark</option>
                    </select>
                </div>
            </article>
        </section>

        <section class="admin-dashboard-surface flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xl font-black text-slate-900">Save admin settings</h3>
                <p class="mt-2 text-sm text-slate-500">Your choices are stored on the municipal manager account for future sessions.</p>
            </div>
            <button class="admin-dashboard-btn" type="submit">Save Settings</button>
        </section>
    </form>
</x-admin-layout>

@push('head')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeSelect = document.getElementById('theme_preference');

            themeSelect?.addEventListener('change', () => {
                document.body.dataset.adminTheme = themeSelect.value;
            });
        });
    </script>
@endpush
