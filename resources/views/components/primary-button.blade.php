<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-10 items-center justify-center rounded-md border border-transparent bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 active:bg-gray-950 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white dark:focus:ring-gray-100 dark:focus:ring-offset-gray-900']) }}>
    {{ $slot }}
</button>
